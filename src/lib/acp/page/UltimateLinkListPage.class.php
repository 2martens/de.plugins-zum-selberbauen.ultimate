<?php
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
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
class UltimateLinkListPage extends AbstractCachedListPage {
	/**
	 * @see	\wcf\page\AbstractPage::$templateName
	 */
	public $templateName = 'ultimateLinkList';
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = '\ultimate\data\link\LinkList';
	
	/**
	 * @see	\wcf\page\SortablePage::$validSortFields
	 */
	public $validSortFields = array(
		'linkID',
		'linkName'
	);
	
	/**
	 * @see	\wcf\page\SortablePage::$defaultSortField
	 */
	public $defaultSortField = 'linkID';
	
	/**
	 * @see	\wcf\page\SortablePage::$defaultSortOrder
	 */
	public $defaultSortOrder = 'ASC';
	
	/**
	 * @see	\wcf\page\AbstractCachedListPage::$cacheBuilderClassName
	 */
	public $cacheBuilderClassName = '\ultimate\system\cache\builder\LinkCacheBuilder';
	
	/**
	 * @see	\wcf\page\AbstractCachedListPage::$cacheName
	 */
	public $cacheName = 'link';
	
	/**
	 * @see	\wcf\page\AbstractCachedListPage::$cacheIndex
	 */
	public $cacheIndex = 'links';
	
	/**
	 * Contains the active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.link.list';
	
	/**
	 * Contains the category id.
	 * @var integer
	 */
	public $categoryID = 0;
	
	/**
	 * Contains the url.
	 * @var	string
	 */
	protected $url = '';
	
	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		if (isset($_GET['categoryID'])) $this->categoryID = intval($_GET['categoryID']);
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		$this->url = LinkHandler::getInstance()->getLink('UltimateLinkList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
		
		// save the items count
		$items = $this->items;
		
		// if no category id specified, proceed as always
		if (!$this->categoryID) return;
		else {
			// if category id provided, change object variables and load the new cache
			$this->cacheBuilderClassName = '\ultimate\system\cache\builder\LinkCategoryCacheBuilder';
			$this->cacheName = 'link-to-category';
			$this->cacheIndex = 'linksToCategoryID';
				
			$this->loadCache();
			$this->objects = $this->objects[$this->categoryID];
			$this->currentObjects = $this->currentObjects[$this->categoryID];
		}
		
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
			'url' => $this->url
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
