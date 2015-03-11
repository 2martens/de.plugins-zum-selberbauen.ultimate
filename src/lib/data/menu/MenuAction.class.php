<?php
/**
 * Contains the menu data model action class.
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
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.menu
 * @category	Ultimate CMS
 */
namespace ultimate\data\menu;
use ultimate\data\menu\item\MenuItemAction;
use wcf\data\page\menu\item\PageMenuItemList;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes menu-related functions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.menu
 * @category	Ultimate CMS
 */
class MenuAction extends AbstractDatabaseObjectAction {
	/**
	 * The class name.
	 * @var	string
	 */
	public $className = '\ultimate\data\menu\MenuEditor';
	
	/**
	 * Array of permissions that are required for create action.
	 * @var	string[]
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canManageMenus');
	
	/**
	 * Array of permissions that are required for delete action.
	 * @var	string[]
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canManageMenus');
	
	/**
	 * Array of permissions that are required for update action.
	 * @var	string[]
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canManageMenus');
	
	/**
	 * Creates a menu.
	 * 
	 * @return	Menu
	 */
	public function create() {
		$menu = parent::create();
		
		// create default menu entries
		$menuItemList = new PageMenuItemList();
		$menuItemList->getConditionBuilder()->add("page_menu_item.menuPosition = 'header'");
		$menuItemList->sqlOrderBy = 'page_menu_item.showOrder ASC';
		$menuItemList->readObjects();
		
		foreach ($menuItemList as $menuItem) {
			$parameters = array(
				'data' => array(
					'menuID' => $menu->__get('menuID'),
					'menuItemName' => $menuItem->__get('menuItem'),
					'menuItemParent' => $menuItem->__get('parentMenuItem'),
					'menuItemController' => $menuItem->__get('menuItemController'),
					'menuItemLink' => $menuItem->__get('menuItemLink'),
					'showOrder' => $menuItem->__get('showOrder'),
					'permissions' => $menuItem->__get('permissions'),
					'options' => $menuItem->__get('options'),
					'type' => 'custom',
					'isDisabled' => $menuItem->__get('isDisabled'),
					'className' => $menuItem->__get('className'),
					'isLandingPage' => $menuItem->__get('isLandingPage')
				)
			);
			$menuItemAction = new MenuItemAction(array(), 'create', $parameters);
			$menuItemAction->executeAction();
		}
		return $menu;
	}
}
