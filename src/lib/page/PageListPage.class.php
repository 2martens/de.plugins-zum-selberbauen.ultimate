<?php
/**
 * The PageList page.
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
 * @subpackage	page
 * @category	Ultimate CMS
 */
namespace ultimate\page;
use wcf\page\AbstractCachedListPage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Provides a list of pages.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
class PageListPage extends AbstractCachedListPage implements IEditSuitePage {
	/**
	 * name of the template for the called page
	 * @var	string
	 */
	public $templateName = 'editSuite';

	/**
	 * indicates if you need to be logged in to access this page
	 * @var	boolean
	 */
	public $loginRequired = true;

	/**
	 * enables template usage
	 * @var	string
	 */
	public $useTemplate = true;
	
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
	 * If given only pages written by this author are loaded.
	 * @var integer
	 */
	protected $authorID = 0;

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
	 * The url.
	 * @var	string
	 */
	protected $url = '';

	/**
	 * A list of active EditSuite menu items.
	 * @var string[]
	 */
	protected $activeMenuItems = array(
		'PageListPage',
		'ultimate.edit.contents'
	);

	/**
	 * Reads parameters.
	 */
	public function readParameters() {
		parent::readParameters();

		// these two are exclusive to each other
		// don't use both at the same time
		if (isset($_REQUEST['authorID'])) $this->authorID = intval($_REQUEST['authorID']);
	}
	
	/**
	 * Reads data.
	 */
	public function readData() {
		parent::readData();
		if ($this->sortField == 'page.pageID') {
			$this->sortField = 'pageID';
		}
		$this->url = LinkHandler::getInstance()->getLink('PageList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);

		// save the items count
		$items = $this->items;

		// if no category id, no tag id and no author id specified, proceed as always
		if (!$this->authorID) {
			return;
		}
		else if ($this->authorID) {
			$this->cacheBuilderClassName = '\ultimate\system\cache\builder\PageAuthorCacheBuilder';
			$this->cacheIndex = 'pagesToAuthorID';

			$this->loadCache();
			$this->objects = $this->objects[$this->authorID];
			$this->calculateNumberOfPages();
			$this->currentObjects = array_slice($this->objects, ($this->pageNo - 1) * $this->itemsPerPage, $this->itemsPerPage, true);
		}
		
		// restore old items count
		$this->items = $items;
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
		if ($this->sortField == 'pageID') {
			$this->sortField = 'page.pageID';
		}
	}
	
	/**
	 * Loads the cache.
	 * 
	 * @see \wcf\page\AbstractCachedListPage::loadCache
	 */
	public function loadCache() {
		parent::loadCache();
	}

	/**
	 * @see \ultimate\page\IEditSuitePage::getActiveMenuItems()
	 */
	public function getActiveMenuItems() {
		return $this->activeMenuItems;
	}

	/**
	 * @see \ultimate\page\IEditSuitePage::getJavascript()
	 */
	public function getJavascript() {
		$this->readData();
		if (!empty($this->tempSortField)) $this->sortField = $this->tempSortField;
		if (!empty($this->tempSortOrder)) $this->sortOrder = $this->tempSortOrder;

		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems()
		));
		$result = WCF::getTPL()->fetch('__editSuiteJS.PageListPage', 'ultimate');
		return $result;
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

		WCF::getTPL()->assign(array(
			'activeMenuItems' => $this->activeMenuItems,
			'pageContent' => WCF::getTPL()->fetch('__editSuite.PageListPage', 'ultimate'),
			'pageJS' => WCF::getTPL()->fetch('__editSuiteJS.PageListPage', 'ultimate'),
			'initialController' => 'PageListPage',
			'initialRequestType' => 'page',
			'initialURL' => '/EditSuite/PageList/'
		));
	}
	
	/**
	 * Shows the page.
	 */
	public function show() {
		parent::show();
		if (!$this->useTemplate) {
			WCF::getTPL()->display($this->templateName, 'ultimate', false);
		}
	}

	/**
	 * Reads object list.
	 */
	protected function readObjects() {
		$conditionBuilder = $this->objectList->getConditionBuilder();
		$conditionBuilder->add(
			'(pageLanguage.languageID = ? OR pageLanguage.languageID IS NULL)',
			array(WCF::getLanguage()->getObjectID())
		);
		$conditionBuilder->add('pageLanguage.pageTitle IS NOT NULL', array());

		parent::readObjects();
	}
}
