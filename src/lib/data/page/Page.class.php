<?php
namespace ultimate\data\page;
use ultimate\data\content\Content;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\data\user\group\UserGroup;
use wcf\data\user\User;
use wcf\data\ITitledDatabaseObject;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Represents a page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.page
 * @category	Ultimate CMS
 */
class Page extends AbstractUltimateDatabaseObject implements ITitledDatabaseObject {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableName
	 */
	protected static $databaseTableName = 'page';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'pageID';
	
	/**
	 * Contains the content to page database table name.
	 * @var	string
	 */
	protected $contentPageTable = 'content_to_page';
	
	/**
	 * Returns the content of this page.
	 * 
	 * @return	\ultimate\data\content\Content
	 */
	public function getContent() {
		$sql = 'SELECT	  content.*
		        FROM      ultimate'.ULTIMATE_N.'_'.$this->contentPageTable.' contentToPage
		        LEFT JOIN ultimate'.ULTIMATE_N.'_content content
		        ON        (content.contentID = contentToPage.contentID)
		        WHERE     contentToPage.pageID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->pageID));
		
		$content = $statement->fetchObject('\ultimate\data\content\Content');
		return $content;
	}
	
	/**
	 * Returns the page title without language interpreting.
	 * 
	 * To use language interpreting, use magic toString method.
	 * 
	 * @return	string
	 */
	public function getTitle() {
		return $this->pageTitle;
	}
	
	/**
	 * Returns all child pages of this page.
	 * 
	 * @return	\ultimate\data\page\Page[]
	 */
	public function getChildPages() {
		$sql = 'SELECT	*
		        FROM    '.self::getDatabaseTableName().'
		        WHERE   pageParent = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->pageID));
		
		$childPages = array();
		while ($page = $statement->fetchObject(get_class($this))) {
			$childPages[$page->pageID] = $page;
		}
		return $childPages;
	}
	
	/**
	 * Returns all user groups associated with this page.
	 * 
	 * @return	\wcf\data\user\group\UserGroup[]
	 */
	public function getGroups() {
		$sql = 'SELECT	  group.*
		        FROM      ultimate'.ULTIMATE_N.'_user_group_to_page groupToPage
		        LEFT JOIN wcf'.WCF_N.'_user_group group
		        ON        (group.groupID = groupToPage.groupID)
		        WHERE     groupToPage.pageID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->pageID));
		
		$groups = array();
		while ($group = $statement->fetchObject('\wcf\data\user\group\UserGroup')) {
			$groups[$group->__get('groupID')] = $group;
		}
		return $groups;
	}
	
	/**
	 * Returns the title of this page.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->get($this->pageTitle);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#handleData
	 */
	protected function handleData($data) {
		$data['pageID'] = intval($data['pageID']);
		$data['authorID'] = intval($data['authorID']);
		$data['author'] = new User($data['authorID']);
		$data['publishDate'] = intval($data['publishDate']);
		$data['publishDateObject'] = DateUtil::getDateTimeByTimestamp($data['publishDate']);
		$data['lastModified'] = intval($data['lastModified']);
		$data['status'] = intval($data['status']);
		parent::handleData($data);
		$this->data['groups'] = $this->getGroups();
		$this->data['childPages'] = $this->getChildPages();
	}
}
