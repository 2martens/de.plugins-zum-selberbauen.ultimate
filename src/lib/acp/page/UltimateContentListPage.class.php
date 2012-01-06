<?php
namespace ultimate\acp\page;
use ultimate\system\UltimateCore;
use wcf\page\SortablePage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\menu\acp\ACPMenu;

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
class UltimateContentListPage extends SortablePage {
    
    /**
     * Contains the active menu item.
     * @var string
     */
    public $activeMenuItem = 'wcf.acp.menu.item.link.ultimate.content.list';
    
    /**
     * @see \wcf\page\AbstractPage::$templateName
     */
    public $templateName = 'ultimateContentList';
    
    /**
     * @see \wcf\page\MultiplLinkPage::$objectListClassName
     */
    public $objectListClassName = '\ultimate\data\content\ContentList';
    
    /**
	 * @see \wcf\page\SortablePage::$validSortFields
	 */
    public $validSortFields = array(
        'contentID',
        'contentTitle'
    );
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        //overrides objects assignment in MultipleLinkPage
        UltimateCore::getTPL()->assign(array(
        	'objects' => $this->links,
            'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems()
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
