<?php
namespace ultimate\system\exporter;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exporter\AbstractExporter;
use wcf\system\importer\ImportHandler;
use wcf\system\WCF;

/**
 * Exporter for Wordpress 3.x
 * 
 * @author		Marcel Werk, Jim Martens
 * @copyright	2001-2013 WoltLab GmbH, 2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.exporter
 * @category	Ultimate CMS
 */
class Wordpress3xExporter extends AbstractExporter {
	/**
	 * category cache
	 * @var	array
	 */
	protected $categoryCache = array();
	
	/**
	 * mapping from old to new IDs
	 * @var integer[]
	 */
	protected $newCategoryIDs = array();
	
	/**
	 * @see	\wcf\system\exporter\AbstractExporter::$methods
	 */
	protected $methods = array(
		'com.woltlab.wcf.user' => 'Users',
		'de.plugins-zum-selberbauen.ultimate.category' => 'Categories',
		'de.plugins-zum-selberbauen.ultimate.content' => 'Contents',
		'de.plugins-zum-selberbauen.ultimate.content.comment' => 'ContentComments'
	);
	
	/**
	 * @see	\wcf\system\exporter\IExporter::getSupportedData()
	 */
	public function getSupportedData() {
		return array(
			'com.woltlab.wcf.user' => array(
			),
			'de.plugins-zum-selberbauen.ultimate.content' => array(
				'de.plugins-zum-selberbauen.ultimate.category',
				'de.plugins-zum-selberbauen.ultimate.content.comment'
			)
		);
	}
	
	/**
	 * @see	\wcf\system\exporter\IExporter::getQueue()
	 */
	public function getQueue() {
		$queue = array();
		
		// user
		if (in_array('com.woltlab.wcf.user', $this->selectedData)) {
			$queue[] = 'com.woltlab.wcf.user';
		}
		
		// CMS
		if (in_array('de.plugins-zum-selberbauen.ultimate.content', $this->selectedData)) {
			if (in_array('de.plugins-zum-selberbauen.ultimate.category', $this->selectedData)) $queue[] = 'de.plugins-zum-selberbauen.ultimate.category';
			$queue[] = 'de.plugins-zum-selberbauen.ultimate.content';
			if (in_array('de.plugins-zum-selberbauen.ultimate.content.comment', $this->selectedData)) $queue[] = 'de.plugins-zum-selberbauen.ultimate.content.comment';
// 			if (in_array('de.plugins-zum-selberbauen.ultimate.page', $this->selectedData)) $queue[] = 'de.plugins-zum-selberbauen.ultimate.page';
		}
		
		return $queue;
	}
	
	/**
	 * @see	\wcf\system\exporter\IExporter::validateFileAccess()
	 */
	public function validateFileAccess() {
		return true;
	}
	
	/**
	 * @see	\wcf\system\exporter\IExporter::getDefaultDatabasePrefix()
	 */
	public function getDefaultDatabasePrefix() {
		return 'wp_';
	}
	
	/**
	 * Counts users.
	 */
	public function countUsers() {
		$sql = 'SELECT COUNT(*) AS count
		        FROM   '.$this->databasePrefix.'users';
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchArray();
		return $row['count'];
	}
	
	/**
	 * Exports users.
	 */
	public function exportUsers($offset, $limit) {
		// prepare password update
		$sql = 'UPDATE wcf'.WCF_N.'_user
		        SET    password = ?
		        WHERE  userID   = ?';
		$passwordUpdateStatement = WCF::getDB()->prepareStatement($sql);
		
		// get users
		$sql = 'SELECT   *
		        FROM     '.$this->databasePrefix.'users
		        ORDER BY ID';
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute();
		
		while ($row = $statement->fetchArray()) {
			$data = array(
				'username' => $row['user_login'],
				'password' => '',
				'email' => $row['user_email'],
				'registrationDate' => @strtotime($row['user_registered'])
			);
			
			// import user
			$newUserID = ImportHandler::getInstance()->getImporter('com.woltlab.wcf.user')->import($row['ID'], $data);
			
			// update password hash
			if ($newUserID) {
				//$passwordUpdateStatement->execute(array($row['user_pass'], $newUserID));
			}
		}
	}
	
	/**
	 * Counts categories.
	 */
	public function countCategories() {
		$sql = 'SELECT COUNT(*) AS count
		        FROM   '.$this->databasePrefix.'term_taxonomy
		        WHERE  taxonomy = ?';
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array('category'));
		$row = $statement->fetchArray();
		return ($row['count'] ? 1 : 0);
	}
	
	/**
	 * Exports categories.
	 */
	public function exportCategories($offset, $limit) {
		$sql = 'SELECT    term_taxonomy.*, term.name, term.slug
		        FROM      '.$this->databasePrefix.'term_taxonomy term_taxonomy
		        LEFT JOIN '.$this->databasePrefix.'terms term
		        ON        (term.term_id = term_taxonomy.term_id)
		        WHERE     term_taxonomy.taxonomy = ?
		        AND       term.slug NOT IN(?, ?)
		        ORDER BY  term_taxonomy.parent, term_taxonomy.term_id';
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array('category', 'uncategorized', 'pages'));
		while ($row = $statement->fetchArray()) {
			$this->categoryCache[$row['parent']][] = $row;
		}
		
		$this->exportCategoriesRecursively();
	}
	
	/**
	 * Exports the categories recursively.
	 */
	protected function exportCategoriesRecursively($parentID = 0) {
		if (!isset($this->categoryCache[$parentID])) return;
		
		foreach ($this->categoryCache[$parentID] as $category) {
			$this->newCategoryIDs[$category['term_id']] = ImportHandler::getInstance()->getImporter('de.plugins-zum-selberbauen.ultimate.category')->import($category['term_id'], array(
				'categoryTitle' => $category['name'],
				'categoryParent' => $category['parent'],
				'categorySlug' => $category['slug']
// 				'showOrder' => 0
			));
			
			$this->exportCategoriesRecursively($category['term_id']);
		}
	}
	
	/**
	 * Counts contents.
	 */
	public function countContents() {
		$sql = 'SELECT COUNT(*) AS count
		       FROM    '.$this->databasePrefix.'posts
		       WHERE   post_type = ?';
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array('post'));
		$row = $statement->fetchArray();
		return $row['count'];
	}
	
	/**
	 * Exports contents.
	 */
	public function exportContents($offset, $limit) {
		// get content ids
		$contentIDs = array();
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('post_type IN (?)', array(array('page', 'post')));
		$sql = 'SELECT   ID
		        FROM     '.$this->databasePrefix.'posts
		        '.$conditionBuilder.'
		        ORDER BY ID';
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute($conditionBuilder->getParameters());
		while ($row = $statement->fetchArray()) {
			$contentIDs[] = $row['ID'];
		}
		
		// get tags
		$tags = array();
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id');
		$conditionBuilder->add('term_relationships.object_id IN (?)', array($contentIDs));
		$conditionBuilder->add('term_taxonomy.taxonomy = ?', array('post_tag'));
		$conditionBuilder->add('term.term_id IS NOT NULL');
		$sql = 'SELECT    term.name, term_relationships.object_id
		        FROM      '.$this->databasePrefix.'term_relationships term_relationships,
		                  '.$this->databasePrefix.'term_taxonomy term_taxonomy
		        LEFT JOIN '.$this->databasePrefix.'terms term
		        ON        (term.term_id = term_taxonomy.term_id)
		        '.$conditionBuilder;
		$statement = $this->database->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		while ($row = $statement->fetchArray()) {
			if (!isset($tags[$row['object_id']])) $tags[$row['object_id']] = array();
			$tags[$row['object_id']][] = $row['name'];
		}
		
		// get categories
		$categories = array();
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id');
		$conditionBuilder->add('term_relationships.object_id IN (?)', array($contentIDs));
		$conditionBuilder->add('term_taxonomy.taxonomy = ?', array('post_tag'));
		$sql = 'SELECT  term_taxonomy.term_id, term_relationships.object_id
		        FROM    '.$this->databasePrefix.'term_relationships term_relationships,
		                '.$this->databasePrefix.'term_taxonomy term_taxonomy
		                '.$conditionBuilder;
		$statement = $this->database->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		while ($row = $statement->fetchArray()) {
			if (!isset($categories[$row['object_id']])) $categories[$row['object_id']] = array();
			$categories[$row['object_id']][] = (
				isset($this->newCategoryIDs[$row['term_id']])
				? $this->newCategoryIDs[$row['term_id']]
				: ImportHandler::getInstance()->getNewID('de.plugins-zum-selberbauen.ultimate.category', $row['term_id'])
			);
		}
		
		// get contents
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('post.ID IN (?)', array($contentIDs));
		
		$sql = 'SELECT	  post.*
		        FROM      '.$this->databasePrefix.'posts post
		        '.$conditionBuilder;
		$statement = $this->database->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		while ($row = $statement->fetchArray()) {
			$additionalData = array();
			if (isset($tags[$row['ID']])) $additionalData['tags'] = $tags[$row['ID']];
			if (isset($categories[$row['ID']])) $additionalData['categories'] = $categories[$row['ID']];
			
			$contentID = ImportHandler::getInstance()->getImporter('de.plugins-zum-selberbauen.ultimate.content')->import($row['ID'], array(
				'authorID' => ($row['post_author'] ? $row['post_author'] : 0),
				'contentTitle' => $row['post_title'],
				'contentDescription' => $row['post_excerpt'],
				'contentText' => $row['post_content'],
				'contentSlug' => ($row['post_type'] == 'page' ? $row['post_name'].'-page' : $row['post_name']),
				'publishDate' => @strtotime($row['post_date_gmt']),
				'lastModified' => @strtotime($row['post_modified_gmt']),
				'enableSmilies' => 0,
				'enableHtml' => 1,
				'enableBBCodes' => 0,
				'status' => $this->getStatus($row['post_status']),
				'visibility' => ($row['post_status'] == 'private' ? 'private' : 'public')
			), $additionalData);
			
			if ($row['post_type'] == 'page') {
				$additionalData['contentID'] = $contentID;
				ImportHandler::getInstance()->getImporter('de.plugins-zum-selberbauen.ultimate.page')->import($row['ID'], array(
					'authorID' => ($row['post_author'] ?: null),
					'pageParent' => $row['post_parent'],
					'pageTitle' => $row['post_title'],
					'pageSlug' => $row['post_name'],
					'publishDate' => @strtotime($row['post_date_gmt']),
					'lastModified' => @strtotime($row['post_modified_gmt']),
					'status' => $this->getStatus($row['post_status']),
					'visibility' => ($row['post_status'] == 'private' ? 'private' : 'public')
				), $additionalData);
			}
		}
	}
	
	/**
	 * Counts content comments.
	 */
	public function countContentComments() {
		$sql = 'SELECT  COUNT(*) AS count
		       FROM     '.$this->databasePrefix.'comments';
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchArray();
		return $row['count'];
	}
	
	/**
	 * Exports content comments.
	 */
	public function exportContentComments($offset, $limit) {
		$sql = 'SELECT comment_ID, comment_parent
		        FROM   '.$this->databasePrefix.'comments
		        WHERE  comment_ID = ?';
		$parentCommentStatement = $this->database->prepareStatement($sql, $limit, $offset);
		
		$sql = 'SELECT    *
		        FROM      '.$this->databasePrefix.'comments
		        ORDER BY  comment_parent, comment_ID';
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute();
		while ($row = $statement->fetchArray()) {
			if (!$row['comment_parent']) {
				ImportHandler::getInstance()->getImporter('de.plugins-zum-selberbauen.ultimate.content.comment')->import($row['comment_ID'], array(
					'objectID' => $row['comment_post_ID'],
					'userID' => ($row['user_id'] ?: null),
					'username' => $row['comment_author'],
					'message' => $row['comment_content'],
					'time' => @strtotime($row['comment_date_gmt'])
				));
			}
			else {
				$parentID = $row['comment_parent'];
				
				do {
					$parentCommentStatement->execute(array($parentID));
					$row2 = $parentCommentStatement->fetchArray();
					
					if (!$row2['comment_parent']) {
						ImportHandler::getInstance()->getImporter('de.plugins-zum-selberbauen.ultimate.content.comment.response')->import($row['comment_ID'], array(
							'commentID' => $row2['comment_ID'],
							'userID' => ($row['user_id'] ?: null),
							'username' => $row['comment_author'],
							'message' => $row['comment_content'],
							'time' => @strtotime($row['comment_date_gmt'])
						));
						break;
					}
					$parentID = $row2['comment_parent'];
				}
				while (true);
			}
		}
	}
	
	/**
	 * Returns the status code for a given status text.
	 * 
	 * @param	string	$status
	 * 
	 * @return	integer
	 */
	private function getStatus($status) {
		$statusCode = 0;
		switch ($status) {
			case 'publish':
				$statusCode = 3;
				break;
			case 'future':
				$statusCode = 2;
				break;
			case 'pending':
				$statusCode = 1;
				break;
			case 'draft':
				$statusCode = 0;
				break;
			default:
				$statusCode = 0;
				break;
		}
		return $statusCode;
	}
}