<?php
namespace ultimate\acp\page;
use ultimate\system\UltimateCore;
use wcf\page\SortablePage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\request\LinkHandler;

/**
 * Shows the UltimateCategoryList page.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.page
 * @category Ultimate CMS
 */
class UltimateCategoryListPage extends SortablePage {

    /**
     * Contains the active menu item.
     * @var string
     */
    public $activeMenuItem = 'wcf.acp.menu.link.ultimate.category.list';

    /**
     * @see \wcf\page\AbstractPage::$templateName
     */
    public $templateName = 'ultimateCategoryList';

    /**
     * @see \wcf\page\MultipleLinkPage::$objectListClassName
     */
    public $objectListClassName = '\ultimate\data\category\CategoryList';

    /**
     * @see \wcf\page\SortablePage::$validSortFields
     */
    public $validSortFields = array(
        'categoryID',
        'categoryTitle',
        'categoryDescription',
        'categorySlug'
    );

    /**
     * Contains the url.
     * @var string
    */
    protected $url = '';

    /**
     * Contains all category objects.
     * @var array<ultimate\date\category\Category>
     */
    protected $objects = array();


    /**
     * @see \wcf\page\IPage::readData()
    */
    public function readData() {
        parent::readData();
        $this->objects = $this->objectList->getObjects();
        $this->url = LinkHandler::getInstance()->getLink('UltimateCategoryList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
    }

    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        //overrides objects assignment in MultipleLinkPage
        UltimateCore::getTPL()->assign(array(
            'objects' => $this->objects,
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
