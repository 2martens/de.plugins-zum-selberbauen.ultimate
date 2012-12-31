<?php
/**
 * Contains the UltimateMenuAdd form.
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
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\data\menu\item\MenuItemNodeList;
use ultimate\data\menu\MenuAction;
use ultimate\util\MenuUtil;
use wcf\form\AbstractForm;
use wcf\system\cache\CacheHandler;
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
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateMenuAddForm extends AbstractForm {
	/**
	 * @var	string
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.acp.form.ACPForm.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance.menu.add';
	
	/**
	 * @var	string
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$templateName
	 */
	public $templateName = 'ultimateMenuAdd';
	
	/**
	 * @var	string[]
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canAddMenu'
	);
	
	/**
	 * Contains the title of the category.
	 * @var	string
	*/
	public $menuName = '';
	
	/**
	 * Contains the MenuItemNodeList.
	 * @var	\ultimate\data\menu\item\MenuItemNodeList
	 */
	public $menuItemNodeList = null;
	
	/**
	 * Contains all categories.
	 * @var	(\ultimate\data\category\Category|array)[]
	 */
	public $categories = array();
	
	/**
	 * Contains all categories which already exist as menu item in this menu.
	 * @var	integer[]
	 */
	public $disabledCategoryIDs = array();
	
	/**
	 * Contains all pages.
	 * @var	(\ultimate\data\page\Page|array)[]
	 */
	public $pages = array();
	
	/**
	 * Contains all pages which already exist as menu item in this menu.
	 * @var	integer[]
	 */
	public $disabledPageIDs = array();
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readParameters
	 */
	public function readParameters() {
		parent::readParameters();
		I18nHandler::getInstance()->register('title');
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		$this->menuItemNodeList = new MenuItemNodeList(0, 0, true);
		// read category cache
		$cacheName = 'category';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\CategoryCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->categories = CacheHandler::getInstance()->get($cacheName, 'categoriesNested');
		
		// get categories which are already used in this menu
		foreach ($this->categories as $categoryID => $categoryArray) {
			foreach ($this->menuItemNodeList as $menuItem) {
				if ($categoryArray[0]->__get('categoryTitle') != $menuItem->__get('menuItemName')) continue;
				$this->disabledCategoryIDs[] = $categoryID;
				break;
			}
		}
		
		// read page cache
		$cacheName = 'page';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\PageCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->pages = CacheHandler::getInstance()->get($cacheName, 'pagesNested');
		
		// get pages which are already used in this menu
		foreach ($this->pages as $pageID => $pageArray) {
			foreach ($this->menuItemNodeList as $menuItem) {
				if ($pageArray[0]->__get('pageTitle') != $menuItem->__get('menuItemName')) continue;
				$this->disabledPageIDs[] = $pageID;
				break;
			}
		}
		
		parent::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#readFormParameters
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['menuName'])) $this->menuName = StringUtil::trim($_POST['menuName']);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#validate
	 */
	public function validate() {
		parent::validate();
		$this->validateName();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
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
		$updateValues = array();
		
		$this->saved();
		
		WCF::getTPL()->assign(
			'success', true
		);
		
		$url = LinkHandler::getInstance()->getLink('UltimateMenuEdit', 
			array(
				'id' => $menuID
			)
		);
		HeaderUtil::redirect($url);
		exit;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'menuName' => $this->menuName,
			'menuItemNodeList' => $this->menuItemNodeList,
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
	
}
