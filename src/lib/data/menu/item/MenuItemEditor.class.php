<?php
/**
 * Contains the menuItem data model editor class.
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
 * @subpackage	data.menu.item
 * @category	Ultimate CMS
 */
namespace ultimate\data\menu\item;
use ultimate\system\cache\builder\MenuItemCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\WCF;

/**
 * Provides functions to edit menu items.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.menu.item
 * @category	Ultimate CMS
 */
class MenuItemEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * The base class.
	 * @var	string
	 */
	protected static $baseClass = '\ultimate\data\menu\item\MenuItem';
	
	/**
	 * Deletes one or more objects.
	 */
	public function delete() {
		// update show order
		$sql = 'UPDATE	ultimate'.WCF_N.'_menu_item
		        SET     showOrder = showOrder - 1
		        WHERE   showOrder >= ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->showOrder
		));
	
		parent::delete();
	}
	
	/**
	 * Deletes all corresponding objects to the given object IDs.
	 * 
	 * @param	integer[]	$objectIDs
	 */
	public static function deleteAll(array $objectIDs = array()) {
		// unmark contents
		ClipboardHandler::getInstance()->unmark($objectIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.menuItem'));
		
		// deletes language items
		$sql = 'DELETE FROM wcf'.WCF_N.'_language_item
		        WHERE       languageItem = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($objectIDs as $objectID) {
			$statement->executeUnbuffered(array('ultimate.menu.item.'.$objectID.'.%'));
		}
		WCF::getDB()->commitTransaction();
		return parent::deleteAll($objectIDs);
	}
	
	/**
	 * Sets first top menu item as landing page.
	 * 
	 * @param	integer	$menuID
	 */
	public static function updateLandingPage($menuID) {
		$sql = 'UPDATE ultimate'.WCF_N.'_menu_item
		        SET    isLandingPage = 0';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
	
		$sql = 'UPDATE   ultimate'.WCF_N.'_menu_item
		        SET      isLandingPage = ?,
		                 isDisabled = ?
		        WHERE    menuItemParent = ?
		        AND      menuID = ?
		        AND      menuItemController <> ?
		        OR       menuItemController IS NULL
		        ORDER BY showOrder ASC';
		$statement = WCF::getDB()->prepareStatement($sql, 1);
		$statement->execute(array(
			1,
			0,
			'',
			intval($menuID),
			''
		));
	
		self::resetCache();
	}
	
	/**
	 * Updates the position of a menu item directly.
	 *
	 * @param	integer		$menuItemID
	 * @param	integer		$showOrder
	 */
	public static function setShowOrder($menuItemID, $showOrder = 1) {
		// Update
		$sql = 'UPDATE ultimate'.WCF_N.'_menu_item
		        SET    showOrder = ?
		        WHERE  menuItemID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$showOrder,
			$menuItemID
		));
	}
	
	/**
	 * Returns show order for a new menu item.
	 *
	 * @param	integer		$showOrder
	 * @param	integer		$menuID
	 * @param	string		$menuItemParent
	 * @return	integer
	 */
	public static function getShowOrder($showOrder, $menuID, $menuItemParent = '') {
		if ($showOrder == 0) {
			// get next number in row
			$sql = 'SELECT  MAX(showOrder) AS showOrder
			        FROM    ultimate'.WCF_N.'_menu_item
			        WHERE   menuItemParent = ?
			        AND     menuID         = ?';
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$menuItemParent,
				intval($menuID)
			));
			$row = $statement->fetchArray();
			if (!empty($row)) $showOrder = intval($row['showOrder']) + 1;
			else $showOrder = 1;
		}
		else {
			$sql = 'UPDATE  ultimate'.WCF_N.'_menu_item
			        SET     showOrder = showOrder + 1
			        WHERE   menuItemParent = ?
			        AND     menuID         = ?
			        AND     showOrder >= ?';
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$menuItemParent,
				intval($menuID),
				$showOrder
			));
		}
	
		return $showOrder;
	}
	
	/**
	 * Resets the cache.
	 */
	public static function resetCache() {
		MenuItemCacheBuilder::getInstance()->reset();
	}
}
