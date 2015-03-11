<?php
/**
 * The UltimateContentList page.
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
 * Shows the UltimateContentList page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
class UltimateContentListPage extends AbstractCachedListPage {
	/**
	 * The template name.
	 * @var	string
	 */
	public $templateName = 'ultimateContentList';
	
	/**
	 * The object list class name.
	 * @var	string
	 */
	public $objectListClassName = '\ultimate\data\content\ContentList';
	
	/**
	 * Array of valid sort fields.
	 * @var	string[]
	 */
	public $validSortFields = array(
		'contentID',
		'contentTitle',
		'contentAuthor',
		'publishDate',
		'lastModified'
	);
	
	/**
	 * The default sort order.
	 * @var	string
	 */
	public $defaultSortOrder = ULTIMATE_SORT_CONTENT_SORTORDER;
	
	/**
	 * The default sort field.
	 * @var	string
	 */
	public $defaultSortField = ULTIMATE_SORT_CONTENT_SORTFIELD;
	
	/**
	 * The cache builder class name.
	 * @see \wcf\page\AbstractCachedListPage::$cacheBuilderClassName
	 */
	public $cacheBuilderClassName = '\ultimate\system\cache\builder\ContentCacheBuilder';
	
	/**
	 * The cache index.
	 * @see \wcf\page\AbstractCachedListPage::$cacheIndex
	 */
	public $cacheIndex = 'contents';
	
	/**
	 * The object decorator class name.
	 * @var string
	 */
	public $objectDecoratorClass = '\ultimate\data\content\TaggedContent';
	
	/**
	 * The active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.content.list';
	
	/**
	 * The url.
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
	 * Contains a temporarily saved sort field.
	 * @var string
	 */
	protected $tempSortField = '';
	
	/**
	 * Contains a temporarily saved sort order.
	 * @var string
	 */
	protected $tempSortOrder = '';
	
	/**
	 * Reads parameters.
	 */
	public function readParameters() {
		parent::readParameters();
		
		// these two are exclusive to each other
		// don't use both at the same time
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		if (isset($_REQUEST['tagID'])) $this->tagID = intval($_REQUEST['tagID']);
	}
	
	/**
	 * Reads data.
	 */
	public function readData() {
		parent::readData();
		$this->url = LinkHandler::getInstance()->getLink('UltimateContentList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
		// save the items count
		$items = $this->items;
		
		// if no category id and no tag id specified, proceed as always
		if (!$this->categoryID && !$this->tagID) {
			return;
		} else if ($this->categoryID) {
			// if category id provided, change object variables and load the new cache
			$this->cacheBuilderClassName = '\ultimate\system\cache\builder\ContentCategoryCacheBuilder';
			$this->cacheIndex = 'contentsToCategoryID';
			
			$this->loadCache();
			$this->objects = $this->objects[$this->categoryID];
			$this->calculateNumberOfPages();
			$this->currentObjects = array_slice($this->objects, ($this->pageNo - 1) * $this->itemsPerPage, $this->itemsPerPage, true);
		}
		// both category id and tag id are provided, the category id wins
		else if ($this->tagID) {
			// if tag id provided, change object variables and load the new cache
			$this->cacheBuilderClassName = '\ultimate\system\cache\builder\ContentTagCacheBuilder';
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
	 * Validates the sort field.
	 * 
	 * Validates the sort field and sorts the array if the sort field is contentAuthor.
	 */
	public function validateSortField() {
		parent::validateSortField();
		if ($this->sortField == 'contentAuthor') {
			$contents = $this->objects;
			$newContents = array();
			// get array with usernames
			/* @var $content \ultimate\data\content\Content */
			foreach ($contents as $contentID => $content) {
				$newContents[$content->__get('author')->__get('username')] = $content;
			}
			// actually sort the array
			if ($this->sortOrder == 'ASC') ksort($newContents);
			else krsort($newContents);
			// refill the sorted values into the original array
			foreach ($newContents as $authorName => $content) {
				$contents[$content->__get('contentID')] = $content;
			}
			// return the sorted array
			$this->objects = $contents;
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
			'url' => $this->url,
			'timeNow' => TIME_NOW
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
