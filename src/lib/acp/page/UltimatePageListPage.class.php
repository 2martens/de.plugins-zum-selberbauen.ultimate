<?php
/**
 * The UltimatePageList page.
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
 * @copyright	2011-2014 Jim Martens
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
 * Shows the UltimatePageList page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
class UltimatePageListPage extends AbstractCachedListPage {
	/**
	 * The template name.
	 * @var	string
	 */
	public $templateName = 'ultimatePageList';
	
	/**
	 * The object list class name.
	 * @var	string
	 */
	public $objectListClassName = '\ultimate\data\page\PageList';
	
	/**
	 * Array of valid sort fields.
	 * @var	string[]
	 */
	public $validSortFields = array(
		'pageID',
		'pageTitle',
		'pageAuthor',
		'publishDate',
		'lastModified'
	);
	
	/**
	 * The default sort order.
	 * @var	string
	 */
	public $defaultSortOrder = ULTIMATE_SORT_PAGE_SORTORDER;
	
	/**
	 * The default sort field.
	 * @var	string
	 */
	public $defaultSortField = ULTIMATE_SORT_PAGE_SORTFIELD;
	
	/**
	 * The cache builder class name.
	 * @see \wcf\page\AbstractCachedListPage::$cacheBuilderClassName
	 */
	public $cacheBuilderClassName = '\ultimate\system\cache\builder\PageCacheBuilder';
	
	/**
	 * The cache index.
	 * @see \wcf\page\AbstractCachedListPage::$cacheIndex
	 */
	public $cacheIndex = 'pages';
	
	/**
	 * The active menu item.
	 * @var string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.page.list';
	
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
		$this->url = LinkHandler::getInstance()->getLink('UltimatePageList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
	}
	
	/**
	 * Validates the sort field.
	 */
	public function validateSortField() {
		parent::validateSortField();
		
		if ($this->sortField == 'pageAuthor') {
			$pages = $this->objects;
			$newPages = array();
			// get array with usernames
			/* @var $page \ultimate\data\page\Page */
			foreach ($pages as $pageID => $page) {
				$newPages[$page->__get('author')->__get('username')] = $page;
			}
			// actually sort the array
			if ($this->sortOrder == 'ASC') ksort($newPages);
			else krsort($newPages);
			// refill the sorted values into the original array
			foreach ($newPages as $authorName => $page) {
				$pages[$page->__get('pageID')] = $page;
			}
			// return the sorted array
			$this->objects = $pages;
			$this->currentObjects = array_slice($this->objects, ($this->pageNo - 1) * $this->itemsPerPage, $this->itemsPerPage, true);
			
			// refill sort values with default values to prevent a second sort process
			$this->tempSortField = $this->sortField;
			$this->tempSortOrder = $this->sortOrder;
			$this->sortField = $this->defaultSortField;
			$this->sortOrder = $this->defaultSortOrder;
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
		// reset sort field and order to temporarily saved values
		if (!empty($this->tempSortField)) $this->sortField = $this->tempSortField;
		if (!empty($this->tempSortOrder)) $this->sortOrder = $this->tempSortOrder;
		
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
}
