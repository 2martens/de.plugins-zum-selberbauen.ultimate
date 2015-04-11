<?php
/**
 * The UltimateCategoryList page.
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
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
namespace ultimate\acp\page;
use wcf\page\AbstractCachedListPage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows the UltimateCategoryList page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
class UltimateCategoryListPage extends AbstractCachedListPage {
	/**
	 * The template name.
	 * @var	string
	 */
	public $templateName = 'ultimateCategoryList';
	
	/**
	 * The object list class name.
	 * @var	string
	 */
	public $objectListClassName = '\ultimate\data\category\CategoryList';
	
	/**
	 * Array of valid sort fields.
	 * @var	string[]
	 */
	public $validSortFields = array(
		'categoryID',
		'categoryTitle',
		'categoryDescription',
		'categorySlug',
		'categoryContents'
	);
	
	/**
	 * The default sort field.
	 * @var	string
	 */
	public $defaultSortField = ULTIMATE_SORT_CATEGORY_SORTFIELD;
	
	/**
	 * The default sort order.
	 * @var	string
	 */
	public $defaultSortOrder = ULTIMATE_SORT_CATEGORY_SORTORDER;
	
	/**
	 * The cache builder class name.
	 * @see \wcf\page\AbstractCachedListPage::$cacheBuilderClassName
	 */
	public $cacheBuilderClassName = '\ultimate\system\cache\builder\CategoryCacheBuilder';
	
	/**
	 * The cache index.
	 * @see \wcf\page\AbstractCachedListPage::$cacheIndex
	 */
	public $cacheIndex = 'categories';
	
	/**
	 * The active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.category.list';

	/**
	 * The url.
	 * @var	string
	 */
	protected $url = '';
	
	/**
	 * Reads data.
	 */
	public function readData() {
		parent::readData();
		if ($this->sortField == 'category.categoryID') {
			$this->sortField = 'categoryID';
		}
		$this->url = LinkHandler::getInstance()->getLink('UltimateCategoryList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
	}
	
	/**
	 * Validates the sort field.
	 */
	public function validateSortField() {
		parent::validateSortField();
		
		if ($this->sortField == 'categoryContents') {
			$categories = $this->objects;
			$newCategories = array();
			// get array with content count
			foreach ($categories as $categoryID => $category) {
				$newCategories[$categoryID] = count($category->getContentsLazy());
			}
			// actually sort the array
			if ($this->sortOrder == 'ASC') asort($newCategories);
			else arsort($newCategories);
			// refill the original array with the sorted values
			$finalCategories = array();
			foreach ($newCategories as $categoryID => $count) {
				$finalCategories[$categoryID] = $categories[$categoryID];
			}
			
			// save the sort field and order temporarily and restore them to default
			// prevents second sort attempt
			$this->tempSortField = $this->sortField;
			$this->tempSortOrder = $this->sortOrder;
			$this->sortField = $this->defaultSortField;
			$this->sortOrder = $this->defaultSortOrder;
			
			// return the sorted array
			$this->objects = $finalCategories;
			$this->currentObjects = array_slice($this->objects, ($this->pageNo - 1) * $this->itemsPerPage, $this->itemsPerPage, true);
		}
		if ($this->sortField == 'categoryID') {
			$this->sortField = 'category.categoryID';
		}
	}
	
	/**
	 * Loads the cache.
	 * 
	 * @param	string	$path
	 * 
	 * @see \wcf\page\AbstractCachedListPage::loadCache
	 */
	public function loadCache($path = ULTIMATE_DIR) {
		parent::loadCache($path);
	}
	
	/**
	 * Assigns template variables.
	 */
	public function assignVariables() {
		if (isset($this->tempSortField) && isset($this->tempSortOrder)) {
			$this->sortField = $this->tempSortField;
			$this->sortOrder = $this->tempSortOrder;
		}
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(),
			'url' => $this->url
		));
	}
	
	/**
	 * Shows the page.
	 */
	public function show() {
		// set active menu item
		ACPMenu::getInstance()->setActiveMenuItem($this->activeMenuItem);
		
		parent::show();
	}

	/**
	 * Reads object list.
	 */
	protected function readObjects() {
		$conditionBuilder = $this->objectList->getConditionBuilder();
		$conditionBuilder->add(
			'(categoryLanguage.languageID = ? OR categoryLanguage.languageID IS NULL)',
			array(WCF::getLanguage()->getObjectID())
		);
		$conditionBuilder->add("categoryLanguage.categoryTitle <> ''", array());

		parent::readObjects();
	}
}
