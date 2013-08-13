<?php
/**
 * Contains the MenuItemHandler class.
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
 * @subpackage	system.menu.item
 * @category	Ultimate CMS
 */
namespace ultimate\system\menu\item;
use ultimate\data\menu\item\MenuItem;
use ultimate\system\cache\builder\MenuItemCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Handles menu items.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.menu.item
 * @category	Ultimate CMS
 */
class MenuItemHandler extends SingletonFactory {
	/**
	 * The cached menu items.
	 * @var	\ultimate\data\menu\item\MenuItem[][]
	 */
	protected $menuItems = array();

	/**
	 * Returns all menu item objects.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	integer	$menuID
	 * @return	\ultimate\data\menu\item\MenuItem[]
	*/
	public function getMenuItems($menuID) {
		return $this->menuItems[intval($menuID)];
	}
	
	/**
	 * Returns the menu item object with the given menu item id or null if there is no such object.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	integer	$menuItemID
	 * @return	\ultimate\data\menu\item\MenuItem|null
	 */
	public function getMenuItem($menuItemID) {
		foreach ($this->menuItems as $menuItems) {
			if (isset($menuItems[$menuItemID])) {
				return $menuItems[$menuItemID];
			}
		}
		
		return null;
	}
	
	/**
	 * Returns the child menu items of the given menu item.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	\ultimate\data\menu\item\MenuItem	$menuItem
	 * @return	\ultimate\data\menu\item\MenuItem[]
	 */
	public function getChildMenuItems(MenuItem $menuItem) {
		$menuItems = array();
		
		foreach ($this->menuItems as $__menuItem) {
			if ($__menuItem->__get('menuItemParent') == $menuItem->__get('menuItemName') /*&& $menuItem->__get('menuItemID') */ && $__menuItem->__get('menuID') == $menuItem->__get('menuID')) {
				$menuItems[$__menuItem->__get('menuItemID')] = $__menuItem;
			}
		}
		
		return $menuItems;
	}
	
	/**
	 * Initializes the MenuItemHandler.
	 */
	protected function init() {
		$this->menuItems = MenuItemCacheBuilder::getInstance()->getData(array(), 'menuItems');
	}
	
	/**
	 * Reloads the menuItem cache.
	 * 
	 * {@internal Calls the init method.}}
	 */
	public function reloadCache() {
		MenuItemCacheBuilder::getInstance()->reset();
		
		$this->init();
	}
}
