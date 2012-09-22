<?php
/**
 * Contains the link data model class.
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
 * @subpackage	data.link
 * @category	Ultimate CMS
 */
namespace ultimate\data\link;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\CacheHandler;
use wcf\system\WCF;

/**
 * Provides functions to edit links.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.link
 * @category	Ultimate CMS
 */
class LinkEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObjectDecorator.html#$baseClass
	 */
	protected static $baseClass = '\ultimate\data\link\Link';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableObject.html#deleteAll
	 */
	public static function deleteAll(array $objectIDs = array()) {
		// unmark contents
		ClipboardHandler::getInstance()->unmark($objectIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.link'));
		
		// delete language items
		$sql = 'DELETE FROM wcf'.WCF_N.'_language_item
		        WHERE       languageItem = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		
		WCF::getDB()->beginTransaction();
		foreach ($objectIDs as $objectID) {
			$statement->executeUnbuffered(array('ultimate.link.'.$objectID.'.%'));
		}
		WCF::getDB()->commitTransaction();
		return parent::deleteAll($objectIDs);
	}
	
	/**
	 * Adds the content to the specified categories.
	 *
	 * @param	array	$categoryIDs
	 * @param	boolean	$deleteOldCategories
	 */
	public function addToCategories(array $categoryIDs, $deleteOldCategories = true) {
		// remove old categores
		if ($deleteOldCategories) {
			$sql = "DELETE FROM	ultimate".ULTIMATE_N."_link_to_category
			        WHERE       linkID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$this->__get('linkID')
			));
		}
		
		// insert new categories
		if (!empty($categoryIDs)) {
			$sql = "INSERT INTO	ultimate".ULTIMATE_N."_link_to_category
			               (linkID, categoryID)
			        VALUES (?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			WCF::getDB()->beginTransaction();
			foreach ($categoryIDs as $categoryID) {
				$statement->executeUnbuffered(array(
					$this->__get('linkID'),
					$categoryID
				));
			}
			WCF::getDB()->commitTransaction();
		}
	}
	
	/**
	 * Adds the link to the specified category.
	 *
	 * @param	integer	$categoryID
	 */
	public function addToCategory($categoryID) {
		$sql = "SELECT   COUNT(*) AS count
		        FROM     ultimate".ULTIMATE_N."_content_to_category
		        WHERE    contentID  = ?
		        AND      categoryID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->__get('linkID'),
			$categoryID
		));
		$row = $statement->fetchArray();
		
		if (!$row['count']) {
			$this->addToCategories(array($categoryID), false);
		}
	}
	
	/**
	 * Removes the link from the specified category.
	 *
	 * @param	integer	$categoryID
	 */
	public function removeFromCategory($categoryID) {
		$this->removeFromCategories(array($categoryID));
	}
	
	/**
	 * Removes the link from multiple categories.
	 *
	 * @param	array	$categoryIDs
	 */
	public function removeFromCategories(array $categoryIDs) {
		$sql = "DELETE FROM	ultimate".ULTIMATE_N."_link_to_category
		        WHERE       linkID  = ?
		        AND         categoryID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($categoryIDs as $categoryID) {
			$statement->executeUnbuffered(array(
				$this->__get('linkID'),
				$categoryID
			));
		}
		WCF::getDB()->commitTransaction();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableCachedObject.html#resetCache
	 */
	public static function resetCache() {
		CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.link.php');
		CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.link-to-category.php');
	}
}
