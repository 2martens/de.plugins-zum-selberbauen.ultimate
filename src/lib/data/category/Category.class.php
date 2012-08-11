<?php
namespace ultimate\data\category;
use ultimate\data\content\Content;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\data\ITitledDatabaseObject;
use wcf\system\WCF;

/**
 * Represents a category entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.category
 * @category	Ultimate CMS
 */
class Category extends AbstractUltimateDatabaseObject implements ITitledDatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'category';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'categoryID';
	
	/**
	 * Contains the content to category database table name.
	 * @var	string
	 */
	protected $contentCategoryTable = 'content_to_category';
	
	/**
	 * Contains all contents in this category.
	 * @var	\ultimate\data\content\Content[]
	 */
	public $contents = array();
	
	/**
	 * Contains all child categories of this category.
	 * @var	\ultimate\data\category\Category[]
	 */
	public $childCategories = array();
	
	/**
	 * Returns all contents in this category.
	 * 
	 * @return	\ultimate\data\content\Content[]
	 */
	public function getContents() {
		$sql = 'SELECT contentID
		        FROM   ultimate'.ULTIMATE_N.'_'.$this->contentCategoryTable.'
		        WHERE  categoryID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->categoryID));
		$contents = array();
		while ($row = $statement->fetchArray()) {
			$contents[$row['contentID']] = new Content($row['contentID']);
		}
		return $contents;
	}
	
	/**
	 * Returns the title of this category (without language interpreting).
	 * 
	 * To use language interpreting, use magic toString method.
	 * 
	 * @return	string
	 */
	public function getTitle() {
		return $this->categoryTitle;
	}
	
	/**
	 * Returns all child categories of this category.
	 * 
	 * @return	\ultimate\data\category\Category[]
	 */
	public function getChildCategories() {
		$sql = 'SELECT categoryID
		        FROM   ultimate'.ULTIMATE_N.'_'.self::$databaseTableName.'
		        WHERE  categoryParent = ?';
		/* @var $statement \wcf\system\database\statement\PreparedStatement */
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->categoryID));
		$categories = array();
		while ($row = $statement->fetchArray()) {
			$categories[$row['categoryID']] = new Category($row['categoryID']);
			$categories[$row['categoryID']]->childCategories = $categories[$row['categoryID']]->getChildCategories();
		}
		return $categories;
	}
	
	/**
	 * Returns the title of this category.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->get($this->categoryTitle);
	}

	/**
	 * @see	\wcf\data\DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		$data['categoryID'] = intval($data['categoryID']);
		$data['categoryParent'] = intval($data['categoryParent']);
		parent::handleData($data);
	}
}
