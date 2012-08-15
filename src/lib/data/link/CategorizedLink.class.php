<?php
namespace ultimate\data\link;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\category\CategoryHandler;
use wcf\system\WCF;

/**
 * Represents a categorized link.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.link
 * @category	Ultimate CMS
 */
class CategorizedLink extends DatabaseObjectDecorator {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = '\ultimate\data\link\Link';
	
	/**
	 * Contains all categories which are associated with this link. 
	 * @var \wcf\data\category\Category[]
	 */
	protected $categories = array();
	
	/**
	 * Creates a new CategorizedLink object.
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
		if ($name == 'categories') {
			return $this->categories;
		}
		
		return parent::__get($name);
	}
	
	/**
	 * Reads the categories and returns them.
	 * 
	 * @return \wcf\data\category\Category[]
	 */
	protected function getCategories() {
		$sql = 'SELECT categoryID
		        FROM   ultimate'.ULTIMATE_N.'_link_to_category
		        WHERE  linkID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->__get('linkID')));
		
		$categoryIDs = array();
		while ($row = $statement->fetchArray()) {
			$categoryIDs[] = intval($row['categoryID']);
		}
		
		// get all link categories
		$categories = CategoryHandler::getCategories('de.plugins-zum-selberbauen.ultimate.linkCategory');
		
		// remove all categories which are not associated with this link
		foreach ($categories as $categoryID => $category) {
			if (in_array($categoryID, $categoryIDs)) continue;
			unset($categories[$categoryID]);
		}
		
		return $categories;
	}
}
