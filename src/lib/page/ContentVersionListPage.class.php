<?php
/**
 * Contains the ContentVersionListPage class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 *
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
namespace ultimate\page;
use wcf\page\AbstractCachedListPage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\event\EventHandler;
use wcf\system\exception\SystemException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\ClassUtil;

/**
 * Provides a list of content versions.
 *
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
class ContentVersionListPage extends AbstractCachedListPage implements IEditSuitePage {
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
	public $objectListClassName = '\ultimate\data\content\version\ContentVersionList';

	/**
	 * Array of valid sort fields.
	 * @var	string[]
	 */
	public $validSortFields = array(
		'versionNumber',
		'contentTitle',
		'contentAuthor',
		'publishDate'
	);

	/**
	 * The default sort order.
	 * @var	string
	 */
	public $defaultSortOrder = 'DESC';

	/**
	 * The default sort field.
	 * @var	string
	 */
	public $defaultSortField = 'versionNumber';

	/**
	 * The order by clause.
	 * @var string
	 */
	public $sqlOrderBy = 'versionNumber DESC';

	/**
	 * Contains the fully qualified name of the CacheBuilder.
	 * @var string
	 */
	public $cacheBuilderClassName = '\ultimate\system\cache\builder\ContentVersionCacheBuilder';

	/**
	 * The cache index.
	 * @see \wcf\page\AbstractCachedListPage::$cacheIndex
	 */
	public $cacheIndex = 'versionsToObjectID';

	/**
	 * The object decorator class name.
	 * @var string
	 */
	public $objectDecoratorClass = '';
	
	/**
	 * The content id.
	 * @var integer
	 */
	protected $contentID = 0;

	/**
	 * The url.
	 * @var	string
	 */
	protected $url = '';

	/**
	 * If given only contents written by this author are loaded.
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
	 * A list of active EditSuite menu items.
	 * @var string[]
	 */
	protected $activeMenuItems = array(
		'ContentListPage',
		'ultimate.edit.contents'
	);

	/**
	 * Reads parameters.
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->contentID = intval($_REQUEST['id']);
		if (isset($_REQUEST['authorID'])) $this->authorID = intval($_REQUEST['authorID']);
	}

	/**
	 * Reads data.
	 */
	public function readData() {
		parent::readData();
		$this->url = LinkHandler::getInstance()->getLink('ContentVersionList', array('id' => $this->contentID), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
		// save the items count
		$items = $this->items;

		// if no category id, no tag id and no author id specified, proceed as always
		if (!$this->authorID) {
			return;
		}
		else if ($this->authorID) {
			// TODO ContentVersionAuthorCache
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
			$versions = $this->objects;
			$newVersions = array();
			// get array with usernames
			/* @var $content \ultimate\data\content\Content */
			foreach ($versions as $version) {
				$newVersions[$version->__get('author')->__get('username')] = $version;
			}
			// actually sort the array
			if ($this->sortOrder == 'ASC') ksort($newVersions);
			else krsort($newVersions);
			// refill the sorted values into the original array
			foreach ($newVersions as $version) {
				$version[$version->__get('versionID')] = $version;
			}
			// return the sorted array
			$this->objects = $versions;
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
	 * @see \wcf\page\AbstractCachedListPage::loadCache
	 * @throws \wcf\system\exception\SystemException if cacheBuilderClassName does not implement ICacheBuilder
	 */
	public function loadCache() {
		// call loadCache event
		EventHandler::getInstance()->fireAction($this, 'loadCache');

		if (!ClassUtil::isInstanceOf($this->cacheBuilderClassName, 'wcf\system\cache\builder\ICacheBuilder')) {
			throw new SystemException("Class '".$this->cacheBuilderClassName."' does not implement 'wcf\\system\\cache\\builder\\ICacheBuilder'");
		}

		$instance = call_user_func($this->cacheBuilderClassName.'::getInstance');
		$this->objects = $instance->getData(array(), $this->cacheIndex);
		$this->objects = $this->objects[$this->contentID];
		$this->currentObjects = array_slice($this->objects, ($this->pageNo - 1) * $this->itemsPerPage, $this->itemsPerPage, true);
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
		$result = WCF::getTPL()->fetch('__editSuiteJS.ContentVersionListPage', 'ultimate');
		return $result;
	}

	/**
	 * @see \wcf\page\AbstractCachedListPage::assignVariables()
	 */
	public function assignVariables() {
		// reset sort field and order to temporarily saved values
		if (!empty($this->tempSortField)) $this->sortField = $this->tempSortField;
		if (!empty($this->tempSortOrder)) $this->sortOrder = $this->tempSortOrder;

		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(),
			'url' => $this->url,
			'timeNow' => TIME_NOW,
			'contentID' => $this->contentID
		));

		WCF::getTPL()->assign(array(
			'activeMenuItems' => $this->activeMenuItems,
			'pageContent' => WCF::getTPL()->fetch('__editSuite.ContentVersionListPage', 'ultimate'),
			'pageJS' => WCF::getTPL()->fetch('__editSuiteJS.ContentVersionListPage', 'ultimate'),
			'initialController' => 'ContentVersionListPage',
			'initialRequestType' => 'page',
			'initialURL' => '/EditSuite/ContentVersionList/'.$this->contentID.'/'
		));
	}

	/**
	 * Shows the requested page.
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
		$conditionBuilder->add('contentID = ?', array($this->contentID));
		parent::readObjects();
	}
}
