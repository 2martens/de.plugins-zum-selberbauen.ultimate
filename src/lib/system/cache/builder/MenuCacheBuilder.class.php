<?php
namespace ultimate\system\cache\builder;
use ultimate\data\menu\MenuList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the menus.
 * 
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.cache.builder
 * @category Ultimate CMS
 */
class MenuCacheBuilder implements ICacheBuilder {
    
    /**
     * @see \wcf\system\cache\builder\ICacheBuilder::getData()
     */
    public function getData(array $cacheResource) {
        $data = array(
            'menus' => array(),
            'menuIDs' => array()
        );
        
        $menuList = new MenuList();
        $menuList->readObjects();
        $menus = $menuList->getObjects();
        $menuIDs = $menuList->getObjectIDs();
        
        $data['menus'] = $menus;
        $data['menuIDs'] = (is_null($menuIDs) ? array() : $menuIDs);
        
        return $data;
    }
}
