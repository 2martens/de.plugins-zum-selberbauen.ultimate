<?php
/**
 * Contains the MenuItemCacheBuilder class.
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
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\menu\item\MenuItemList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the menu items.
 * 
 * Provides three variables:
 * * \ultimate\data\menu\item\MenuItem[][] menuItems (menuID => (menuItemID => menuItem))
 * * integer[][] menuItemIDs (menuID => ( => menuItemID))
 * * \ultimate\data\menu\item\MenuItem[][] menuItemsToParent (menuID => (menuItemName => menuItem))
 * 
 * In all of these variables the menu items are sorted ASC for their parentMenuitem and the showOrder.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class MenuItemCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 * 
	 * @param	array	$parameters
	 * 
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'menuItems' => array(),
			'menuItemIDs' => array(),
			'menuItemsToParent' => array()
		);
		
		$menuItemList = new MenuItemList();
		$menuItemList->sqlOrderBy = 'menu_item.menuItemParent ASC, menu_item.showOrder ASC';
		$menuItemList->readObjects();
		$menuItems = $menuItemList->getObjects();
		
		foreach ($menuItems as $menuItemID => $menuItem) {
			if (!isset($data['menuItems'][$menuItem->__get('menuID')])) {
				$data['menuItems'][$menuItem->__get('menuID')] = array();
			}
			if (!isset($data['menuItemIDs'][$menuItem->__get('menuID')])) {
				$data['menuItemIDs'][$menuItem->__get('menuID')] = array();
			}
			if (!isset($data['menuItemsToParent'][$menuItem->__get('menuID')])) {
				$data['menuItemsToParent'][$menuItem->__get('menuID')] = array();
			}
			/* @var $menuItem \ultimate\data\menu\item\MenuItem */
			$data['menuItems'][$menuItem->__get('menuID')][$menuItemID] = $menuItem;
			$data['menuItemIDs'][$menuItem->__get('menuID')][] = $menuItemID;
			$data['menuItemsToParent'][$menuItem->__get('menuID')][$menuItem->__get('menuItemName')] = array();
			foreach ($menuItems as $__menuItemID => $__menuItem) {
				if ($__menuItem->__get('menuItemParent') == $menuItem->__get('menuItemName')) {
					$data['menuItemsToParent'][$menuItem->__get('menuID')][$menuItem->__get('menuItemName')][$__menuItemID] = $__menuItem;
				}
			}
		}
		
		foreach ($data['menuItems'] as $menuID => $__menuItems) {
			if (!isset($data['menuItemsToParent'][$menuID])) {
				$data['menuItemsToParent'][$menuID] = array();
			}
			$data['menuItemsToParent'][$menuID][''] = array();
			
			foreach ($__menuItems as $menuItemID => $menuItem) {
				if ($menuItem->__get('menuItemParent') != '') continue;
				$data['menuItemsToParent'][$menuID][''][$menuItemID] = $menuItem;
			}
		}
		return $data;
	}
}
