<?php
/**
 * Contains the category data model class.
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.category
 * @category	Ultimate CMS
 */
namespace ultimate\data\category;
use ultimate\data\content\Content;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;

/**
 * Represents a category entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.category
 * @category	Ultimate CMS
 * 
 * @property-read	integer								$categoryID
 * @property-read	integer								$categoryParent
 * @property-read	string								$categoryTitle
 * @property-read	string								$categoryDescription
 * @property-read	string								$categorySlug
 * @property-read	\ultimate\data\category\Category[]	$childCategories (categoryID => category)
 * @property-read	\ultimate\data\content\Content[]	$contents (contentID => content)
 * @property-read	string[]							$metaData ('metaDescription' => metaDescription, 'metaKeywords' => metaKeywords)
 */
class Category extends AbstractUltimateDatabaseObject implements ITitledObject {
	/**
	 * Contains the category ID of the page category.
	 * @var integer
	 */
	const PAGE_CATEGORY = 2;
	
	/**
	 * The database table name.
	 * @var string
	 */
	protected static $databaseTableName = 'category';
	
	/**
	 * If true, the database table index is used as identity.
	 * @var	boolean
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * The database table index name.
	 * @var	string
	 */
	protected static $databaseTableIndexName = 'categoryID';
	
	/**
	 * Contains the content to category database table name.
	 * @var	string
	 */
	protected $contentCategoryTable = 'content_to_category';
	
	/**
	 * Returns the title of this category (without language interpreting).
	 * 
	 * To use language interpreting, use getLangTitle method.
	 * 
	 * @return	string
	 */
	public function getTitle() {
		return $this->categoryTitle;
	}
	
	/**
	 * Returns the title of this category.
	 * 
	 * @return	string
	 */
	public function getLangTitle() {
		return WCF::getLanguage()->get($this->categoryTitle);
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
	 * Returns all child categories of this category.
	 *
	 * @return	\ultimate\data\category\Category[]
	 */
	protected function getChildCategories() {
		$sql = 'SELECT *
		        FROM   ultimate'.WCF_N.'_'.self::$databaseTableName.'
		        WHERE  categoryParent = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->categoryID));
		$categories = array();
		while ($category = $statement->fetchObject(get_class($this))) {
			$categories[$category->categoryID] = $category;
		}
		return $categories;
	}
	
	/**
	 * Returns all contents in this category.
	 *
	 * @return	\ultimate\data\content\Content[]
	 */
	protected function getContents() {
		$sql = 'SELECT    content.*
		        FROM      ultimate'.WCF_N.'_'.$this->contentCategoryTable.' contentToCategory
		        LEFT JOIN ultimate'.WCF_N.'_content content
		        ON        (content.contentID = contentToCategory.contentID)
		        WHERE     contentToCategory.categoryID = ?
		        ORDER BY '.ULTIMATE_SORT_CONTENT_SORTFIELD.' '.ULTIMATE_SORT_CONTENT_SORTORDER;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->categoryID));
		$contents = array();
		while ($content = $statement->fetchObject('\ultimate\data\content\Content')) {
			$contents[$content->__get('contentID')] = $content;
		}
		return $contents;
	}

	/**
	 * Handles data.
	 * 
	 * @param	array	$data
	 */
	protected function handleData($data) {
		$data['categoryID'] = intval($data['categoryID']);
		$data['categoryParent'] = intval($data['categoryParent']);
		parent::handleData($data);
		$this->data['childCategories'] = $this->getChildCategories();
		$this->data['contents'] = $this->getContents();
		$this->data['metaData'] = $this->getMetaData($this->categoryID, 'category');
	}
}
