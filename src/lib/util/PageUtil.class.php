<?php
namespace ultimate\util;
use wcf\system\cache\CacheHandler;

/**
 * Provides some useful functions for Pages.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage util
 * @category Ultimate CMS
 */
class PageUtil {
    
    /**
     * Contains the cached objects.
     * @var array
     */
    protected static $objects = array();
    
    /**
     * Checks whether the given slug is available or not.
     *
     * @param string $slug
     * @return boolean
     */
    public static function isAvailableSlug($slug) {
        
        $isAvailable = true;
        foreach (self::$objects as $object) {
            if ($object->pageSlug != $slug) continue;
            $isAvailable = false;
            break;
        }
        
        return $isAvailable;
    }
    
    /**
     * Loads the cache.
     */
    protected static function loadCache() {
        $cache = 'page';
        $cacheBuilderClass = '\ultimate\system\cache\builder\UltimatePageCacheBuilder';
        $file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
        CacheHandler::getInstance()->addResource($cache, $file, $cacheBuilderClass);
        $objects = CacheHandler::getInstance()->get($cache);
        self::$objects = $objects['pages'];
    }
}
