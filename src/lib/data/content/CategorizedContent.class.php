<?php
/**
 * Contains the categorized content data model class.
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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\WCF;

/**
 * Represents a categorized content.
 * In addition to Content it offers (without ') 'categories'.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
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
	 * @var \ultimate\data\category\Category[]
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
		$value = parent::__get($name);
		
		if ($value === null) {
			if ($name == 'categories') $value = $this->categories;
		}
		return $value;
	}
	
	/**
	 * Returns the categories associated with this content.
	 * 
	 * @return	\ultimate\data\category\Category[]
	 */
	protected function getCategories() {
		$sql = 'SELECT    category.*
		        FROM      ultimate'.WCF_N.'_content_to_category contentToCategory
		        LEFT JOIN ultimate'.WCF_N.'_category category
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
