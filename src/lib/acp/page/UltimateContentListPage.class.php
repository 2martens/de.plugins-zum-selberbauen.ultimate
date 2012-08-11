<?php
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
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
class UltimateContentListPage extends AbstractCachedListPage {
	/**
	 * @see	\wcf\page\AbstractPage::$templateName
	 */
	public $templateName = 'ultimateContentList';
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = '\ultimate\data\content\ContentList';
	
	/**
	 * @see	\wcf\page\SortablePage::$validSortFields
	 */
	public $validSortFields = array(
		'contentID',
		'contentTitle',
		'contentAuthor',
		'publishDate',
		'lastModified'
	);
	
	/**
	 * @see	\wcf\page\SortablePage::$defaultSortOrder
	 */
	public $defaultSortOrder = ULTIMATE_SORT_CONTENT_SORTORDER;
	
	/**
	 * @see	\wcf\page\SortablePage::$defaultSortField
	 */
	public $defaultSortField = ULTIMATE_SORT_CONTENT_SORTFIELD;
	
	/**
	 * @see	\wcf\page\AbstractCachedListPage::$cacheBuilderClassName
	 */
	public $cacheBuilderClassName = '\ultimate\system\cache\builder\ContentCacheBuilder';
	
	/**
	 * @see	\wcf\page\AbstractCachedListPage::$cacheName
	 */
	public $cacheName = 'content';
	
	/**
	 * @see	\wcf\page\AbstractCachedListPage::$cacheIndex
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
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// these two are exclusive to each other
		// don't use both at the same time
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		if (isset($_REQUEST['tagID'])) $this->tagID = intval($_REQUEST['tagID']);
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
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
			
		}
		// both category id and tag id are provided, the category id wins
		elseif ($this->tagID) {
			// TODO implement tags
		}
		else return; // shouldn't be called anyway
		$this->loadCache();
		$this->objects = $this->objects[$this->categoryID];
		$this->currentObjects = $this->currentObjects[$this->categoryID];
		
		// calculate the pages again, because the objects changed
		$this->calculateNumberOfPages();
		
		// restore old items count
		$this->items = $items;
	}
	
	/**
	 * @see	\wcf\page\AbstractCachedListPage::loadCache()
	 */
	public function loadCache($path = ULTIMATE_DIR) {
		parent::loadCache($path);
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
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
	 * @see	\wcf\page\IPage::show()
	 */
	public function show() {
		// set active menu item
		ACPMenu::getInstance()->setActiveMenuItem($this->activeMenuItem);
		
		parent::show();
	}
}
