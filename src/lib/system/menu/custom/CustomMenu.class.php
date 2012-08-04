<?php
namespace ultimate\system\menu\custom;
use ultimate\data\menu\item\MenuItem;
use ultimate\data\menu\Menu;
use ultimate\system\UltimateCore;
use wcf\system\cache\CacheHandler;
use wcf\system\event\EventHandler;
use wcf\system\menu\TreeMenu;

/**
 * Builds a custom menu.
 * 
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.menu.custom
 * @category Ultimate CMS
 */
class CustomMenu extends TreeMenu {
    
    /**
     * Contains the menu.
     * @var \ultimate\data\menu\Menu
     */
    protected $menu = null;
    
    /**
     * Contains all pages.
     * @var \ultimate\data\page\Page[]
     */
    protected $pages = null;
    
    /**
     * Contains all contents.
     * @var \ultimate\data\content\Content[]
     */
    protected $contents = null;
    
    /**
     * Contains the current items.
     * @var array[]
     */
    protected $currentMenuItems = array();
    
    /**
     * Builds the given menu.
     * 
     * You have to call this method before using getMenuItems in order to get the menu items for your menu.
     * 
     * @param \ultimate\data\menu\Menu $menu
     */
    public function buildMenu(Menu $menu) {
        $this->menu = $menu;
        
        // get menu items of the current menu
        $this->readCurrentItems();
        
        // check menu items
        $this->checkMenuItems();
        
        // build plain menu item list
        $this->buildMenuItemList();
    }
    
    /**
     * Returns the current menu items under the given parent menu item, all current menu items if you give null or null if the given parent menu item doesn't exist.
     * 
     * This method should be called inside a template.
     * 
     * @return (\ultimate\data\menu\item\MenuItem|array)[]|null
     * @see \wcf\system\menu\TreeMenu::getMenuItems()
     */
    public function getMenuItems($parentMenuItem = null) {
        if ($parentMenuItem === null) return $this->currentMenuItems;
        if (isset($this->currentMenuItems[$parentMenuItem])) return $this->currentMenuItems[$parentMenuItem];
        return null;
    }
    
    /**
     * @see \wcf\system\menu\TreeMenu::init()
     */
    protected function init() {
        // get menu items from cache
        $this->loadCache();
        
        // call init event
        EventHandler::getInstance()->fireAction($this, 'init');
    }
    
    /**
     * @see \wcf\system\menu\TreeMenu::loadCache()
     */
    protected function loadCache() {
        parent::loadCache();
        
        // get menu item cache
        $cacheName = 'menu-item';
        $cacheBuilderClassName = '\ultimate\system\cache\builder\MenuItemCacheBuilder';
        $file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
        CacheHandler::getInstance()->addResource(
            $cacheName, 
            $file, 
            $cacheBuilderClassName
        );
        $this->menuItems = CacheHandler::getInstance()->get($cacheName, 'menuItemsToParent');
        
        // get content cache
        $cacheName = 'content';
        $cacheBuilderClassName = '\ultimate\system\cache\builder\ContentCacheBuilder';
        $file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
        CacheHandler::getInstance()->addResource(
            $cacheName,
            $file,
            $cacheBuilderClassName
        );
        $this->contents = CacheHandler::getInstance()->get($cacheName, 'contents');
        
        // get content cache
        $cacheName = 'page';
        $cacheBuilderClassName = '\ultimate\system\cache\builder\PageCacheBuilder';
        $file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
        CacheHandler::getInstance()->addResource(
            $cacheName,
            $file,
            $cacheBuilderClassName
        );
        $this->pages = CacheHandler::getInstance()->get($cacheName, 'pages');
    }
    
    /**
     * Reads current menu items.
     */
    protected function readCurrentItems($parentMenuItem = '') {
        if (!isset($this->menuItems[$parentMenuItem])) return;
        
        foreach ($this->menuItems[$parentMenuItem] as $menuItemID => $menuItem) {
            /* @var $menuItem \ultimate\data\menu\item\MenuItem */
            if ($menuItem->__get('menuID') != $this->menu->__get('menuID')) continue;
            $this->currentMenuItems[$parentMenuItem][$menuItemID] = $menuItem;
            
            // check children
            $this->readCurrentItems($menuItem->__get('menuItemName'));
        }
    }
    
    /**
     * Checks the permissions of given menu item.
     *
     * @param  \ultimate\data\menu\item\MenuItem $menuItem
     * @return boolean
     */
    protected function checkMenuItem(MenuItem $menuItem) {
        // check the permission of this item for the active user
        $hasPermission = true;
        switch ($menuItem->__get('type')) {
            case 'content':
                $hasPermission = false;
                foreach ($this->contents as $contentID => $content) {
                    // if you added a menu item associated with a content
                    // then the name of the menu item equals the one of the content
                    if ($content->__get('contentTitle') != $menuItem->__get('menuItemName')) continue;
                    $groups = $content->__get('groups');
                    $accessibleGroups = UltimateCore::getSession()->getUser()->getGroupIDs();
                    foreach ($accessibleGroups as $groupID) {
                        if (!isset($groups[$groupID])) continue;
                        $hasPermission = true;
                        break 2;
                    }
                }
                break;
            case 'page':
                $hasPermission = false;
                foreach ($this->pages as $pageID => $page) {
                    // if you added a menu item associated with a page
                    // then the name of the menu item equals the one of the page
                    if ($page->__get('pageTitle') != $menuItem->__get('menuItemName')) continue;
                    $groups = $page->__get('groups');
                    $accessibleGroups = UltimateCore::getSession()->getUser()->getGroupIDs();
                    foreach ($accessibleGroups as $groupID) {
                        if (!isset($groups[$groupID])) continue;
                        $hasPermission = true;
                        break 2;
                    }
                }
                break;
            default:
                break;
        }
        if (!$hasPermission) return false;
    
        return $menuItem->getProcessor()->isVisible();
    }
    
    /**
     * Checks the permissions of the menu items.
     * 
     * @param string $parentMenuItem
     */
    protected function checkMenuItems($parentMenuItem = '') {
        if (!isset($this->currentMenuItems[$parentMenuItem])) return;
        
        foreach ($this->currentMenuItems[$parentMenuItem] as $menuItemID => $menuItem) {
            if ($this->checkMenuItem($menuItem)) {
                // check children
                $this->checkMenuItems($menuItem->__get('menuItemName'));
            }
            else {
                // remove this item
                unset($this->currentMenuItems[$parentMenuItem][$menuItemID]);
            }
        }
    }
    
    /**
     * Builds a plain menu item list.
     *
     * @param string $parentMenuItem
     */
    protected function buildMenuItemList($parentMenuItem = '') {
        if (!isset($this->currentMenuItems[$parentMenuItem])) return;
    
        foreach ($this->currentMenuItems[$parentMenuItem] as $menuItem) {
            $this->menuItemList[$menuItem->__get('menuItemName')] = $menuItem;
            $this->buildMenuItemList($menuItem->__get('menuItemName'));
        }
    }
}
