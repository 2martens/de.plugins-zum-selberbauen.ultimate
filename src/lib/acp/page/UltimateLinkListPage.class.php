<?php
namespace ultimate\acp\page;
use wcf\system\request\LinkHandler;

use ultimate\data\config\Config;
use ultimate\system\UltimateCore;
use wcf\page\SortablePage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\menu\acp\ACPMenu;

/**
 * Shows the UltimateLinkList page.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.page
 * @category Ultimate CMS
 */
class UltimateLinkListPage extends SortablePage {
    
	/**
     * Contains the active menu item.
     * @var string
     */
    public $activeMenuItem = 'wcf.acp.menu.item.link.ultimate.link.list';
    
    /**
     * @see \wcf\page\AbstractPage::$templateName
     */
    public $templateName = 'ultimateLinkList';
    
    /**
     * @see \wcf\page\MultiplLinkPage::$objectListClassName
     */
    public $objectListClassName = '\ultimate\data\link\LinkList';
    
    /**
	 * @see \wcf\page\SortablePage::$validSortFields
	 */
    public $validSortFields = array(
        'linkID',
        'linkSlug'
    );
    
    /**
     * Contains an array of links.
     * @var array
     */
    protected $links = array();
    
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
        $links = array();
        
        foreach ($objects as $object) {
            $config = new Config($object->configID);
            $links[] = array(
                'linkID' => $object->linkID,
                'linkSlug' => $object->linkSlug,
                'configTitle' => $config->configTitle
            );
        }
        $this->links = $links;
        $this->url = LinkHandler::getInstance()->getLink('UltimateLinkList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        //overrides objects assignment in MultipleLinkPage
        UltimateCore::getTPL()->assign(array(
        	'links' => $this->links,
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
