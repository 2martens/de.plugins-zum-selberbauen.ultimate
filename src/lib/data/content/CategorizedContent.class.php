<?php
namespace ultimate\data\content;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\WCF;

/**
 * Represents a categorized content.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class CategorizedContent extends DatabaseObjectDecorator {
	/**
	 * @see \wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = '\ultimate\data\content\Content';
	
	/**
	 * Contains the categories of this content.
	 * @var array[]
	 */
	protected $categories = array();
	
	/**
	 * Creates a new CategorizedContent object.
	 *
	 * @param \wcf\data\DatabaseObject $object
	 */
	public function __construct(\wcf\data\DatabaseObject $object) {
		parent::__construct($object);
		$this->categories = $this->getCategories();
	}
	
	/**
	 * @see \wcf\data\DatabaseObjectDecorator::__get()
	 */
	public function __get($name) {
		if ($name == 'categories') return $this->categories;
		parent::__get($name);
	}
	
	/**
	 * Returns the categories associated with this content.
	 * 
	 * @return	\ultimate\data\category\Category[]
	 */
	protected function getCategories() {
		$sql = 'SELECT    category.*
		        FROM      ultimate'.ULTIMATE_N.'_content_to_category contentToCategory
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
}
