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
     * Checks whether the given slug is available or not.
     *
     * @param   string    $title
     * @return  boolean   $isAvailable
     */
    public static function isAvailableTitle($title) {
        $title = trim($title);
        $objects = self::loadCache(
                'page',
                '\ultimate\system\cache\builder\UltimatePageCacheBuilder',
                'pages'
        );
        $isAvailable = true;
        foreach ($objects as $object) {
            if ($object->pageTitle != $title) continue;
            $isAvailable = false;
            break;
        }
    
        return $isAvailable;
    }
    
    /**
     * Checks whether the given slug is available or not.
     *
     * @param   string    $slug
     * @return  boolean   $isAvailable
     */
    public static function isAvailableSlug($slug) {
        $slug = trim($slug);
        $objects = self::loadCache(
            'page',
            '\ultimate\system\cache\builder\UltimatePageCacheBuilder',
            'pages'
        );
        $isAvailable = true;
        foreach ($objects as $object) {
            if ($object->pageSlug != $slug) continue;
            $isAvailable = false;
            break;
        }
        
        return $isAvailable;
    }
    
    /**
     * Returns all contents which are available.
     *
     * @param  int      $pageID    0 by default; give page id to get all unbound contents plus the one already in use by the page
     * @return array    $contents
     */
    public static function getAvailableContents($pageID = 0) {
        $contents = self::loadCache(
            'content',
            '\ultimate\system\cache\builder\UltimateContentCacheBuilder',
            'contents'
        );
        $unavailableContentIDs = self::loadCache(
            'content-to-page',
            '\ultimate\system\cache\builder\UltimateContentPageCacheBuilder',
            'contentIDsToPageID'
        );
        
        foreach ($unavailableContentIDs as $key => $contentID) {
            if ($pageID == $key) continue;
            unset($contents[$contentID]);
        }
        
        return $contents;
    }
    
    /**
     * Loads the cache.
     *
     * @return array    $objects
     */
    protected static function loadCache($cache, $cacheBuilderClass, $cacheIndex) {
        $file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
        CacheHandler::getInstance()->addResource($cache, $file, $cacheBuilderClass);
        $objects = CacheHandler::getInstance()->get($cache);
        return $objects[$cacheIndex];
    }
    
    /**
     * Constructor not supported.
     */
    private function __construct() {}
}
