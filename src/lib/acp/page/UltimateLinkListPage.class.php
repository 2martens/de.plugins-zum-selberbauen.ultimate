<?php
/**
 * The UltimateLinkList page.
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
 * Shows the UltimateLinkList page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
class UltimateLinkListPage extends AbstractCachedListPage {
	/**
	 * The template name.
	 * @var	string
	 */
	public $templateName = 'ultimateLinkList';
	
	/**
	 * The object list class name.
	 * @var	string
	 */
	public $objectListClassName = '\ultimate\data\link\LinkList';
	
	/**
	 * Array of valid sort fields.
	 * @var	string[]
	 */
	public $validSortFields = array(
		'linkID',
		'linkName'
	);
	
	/**
	 * The default sort field.
	 * @var	string
	 */
	public $defaultSortField = 'linkID';
	
	/**
	 * The default sort order.
	 * @var	string
	 */
	public $defaultSortOrder = 'ASC';
	
	/**
	 * The cache builder class name.
	 * @see \wcf\page\AbstractCachedListPage::$cacheBuilderClassName
	 */
	public $cacheBuilderClassName = '\ultimate\system\cache\builder\LinkCacheBuilder';
	
	/**
	 * The cache index.
	 * @see \wcf\page\AbstractCachedListPage::$cacheIndex
	 */
	public $cacheIndex = 'links';
	
	/**
	 * The active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.link.list';
	
	/**
	 * The category id.
	 * @var integer
	 */
	public $categoryID = 0;
	
	/**
	 * The url.
	 * @var	string
	 */
	protected $url = '';
	
	/**
	 * Reads parameters.
	 */
	public function readParameters() {
		parent::readParameters();
		if (isset($_GET['categoryID'])) $this->categoryID = intval($_GET['categoryID']);
	}
	
	/**
	 * Reads data.
	 */
	public function readData() {
		parent::readData();
		$this->url = LinkHandler::getInstance()->getLink('UltimateLinkList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
		
		// save the items count
		$items = $this->items;
		
		// if no category id specified, proceed as always
		if (!$this->categoryID) return;
		// if category id provided, change object variables and load the new cache
		$this->cacheBuilderClassName = '\ultimate\system\cache\builder\LinkCategoryCacheBuilder';
		$this->cacheIndex = 'linksToCategoryID';
				
		$this->loadCache();
		$this->objects = $this->objects[$this->categoryID];
		
		// calculate the pages again, because the objects changed
		$this->calculateNumberOfPages();
		
		$this->currentObjects = array_slice($this->objects, ($this->pageNo - 1) * $this->itemsPerPage, $this->itemsPerPage, true);
				
		// restore old items count
		$this->items = $items;
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
