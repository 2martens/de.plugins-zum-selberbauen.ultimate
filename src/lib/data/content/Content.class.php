<?php
namespace ultimate\data\content;
use ultimate\data\AbstractUltimateProcessibleDatabaseObject;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\tagging\ITaggable;
use wcf\system\tagging\ITagged;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Represents a content entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class Content extends AbstractUltimateProcessibleDatabaseObject implements ITaggable {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'content';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'contentID';
	
	/**
	 * @see	\wcf\data\ProcessibleDatabaseObject::$processorInterface
	 */
	protected static $processorInterface = '\wcf\data\IDatabaseObjectProcessor';
	
	/**
	 * Contains the content to category database table name.
	 * @var	string
	 */
	protected $contentCategoryTable = 'content_to_category';
	
	/**
	 * Contains the categories to which this content belongs.
	 * @var	\ultimate\data\category\Category[]
	 */
	public $categories = array();
	
	/**
	 * Returns the categories associated with this content.
	 * 
	 * @return	\ultimate\data\category\Category[]
	 */
	public function getCategories() {
		$sql = 'SELECT    category.*
		        FROM      ultimate'.ULTIMATE_N.'_'.$this->contentCategoryTable.' contentToCategory
		        LEFT JOIN ultimate'.ULTIMATE_N.'_category category
		        ON        (category.categoryID = contentToCategory.categoryID)
		        WHERE     contentToCategory.contentID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->contentID));
		$categories = array();
		while ($category = $statement->fetchObject('\ultimate\data\category\Category')) {
			$categories[$category->__get('categoryID')] = $category;
		}
		return $categories;
	}
	
	/**
	 * Returns all user groups associated with this content.
	 * 
	 * @return	\wcf\data\user\group\UserGroup[]
	 */
	public function getGroups() {
		$sql = 'SELECT	  group.*
		        FROM      ultimate'.ULTIMATE_N.'_user_group_to_content groupToContent
		        LEFT JOIN wcf'.WCF_N.'_user_group group
		        ON        (group.groupID = groupToContent.groupID)
		        WHERE     groupToContent.contentID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->contentID));
		
		$groups = array();
		while ($group = $statement->fetchObject('\wcf\data\user\group\UserGroup')) {
			$groups[$group->groupID] = $group;
		}
		return $groups;
	}
	
	/**
	 * Returns the tags of this content.
	 * 
	 * @return array[]
	 */
	public function getTags() {
		$languages = WCF::getLanguage()->getLanguages();
		$tags = array();
		foreach ($languages as $languageID => $language) {
			/* @var $language \wcf\data\language\Language */
			$tags[$languageID] = TagEngine::getInstance()->getObjectTags(
				'de.plugins-zum-selberbauen.ultimate.contentTaggable',
				$this->contentID,
				$languageID
			);
		}
		return $tags;
	}
	
	/**
	 * Returns the title of this content.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->get($this->contentTitle);
	}
	
	/**
	 * @see \wcf\system\tagging\ITaggable::getObjectTypeID()
	 */
	public function getObjectTypeID() {
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.tagging.taggableObject', 'de.plugins-zum-selberbauen.ultimate.contentTaggable');
		return $objectType->__get('objectTypeID');
	}
	
	/**
	 * @see	\wcf\data\DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		$data['contentID'] = intval($data['contentID']);
		$data['authorID'] = intval($data['authorID']);
		$data['author'] = new User($data['authorID']);
		$data['enableSmilies'] = (boolean) intval($data['enableSmilies']);
		$data['enableHtml'] = (boolean) intval($data['enableHtml']);
		$data['enableBBCodes'] = (boolean) intval($data['enableBBCodes']);
		$data['publishDate'] = intval($data['publishDate']);
		$data['publishDateObject'] = DateUtil::getDateTimeByTimestamp($data['publishDate']);
		$data['lastModified'] = intval($data['lastModified']);
		$data['status'] = intval($data['status']);
		parent::handleData($data);
		$this->data['tags'] = $this->getTags();
		$this->data['groups'] = $this->getGroups();
	}
}
