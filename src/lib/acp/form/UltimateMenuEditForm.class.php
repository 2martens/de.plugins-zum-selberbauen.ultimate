<?php
namespace ultimate\acp\form;
use ultimate\data\menu\item\MenuItemNodeList;
use ultimate\data\menu\Menu;
use wcf\form\AbstractForm;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows the UltimateMenuEdit form.
 * 
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.form
 * @category Ultimate CMS
 */
class UltimateMenuEditForm extends UltimateMenuAddForm {
    
    /**
     * @var string
     * @see \wcf\acp\form\ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance';
    
    /**
     * Contains the menu id.
     * @var integer
     */
    public $menuID = 0;
    
    /**
     * Contains the Menu object.
     * @var \ultimate\data\menu\Menu
     */
    public $menu = null;
    
    /**
     * @see \wcf\page\IPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        
        if (isset($_REQUEST['id'])) $this->menuID = intval($_REQUEST['id']);
        $menu = new Menu($this->menuID);
        if (!$menu->__get('menuID')) {
            throw new IllegalLinkException();
        }
    
        $this->menu = $menu;
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        if (!count($_POST)) {
            // reading object fields
            $this->menuName = $this->menu->__get('menuName');
            $this->menuItemNodeList = new MenuItemNodeList($this->menuID, 0, false);
            
            // read category cache
            $cacheName = 'category';
            $cacheBuilderClassName = '\ultimate\system\cache\builder\CategoryCacheBuilder';
            $file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
            CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
            $this->categories = CacheHandler::getInstance()->get($cacheName, 'categoriesNested');
            
            // read page cache
            $cacheName = 'page';
            $cacheBuilderClassName = '\ultimate\system\cache\builder\PageCacheBuilder';
            $file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
            CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
            $this->pages = CacheHandler::getInstance()->get($cacheName, 'pagesNested');
        }
        AbstractForm::readData();
    }
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        AbstractForm::save();
        
        $parameters = array(
            'data' => array(
                'menuName' => $this->menuName
            )
        );
        
        $this->objectAction = new MenuAction(array($this->menuID), 'update', $parameters);
        $this->objectAction->executeAction();
        
        $this->saved();
        
        WCF::getTPL()->assign(
            'success', true
        );
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
    
        WCF::getTPL()->assign(array(
            'menuID' => $this->menuID,
            'action' => 'edit'
        ));
    }
}
