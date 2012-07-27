<?php
namespace ultimate\util;
use wcf\system\cache\CacheHandler;

/**
 * Provides category-related functions.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage util
 * @category Ultimate CMS
 */
class CategoryUtil {
    
    /**
     * Checks if the given title is available.
     *
     * @param  string  $categoryTitle
     * @param  integer $categoryParent
     *
     * @return boolean $isAvailable
     */
    public static function isAvailableTitle($categoryTitle, $categoryParent) {
        $categoryTitle = trim($categoryTitle);
        $categoryParent = intval($categoryParent);
        $isAvailable = true;
        if ($categoryParent) {
            $categories = self::loadCache(
                'category',
                '\ultimate\system\cache\builder\UltimateCategoryCacheBuilder',
                'categoriesToParent'
            );
        
            $relevantCategories = $categories[$categoryParent];
            foreach ($relevantCategories as $categoryID => $category) {
                if ($category->categoryTitle != $categoryTitle) continue;
                $isAvailable = false;
                break;
            }
        }
        else {
            $categories = self::loadCache(
                    'category',
                    '\ultimate\system\cache\builder\UltimateCategoryCacheBuilder',
                    'categories'
            );
            foreach ($categories as $categoryID => $category) {
                if ($category->categoryTitle != $categoryTitle) continue;
                $isAvailable = false;
                break;
            }
        }
        return $isAvailable;
    }
    
    /**
     * Checks if the given slug is available.
     *
     * @param  string  $categorySlug
     * @param  integer $categoryParent
     *
     * @return boolean $isAvailable
     */
    public static function isAvailableSlug($categorySlug, $categoryParent) {
        $categorySlug = trim($categorySlug);
        $categoryParent = intval($categoryParent);
        $isAvailable = true;
        if ($categoryParent) {
            $categories = self::loadCache(
                'category',
                '\ultimate\system\cache\builder\UltimateCategoryCacheBuilder',
                'categoriesToParent'
            );
        
            $relevantCategories = $categories[$categoryParent];
            foreach ($relevantCategories as $categoryID => $category) {
                if ($category->categorySlug != $categorySlug) continue;
                $isAvailable = false;
                break;
            }
        }
        else {
            $categories = self::loadCache(
                    'category',
                    '\ultimate\system\cache\builder\UltimateCategoryCacheBuilder',
                    'categories'
            );
            foreach ($categories as $categoryID => $category) {
                if ($category->categorySlug != $categorySlug) continue;
                $isAvailable = false;
                break;
            }
        }
        return $isAvailable;
    }
    
    /**
     * Loads the cache.
     *
     * @return array  $objects
     */
    protected static function loadCache($cache, $cacheBuilderClass, $cacheIndex) {
        $file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
        CacheHandler::getInstance()->addResource($cache, $file, $cacheBuilderClass);
        return CacheHandler::getInstance()->get($cache, $cacheIndex);
    }
    
    /**
     * no constructor supported
     */
    private function __construct() {}
}