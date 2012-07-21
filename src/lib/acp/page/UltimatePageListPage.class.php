<?php
namespace ultimate\acp\page;
use ultimate\system\UltimateCore;
use wcf\page\SortablePage;
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
class UltimatePageListPage extends SortablePage {
    
	/**
     * Contains the active menu item.
     * @var string
     */
    public $activeMenuItem = 'wcf.acp.menu.link.ultimate.page.list';
    
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
        'pageSlug'
    );
    
    /**
     * Contains an array of pages.
     * @var array
     */
    protected $pages = array();
    
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
        $objects = $this->objectList->getObjects();
        $pages = array();
        
        foreach ($objects as $object) {
            $config = new Config($object->configID);
            $pages[] = array(
                'pageID' => $object->pageID,
                'pageSlug' => $object->pageSlug,
                'configTitle' => $config->configTitle
            );
        }
        $this->pages = $pages;
        $this->url = LinkHandler::getInstance()->getLink('UltimatePageList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        //overrides objects assignment in MultipleLinkPage
        UltimateCore::getTPL()->assign(array(
        	'objects' => $this->pages,
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
