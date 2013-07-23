<?php
/**
 * Contains the UltimateMenuEdit form.
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
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\data\menu\item\ViewableMenuItem;
use ultimate\data\menu\Menu;
use ultimate\data\menu\MenuAction;
use ultimate\system\cache\builder\CategoryCacheBuilder;
use ultimate\system\cache\builder\MenuItemCacheBuilder;
use ultimate\system\cache\builder\PageCacheBuilder;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows the UltimateMenuEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateMenuEditForm extends UltimateMenuAddForm {
	/**
	 * @var	string
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance';
	
	/**
	 * Contains the menu id.
	 * @var	integer
	 */
	public $menuID = 0;
	
	/**
	 * Contains the Menu object.
	 * @var	\ultimate\data\menu\Menu
	 */
	public $menu = null;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readParameters
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['id'])) $this->menuID = intval($_REQUEST['id']);
		$menu = new Menu($this->menuID);
		if (!$menu->__get('menuID')) {
			throw new IllegalLinkException();
		}
		
		$this->menu = $menu;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		// reading object fields
		$this->menuName = $this->menu->__get('menuName');
		$menuItems = MenuItemCacheBuilder::getInstance()->getData(array(), 'menuItems');
		$menuItems = $menuItems[$this->menuID];
		$this->menuItems = array();
		foreach ($menuItems as $menuItem) {
			if ($menuItem->__get('menuItemParent')) {
				if (isset($this->menuItems[$menuItem->__get('menuItemParent')])) {
					$this->menuItems[$menuItem->__get('menuItemParent')]->addChild($menuItem);
				}
			}
			else {
				$this->menuItems[$menuItem->menuItemName] = new ViewableMenuItem($menuItem);
			}
		}
			
		// read category cache
		$this->categories = CategoryCacheBuilder::getInstance()->getData(array(), 'categoriesNested');
		$this->disabledCategoryIDs = array();
		// get categories which are already used in this menu
		foreach ($this->categories as $categoryID => $categoryArray) {
			foreach ($this->menuItems as $menuItem) {
				/* @var $category \ultimate\data\category\Category */
				/* @var $menuItem \ultimate\data\menu\item\MenuItemNode */
				if ($categoryArray[0]->__get('categoryTitle') != $menuItem->__get('menuItemName')) continue;
				$this->disabledCategoryIDs[] = $categoryID;
				break;
				
				/* @var $menuItem \ultimate\data\menu\item\ViewableMenuItem */
				foreach ($menuItem as $_menuItem) {
					if ($categoryArray[0]->__get('categoryTitle') != $_menuItem->__get('menuItemName')) continue;
					$this->disabledCategoryIDs[] = $categoryID;
					break;
				}
			}
			$this->getNestedCategories($categoryArray[1]);
		}
		
		// read page cache
		$this->pages = PageCacheBuilder::getInstance()->getData(array(), 'pagesNested');
		$this->disabledPageIDs = array();
		// get pages which are already used in this menu
		foreach ($this->pages as $pageID => $pageArray) {
			foreach ($this->menuItems as $menuItem) {
				if ($pageArray[0]->__get('pageTitle') != $menuItem->__get('menuItemName')) continue;
				$this->disabledPageIDs[] = $pageID;
				break;
				
				/* @var $menuItem \ultimate\data\menu\item\ViewableMenuItem */
				foreach ($menuItem as $_menuItem) {
					if ($pageArray[0]->__get('pageTitle') != $_menuItem->__get('menuItemName')) continue;
					$this->disabledPageIDs[] = $pageID;
					break;
				}
			}
			$this->getNestedPages($pageArray[1]);
		}
		AbstractForm::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		AbstractForm::save();
		
		$parameters = array(
			'data' => array(
				'menuName' => $this->menuName
			)
		);
		
		$this->objectAction = new MenuAction(array($this->menuID), 'update', $parameters);
		$this->objectAction->executeAction();
		
		$this->saved();
		
		WCF::getTPL()->assign(
			'success', true
		);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'menuID' => $this->menuID,
			'action' => 'edit'
		));
	}
}
