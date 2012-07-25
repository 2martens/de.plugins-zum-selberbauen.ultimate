<?php
namespace ultimate\acp\page;
use ultimate\system\UltimateCore;
use wcf\page\AbstractCachedListPage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\request\LinkHandler;

/**
 * Shows the UltimatePageList page.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.page
 * @category Ultimate CMS
 */
class UltimatePageListPage extends AbstractCachedListPage {
    
    /**
     * @see \wcf\page\AbstractPage::$templateName
     */
    public $templateName = 'ultimatePageList';
    
    /**
     * @see \wcf\page\MultipleLinkPage::$objectListClassName
     */
    public $objectListClassName = '\ultimate\data\page\PageList';
    
    /**
     * @see \wcf\page\SortablePage::$validSortFields
     */
    public $validSortFields = array(
        'pageID',
        'pageTitle',
        'pageAuthor',
        'lastModified'
    );
    
    /**
     * @see \wcf\page\SortablePage::$defaultSortOrder
     */
    public $defaultSortOrder = ULTIMATE_SORT_PAGE_SORTORDER;
    
    /**
     * @see \wcf\page\SortablePage::$defaultSortField
     */
    public $defaultSortField = ULTIMATE_SORT_PAGE_SORTFIELD;
    
    /**
     * @see \wcf\page\AbstractCachedListPage::$cacheBuilderClassName
     */
    public $cacheBuilderClassName = '\ultimate\system\cache\builder\UltimatePageCacheBuilder';
    
    /**
     * @see \wcf\page\AbstractCachedListPage::$cacheName
     */
    public $cacheName = 'page';
    
    /**
     * @see \wcf\page\AbstractCachedListPage::$cacheIndex
     */
    public $cacheIndex = 'pages';
    
	/**
     * Contains the active menu item.
     * @var string
     */
    public $activeMenuItem = 'wcf.acp.menu.link.ultimate.page.list';
    
    /**
     * Contains the url.
     * @var string
     */
    protected $url = '';
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        parent::readData();
        $this->url = LinkHandler::getInstance()->getLink('UltimatePageList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
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
