<?php
/**
 * Contains the CustomMenu class.
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
 * @subpackage	system.menu.custom
 * @category	Ultimate CMS
 */
namespace ultimate\system\menu\custom;
use ultimate\data\menu\Menu;
use ultimate\system\cache\builder\ContentCacheBuilder;
use ultimate\system\cache\builder\MenuItemCacheBuilder;
use ultimate\system\cache\builder\PageCacheBuilder;
use wcf\system\event\EventHandler;
use wcf\system\menu\ITreeMenuItem;
use wcf\system\menu\TreeMenu;
use wcf\system\WCF;

/**
 * Builds a custom menu.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.menu.custom
 * @category	Ultimate CMS
 */
class CustomMenu extends TreeMenu {
	/**
	 * The menu.
	 * @var	\ultimate\data\menu\Menu
	 */
	protected $menu = null;
	
	/**
	 * Contains all pages.
	 * @var	\ultimate\data\page\Page[]
	 */
	protected $pages = null;
	
	/**
	 * Contains all contents.
	 * @var	\ultimate\data\content\Content[]
	 */
	protected $contents = null;
	
	/**
	 * The current items.
	 * @var	array[]
	 */
	protected $currentMenuItems = array();
	
	/**
	 * Builds the given menu.
	 * 
	 * You have to call this method before using getMenuItems in order to get the menu items for your menu.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	\ultimate\data\menu\Menu	$menu
	 * @return	void 
	 */
	public function buildMenu(Menu $menu) {
		$this->menu = $menu;
		
		// get menu items of the current menu
		$this->readCurrentItems();
		
		// check menu items
		$this->checkMenuItems();
		
		// build plain menu item list
		$this->buildMenuItemList();
	}
	
	/**
	 * Returns the current menu items under the given parent menu item, all current menu items if you give null or null if the given parent menu item doesn't exist.
	 * 
	 * This method should be called inside a template.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @return	\ultimate\data\menu\item\MenuItem[]|array[]|null
	 * @see		\wcf\system\menu\TreeMenu::getMenuItems()
	 */
	public function getMenuItems($parentMenuItem = null) {
		if ($parentMenuItem === null) return $this->currentMenuItems;
		if (isset($this->currentMenuItems[$parentMenuItem])) {
			return $this->currentMenuItems[$parentMenuItem];
		}
		return null;
	}
	
	/**
	 * Initializes the CustomMenu.
	 */
	protected function init() {
		// get menu items from cache
		$this->loadCache();
		
		// call init event
		EventHandler::getInstance()->fireAction($this, 'init');
	}
	
	/**
	 * Loads the cache.
	 */
	protected function loadCache() {
		parent::loadCache();
		
		// get menu item cache
		$this->menuItems = MenuItemCacheBuilder::getInstance()->getData(array(), 'menuItemsToParent');
		
		// get content cache
		$this->contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		
		// get content cache
		$this->pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
	}
	
	/**
	 * Reads current menu items.
	 */
	protected function readCurrentItems($parentMenuItem = '') {
		if (!isset($this->menuItems[$this->menu->__get('menuID')][$parentMenuItem])) return;
		if (!isset($this->currentMenuItems[$parentMenuItem])) $this->currentMenuItems[$parentMenuItem] = array();
		
		foreach ($this->menuItems[$this->menu->__get('menuID')][$parentMenuItem] as $menuItemID => $menuItem) {
			/* @var $menuItem \ultimate\data\menu\item\MenuItem */
			if ($menuItem->__get('menuID') != $this->menu->__get('menuID')) continue;
			if ($menuItem->__get('isDisabled')) continue;
			$this->currentMenuItems[$parentMenuItem][$menuItemID] = $menuItem;
			
			// check children
			$this->readCurrentItems($menuItem->__get('menuItemName'));
		}
	}
	
	/**
	 * Checks the permissions of given menu item.
	 * 
	 * @param	\ultimate\data\menu\item\MenuItem	$menuItem
	 * @return	boolean
	 */
	protected function checkMenuItem(ITreeMenuItem $menuItem) {
		// check the permission of this item for the active user
		$hasPermission = true;
		switch ($menuItem->__get('type')) {
			case 'content':
				$hasPermission = false;
				foreach ($this->contents as $contentID => $content) {
					// if you added a menu item associated with a content
					// then the name of the menu item equals the one of the content
					if ($content->__get('contentTitle') != $menuItem->__get('menuItemName')) continue;
					
					$visbility = $content->__get('visibility');
					if ($visibility == 'public') {
						$hasPermission = true;
						continue;
					} elseif ($visibility == 'private') {
						$hasPermission = (WCF::getUser()->__get('userID') == $content->__get('authorID'));
						continue;
					}
					
					$groups = $content->__get('groups');
					$accessibleGroups = WCF::getUser()->getGroupIDs();
					foreach ($accessibleGroups as $groupID) {
						if (!isset($groups[$groupID])) continue;
						$hasPermission = true;
						break 2;
					}
				}
				break;
			case 'page':
				$hasPermission = false;
				foreach ($this->pages as $pageID => $page) {
					// if you added a menu item associated with a page
					// then the name of the menu item equals the one of the page
					if ($page->__get('pageTitle') != $menuItem->__get('menuItemName')) continue;
					
					$visibility = $page->__get('visibility');
					if ($visibility == 'public') {
						$hasPermission = true;
						continue;
					} elseif ($visibility == 'private') {
						$hasPermission = (WCF::getUser()->__get('userID') == $page->__get('authorID'));
						continue;
					}
					
					$groups = $page->__get('groups');
					$accessibleGroups = WCF::getUser()->getGroupIDs();
					foreach ($accessibleGroups as $groupID) {
						if (!isset($groups[$groupID])) continue;
						$hasPermission = true;
						break 2;
					}
				}
				break;
			default:
				break;
		}
		if (!$hasPermission) return false;
		
		return $menuItem->getProcessor()->isVisible();
	}
	
	/**
	 * Checks the permissions of the menu items.
	 * 
	 * @param	string	$parentMenuItem
	 */
	protected function checkMenuItems($parentMenuItem = '') {
		if (!isset($this->currentMenuItems[$parentMenuItem])) return;
		
		foreach ($this->currentMenuItems[$parentMenuItem] as $menuItemID => $menuItem) {
			if ($this->checkMenuItem($menuItem)) {
				// check children
				$this->checkMenuItems($menuItem->__get('menuItemName'));
			}
			else {
				// remove this item
				unset($this->currentMenuItems[$parentMenuItem][$menuItemID]);
			}
		}
	}
	
	/**
	 * Builds a plain menu item list.
	 * 
	 * @param	string	$parentMenuItem
	 */
	protected function buildMenuItemList($parentMenuItem = '') {
		if (!isset($this->currentMenuItems[$parentMenuItem])) return;
		
		foreach ($this->currentMenuItems[$parentMenuItem] as $menuItem) {
			$this->menuItemList[$menuItem->__get('menuItemName')] = $menuItem;
			$this->buildMenuItemList($menuItem->__get('menuItemName'));
		}
	}
}
