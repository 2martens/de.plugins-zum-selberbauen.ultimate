<?php
/**
 * Contains the category data model editor class.
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
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.category
 * @category	Ultimate CMS
 */
namespace ultimate\data\category;
use ultimate\data\category\language\CategoryLanguageEntryCache;
use ultimate\data\layout\LayoutAction;
use ultimate\system\cache\builder\CategoryCacheBuilder;
use ultimate\system\cache\builder\LayoutCacheBuilder;
use ultimate\system\layout\LayoutHandler;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Provides functions to edit categories.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.category
 * @category	Ultimate CMS
 */
class CategoryEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * The base class.
	 * @var	string
	 */
	protected static $baseClass = '\ultimate\data\category\Category';
	
	/**
	 * Creates an object with the given parameters.
	 * 
	 * @param	array	$parameters	
	 */
	public static function create(array $parameters = array()) {
		$category = parent::create($parameters);
		$parameters = array(
			'data' => array(
				'objectID' => $category->__get('categoryID'),
				'objectType' => 'category'
			)
		);
		$layoutAction = new LayoutAction(array(), 'create', $parameters);
		$layoutAction->executeAction();
		return $category;
	}
	
	
	/**
	 * Deletes all corresponding objects to the given object IDs.
	 * 
	 * @param	integer[]	$objectIDs
	 */
	public static function deleteAll(array $objectIDs = array()) {
		// unmark contents
		ClipboardHandler::getInstance()->unmark($objectIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.category'));
		
		// delete meta data
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('objectID IN (?)', array($objectIDs));
		$conditionBuilder->add('objectType = ?', array('content'));
		$sql = 'DELETE FROM ultimate'.WCF_N.'_meta
			    '.$conditionBuilder->__toString();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		
		return parent::deleteAll($objectIDs);
	}
	
	/**
	 * Deletes this object.
	 */
	public function delete() {
		/* @var $layout \ultimate\data\layout\Layout */
		$layout = LayoutHandler::getInstance()->getLayoutFromObjectData($this->__get('categoryID'), 'category');
		$layoutAction = new LayoutAction(array($layout->__get('layoutID')), 'delete', array());
		$layoutAction->executeAction();
		parent::delete();
	}
	
	/**
	 * Adds meta data for this category.
	 *
	 * @param	string	$metaDescription
	 * @param	string	$metaKeywords
	 */
	public function addMetaData($metaDescription, $metaKeywords) {
		$metaDescription = StringUtil::trim($metaDescription);
		$metaKeywords = StringUtil::trim($metaKeywords);
		if (empty($metaDescription) && empty($metaKeywords)) {
			return;
		}
		$sql = 'REPLACE INTO ultimate'.WCF_N.'_meta
		               (objectID, objectType, metaDescription, metaKeywords)
		        VALUES (?, ?, ?, ?)';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->object->__get('categoryID'),
			'category',
			$metaDescription,
			$metaKeywords
		));
	}
	
	/**
	 * Resets the cache.
	 */
	public static function resetCache() {
		CategoryCacheBuilder::getInstance()->reset();
		CategoryLanguageEntryCache::getInstance()->reloadCache();
		LayoutCacheBuilder::getInstance()->reset();
	}
}
