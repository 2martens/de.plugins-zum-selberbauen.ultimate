<?php
namespace ultimate\system\cache\builder;
use ultimate\data\menu\item\MenuItemList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the menu items.
 * 
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.cache.builder
 * @category Ultimate CMS
 */
class MenuItemCacheBuilder implements ICacheBuilder {
    /**
     * @see \wcf\system\cache\builder\ICacheBuilder::getData()
     */
    public function getData(array $cacheResource) {
        $data = array(
            'menuItems' => array(),
            'menuItemIDs' => array(),
            'menuItemsToParent' => array()
        );
        
        $menuItemList = new MenuItemList();
        $menuItemList->readObjects();
        $menuItems = $menuItemList->getObjects();
        
        foreach ($menuItems as $menuItemID => $menuItem) {
            /* @var $menuItem \ultimate\data\menu\item\MenuItem */
            $data['menuItems'][$menuItemID] = $menuItem;
            $data['menuItemIDs'][] = $menuItemID;
            $data['menuItemsToParent'][$menuItem->__get('menuItemName')] = $menuItem->__get('childItems');
        }
        
        $data['menuItemsToParent'][''] = array();
        foreach ($data['menuItems'] as $menuItemID => $menuItem) {
            if ($menuItem->__get('menuItemParent') != '') continue;
            $data['menuItemsToParent'][''][$menuItemID] = $menuItem;
        }
        
        return $data;
    }
}
