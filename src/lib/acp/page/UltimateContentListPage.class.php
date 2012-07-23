<?php
namespace ultimate\acp\page;
use ultimate\data\category\Category;
use ultimate\system\UltimateCore;
use wcf\page\AbstractCachedListPage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\request\LinkHandler;

/**
 * Shows the UltimateContentList page.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.page
 * @category Ultimate CMS
 */
class UltimateContentListPage extends AbstractCachedListPage {
    
    /**
     * @see \wcf\page\SortablePage::$validSortFields
     */
    public $validSortFields = array(
        'contentID',
        'contentTitle',
        'contentAuthor'
    );
    
    /**
     * @see \wcf\page\SortablePage::$defaultSortOrder
     */
    public $defaultSortOrder = ULTIMATE_SORT_CONTENT_SORTORDER;
    
    /**
     * @see \wcf\page\SortablePage::$defaultSortField
     */
    public $defaultSortField = ULTIMATE_SORT_CONTENT_SORTFIELD;
    
    /**
     * Contains the active menu item.
     * @var string
     */
    public $activeMenuItem = 'wcf.acp.menu.link.ultimate.content.list';
    
    /**
     * @see \wcf\page\AbstractPage::$templateName
     */
    public $templateName = 'ultimateContentList';
    
    /**
     * @see \wcf\page\MultipleLinkPage::$objectListClassName
     */
    public $objectListClassName = '\ultimate\data\content\ContentList';
    
    /**
     * @see \wcf\page\AbstractCachedListPage::$cacheBuilderClassName
     */
    public $cacheBuilderClassName = '\ultimate\system\cache\builder\UltimateContentCacheBuilder';
    
    /**
     * @see \wcf\page\AbstractCachedListPage::$cacheName
     */
    public $cacheName = 'content';
    
    /**
     * @see \wcf\page\AbstractCachedListPage::$cacheIndex
     */
    public $cacheIndex = 'contents';
    
    /**
     * Contains the url.
     * @var string
     */
    protected $url = '';
    
    /**
     * If given only contents associated with this category are loaded.
     * @var int
     */
    protected $categoryID = 0;
    
    /**
     * @see \wcf\page\IPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        
        if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        parent::readData();
        $this->url = LinkHandler::getInstance()->getLink('UltimateContentList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
        
        // if no category id specified, proceed as always
        if (!$this->categoryID) return;
        
        // if category id provided, change object variables and load the new cache
        $this->cacheBuilderClassName = '\ultimate\cache\builder\UltimateContentCategoryCacheBuilder';
        $this->cacheName = 'content-to-category';
        $this->cacheIndex = 'contentsToCategoryID';
        $this->loadCache();
        
        $this->objects = $this->objects[$this->categoryID];
        // calculate the pages again, because the objects changed
        $this->calculateNumberOfPages();
    }
    
    
    /**
     * @see \wcf\page\AbstractCachedListPage::loadCache()
     */
    public function loadCache($path = ULTIMATE_DIR) {
        parent::loadCache($path);
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        
        UltimateCore::getTPL()->assign(array(
        	'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(),
            'url' => $this->url
        ));
    }
    
	/**
	 * @see \wcf\page\IPage::show()
	 */
	public function show() {
		// set active menu item
		ACPMenu::getInstance()->setActiveMenuItem($this->activeMenuItem);
		
		parent::show();
	}
}
