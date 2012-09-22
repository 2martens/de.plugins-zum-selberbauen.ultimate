<?php
/**
 * Contains the UltimateContentList page.
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
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
namespace ultimate\acp\page;
use ultimate\data\category\Category;
use wcf\page\AbstractCachedListPage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows the UltimateContentList page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
class UltimateContentListPage extends AbstractCachedListPage {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$templateName
	 */
	public $templateName = 'ultimateContentList';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.MultipleLinkPage.html#$objectListClassName
	 */
	public $objectListClassName = '\ultimate\data\content\ContentList';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.SortablePage.html#$validSortFields
	 */
	public $validSortFields = array(
		'contentID',
		'contentTitle',
		'contentAuthor',
		'publishDate',
		'lastModified'
	);
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.SortablePage.html#$defaultSortOrder
	 */
	public $defaultSortOrder = ULTIMATE_SORT_CONTENT_SORTORDER;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.SortablePage.html#$defaultSortField
	 */
	public $defaultSortField = ULTIMATE_SORT_CONTENT_SORTFIELD;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractCachedListPage.html#$cacheBuilderClassName
	 */
	public $cacheBuilderClassName = '\ultimate\system\cache\builder\ContentCacheBuilder';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractCachedListPage.html#$cacheName
	 */
	public $cacheName = 'content';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractCachedListPage.html#$cacheIndex
	 */
	public $cacheIndex = 'contents';
	
	/**
	 * Contains the active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.content.list';
	
	/**
	 * Contains the url.
	 * @var	string
	 */
	protected $url = '';
	
	/**
	 * If given only contents associated with this category are loaded.
	 * @var	integer
	 */
	protected $categoryID = 0;
	
	/**
	 * If given only contents associated with this tag are loaded.
	 * @var	integer
	 */
	protected $tagID = 0;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readParameters
	 */
	public function readParameters() {
		parent::readParameters();
		
		// these two are exclusive to each other
		// don't use both at the same time
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		if (isset($_REQUEST['tagID'])) $this->tagID = intval($_REQUEST['tagID']);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		parent::readData();
		$this->url = LinkHandler::getInstance()->getLink('UltimateContentList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
		// save the items count
		$items = $this->items;
		
		// if no category id and no tag id specified, proceed as always
		if (!$this->categoryID && !$this->tagID) return;
		elseif($this->categoryID) {
			// if category id provided, change object variables and load the new cache
			$this->cacheBuilderClassName = '\ultimate\system\cache\builder\ContentCategoryCacheBuilder';
			$this->cacheName = 'content-to-category';
			$this->cacheIndex = 'contentsToCategoryID';
			
			$this->loadCache();
			$this->objects = $this->objects[$this->categoryID];
			$this->calculateNumberOfPages();
			$this->currentObjects = array_slice($this->objects, ($this->pageNo - 1) * $this->itemsPerPage, $this->itemsPerPage, true);
		}
		// both category id and tag id are provided, the category id wins
		elseif ($this->tagID) {
			// if tag id provided, change object variables and load the new cache
			$this->cacheBuilderClassName = '\ultimate\system\cache\builder\ContentTagCacheBuilder';
			$this->cacheName = 'content-to-tag';
			$this->cacheIndex = 'contentsToTagID';
			
			$this->loadCache();
			$this->objects = $this->objects[$this->tagID];
			$this->calculateNumberOfPages();
			$this->currentObjects = array_slice($this->objects, ($this->pageNo - 1) * $this->itemsPerPage, $this->itemsPerPage, true);
		}
		else return; // shouldn't be called anyway
		
		// restore old items count
		$this->items = $items;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractCachedListPage.html#loadCache
	 */
	public function loadCache($path = ULTIMATE_DIR) {
		parent::loadCache($path);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(),
			'url' => $this->url,
			'timeNow' => TIME_NOW
		));
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#show
	 */
	public function show() {
		// set active menu item
		ACPMenu::getInstance()->setActiveMenuItem($this->activeMenuItem);
		
		parent::show();
	}
}
