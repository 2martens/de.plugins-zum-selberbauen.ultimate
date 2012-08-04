<?php
namespace ultimate\util;
use wcf\system\cache\CacheHandler;
use wcf\util\StringUtil;

/**
 * Provides useful functions for menus.
 * 
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage util
 * @category Ultimate CMS
 */
class MenuUtil {
    
    /**
     * Checks whether the given name is available or not.
     *
     * @param   string    $menuName
     * @return  boolean   $isAvailable
     */
    public static function isAvailableName($menuName) {
        $menuName = StringUtil::trim($menuName);
        $isAvailable = true;
    
        $menus = self::loadCache(
            'menu',
            '\ultimate\system\cache\builder\MenuCacheBuilder',
            'menus'
        );
    
        foreach ($menus as $menu) {
            /* @var $menu \ultimate\data\menu\Menu */
            if ($menu->__get('menuName') != $menuName) continue;
            $isAvailable = false;
            break;
        }
    
        return $isAvailable;
    }
    
    /**
     * Loads the cache.
     *
     * @return \ultimate\data\menu\Menu[]
     */
    protected static function loadCache($cache, $cacheBuilderClass, $cacheIndex) {
        $file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
        CacheHandler::getInstance()->addResource($cache, $file, $cacheBuilderClass);
        return CacheHandler::getInstance()->get($cache, $cacheIndex);
    }
    
    /**
     * Constructor not supported.
     */
    private function __construct() {}
}