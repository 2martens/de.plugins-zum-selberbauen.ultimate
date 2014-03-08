<?php
/**
 * The UltimateMenuAdd form.
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
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\data\menu\MenuAction;
use ultimate\system\cache\builder\CategoryCacheBuilder;
use ultimate\system\cache\builder\PageCacheBuilder;
use ultimate\util\MenuUtil;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows the UltimateMenuAdd form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateMenuAddForm extends AbstractForm {
	/**
	 * The active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance.menu.add';
	
	/**
	 * The template name.
	 * @var	string
	 */
	public $templateName = 'ultimateMenuAdd';
	
	/**
	 * Array of needed permissions.
	 * @var	string[]
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canManageMenus'
	);
	
	/**
	 * The title of the category.
	 * @var	string
	 */
	public $menuName = '';
	
	/**
	 * The menu items.
	 * @var	\ultimate\data\menu\item\ViewableMenuItem[]
	 */
	public $menuItems = array();
	
	/**
	 * All categories.
	 * @var	(\ultimate\data\category\Category|array)[]
	 */
	public $categories = array();
	
	/**
	 * All categories which already exist as menu item in this menu.
	 * @var	integer[]
	 */
	public $disabledCategoryIDs = array();
	
	/**
	 * All pages.
	 * @var	(\ultimate\data\page\Page|array)[]
	 */
	public $pages = array();
	
	/**
	 * All pages which already exist as menu item in this menu.
	 * @var	integer[]
	 */
	public $disabledPageIDs = array();
	
	/**
	 * Reads parameters.
	 */
	public function readParameters() {
		parent::readParameters();
		I18nHandler::getInstance()->register('title');
	}
	
	/**
	 * Reads data.
	 */
	public function readData() {
		// read category cache
		$this->categories = CategoryCacheBuilder::getInstance()->getData(array(), 'categoriesNested');
		
		// read page cache
		$this->pages = PageCacheBuilder::getInstance()->getData(array(), 'pagesNested');
		
		parent::readData();
	}
	
	/**
	 * Reads form input.
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['menuName'])) $this->menuName = StringUtil::trim($_POST['menuName']);
	}
	
	/**
	 * Validates the form input.
	 */
	public function validate() {
		parent::validate();
		$this->validateName();
	}
	
	/**
	 * Saves the form input.
	 */
	public function save() {
		parent::save();
		
		$parameters = array(
			'data' => array(
				'menuName' => $this->menuName
			)
		);
		
		$this->objectAction = new MenuAction(array(), 'create', $parameters);
		$this->objectAction->executeAction();
		
		$returnValues = $this->objectAction->getReturnValues();
		$menuID = $returnValues['returnValues']->menuID;
		
		$this->saved();
		
		WCF::getTPL()->assign(
			'success', true
		);
		
		$url = LinkHandler::getInstance()->getLink('UltimateMenuEdit', 
			array(
				'id' => $menuID,
				'application' => 'ultimate'
			)
		);
		HeaderUtil::redirect($url);
		// after initiating the redirect, no other code should be executed as the request for the original resource has ended
		exit;
	}
	
	/**
	 * Assigns template variables.
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'menuName' => $this->menuName,
			'menuItems' => $this->menuItems,
			'categories' => $this->categories,
			'disabledCategoryIDs' => $this->disabledCategoryIDs,
			'pages' => $this->pages,
			'disabledPageIDs' => $this->disabledPageIDs,
			'action' => 'add'
		));
	}
	
	/**
	 * Validates the menu name.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateName() {
		if (empty($this->menuName)) {
				throw new UserInputException('menuName');
		}
		if (!MenuUtil::isAvailableName($this->menuName, (isset($this->menuID) ? $this->menuID : 0))) {
			throw new UserInputException('menuName', 'notUnique');
		}
	}
	
	/**
	 * Determines the nested categories.
	 * 
	 * @param array $categories
	 */
	protected function getNestedCategories(array $categories) {
		foreach ($categories as $categoryID => $categoryArray) {
			foreach ($this->menuItems as $menuItem) {
				if ($categoryArray[0]->getTitle() == $menuItem->__get('menuItemName')) {
					$this->disabledCategoryIDs[] = $categoryID;
					break;
				}
				
				/* @var $menuItem \ultimate\data\menu\item\ViewableMenuItem */
				foreach ($menuItem as $_menuItem) {
					if ($categoryArray[0]->getTitle() != $_menuItem->__get('menuItemName')) continue;
					$this->disabledCategoryIDs[] = $categoryID;
					break 2;
				}
			}
			$this->getNestedCategories($categoryArray[1]);
		}
	}
	
	/**
	 * Determines the nested pages.
	 *
	 * @param array $pages
	 */
	protected function getNestedPages(array $pages) {
		foreach ($pages as $pageID => $pageArray) {
			foreach ($this->menuItems as $menuItem) {
				if ($pageArray[0]->getTitle() == $menuItem->__get('menuItemName')) {
					$this->disabledPageIDs[] = $pageID;
					break;
				}
				
				foreach ($menuItem as $_menuItem) {
					if ($pageArray[0]->getTitle() != $_menuItem->__get('menuItemName')) continue;
					$this->disabledPageIDs[] = $pageID;
					break 2;
				}
			}
			$this->getNestedPages($pageArray[1]);
		}
	}
}
