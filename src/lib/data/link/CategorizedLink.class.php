<?php
/**
 * Contains the categorized link data model class.
 * 
 * LICENSE:
 * This file is part of the Ultimate CMS.
 *
 * The Ultimate CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * The Ultimate CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.link
 * @category	Ultimate CMS
 */
namespace ultimate\data\link;
use ultimate\data\IUltimateData;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\category\CategoryHandler;
use wcf\system\WCF;

/**
 * Represents a categorized link.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.link
 * @category	Ultimate CMS
 * 
 * @property-read	\wcf\data\category\Category[]	$categories	(categoryID => category)
 */
class CategorizedLink extends DatabaseObjectDecorator implements IUltimateData {
	/**
	 * The base class.
	 * @var	string
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
	 * Returns the value of a object data variable with the given name.
	 * 
	 * @param	string	$name
	 * @return	mixed
	 */
	public function __get($name) {
		$value = parent::__get($name);
		
		if ($value === null && $name == 'categories') {
			$value = $this->categories;
		}
		
		return $value;
	}
	
	/**
	 * Reads the categories and returns them.
	 * 
	 * @return \wcf\data\category\Category[]
	 */
	protected function getCategories() {
		$sql = 'SELECT categoryID
		        FROM   ultimate'.WCF_N.'_link_to_category
		        WHERE  linkID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->__get('linkID')));
		
		$categoryIDs = array();
		while ($row = $statement->fetchArray()) {
			$categoryIDs[] = intval($row['categoryID']);
		}
		
		// get all link categories
		$categories = CategoryHandler::getInstance()->getCategories('de.plugins-zum-selberbauen.ultimate.linkCategory');
		
		// remove all categories which are not associated with this link
		foreach ($categories as $categoryID => $category) {
			if (in_array($categoryID, $categoryIDs)) continue;
			unset($categories[$categoryID]);
		}
		
		return $categories;
	}
}
