<?php
namespace ultimate\util;
use wcf\system\cache\CacheHandler;
use wcf\util\StringUtil;

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
     * Returns all categories which are available.
     *
     * @param integer $categoryID 0 by default; give category id to get all categories except the one belonging to the given category id
     * @return \ultimate\data\category\Category[]
     */
    public static function getAvailableCategories($categoryID = 0) {
        $categoryID = intval($categoryID);
        $categories = self::loadCache(
            'category',
            '\ultimate\system\cache\builder\CategoryCacheBuilder',
            'categories'
        );
        
        if ($categoryID) unset($categories[$categoryID]);
        
        return $categories;
    }
    
    /**
     * Checks if the given title is available.
     *
     * @param  string  $categoryTitle
     * @param  integer $categoryParent
     * @return boolean $isAvailable
     */
    public static function isAvailableTitle($categoryTitle, $categoryParent = 0) {
        $categoryTitle = StringUtil::trim($categoryTitle);
        $categoryParent = intval($categoryParent);
        $isAvailable = true;
        if ($categoryParent) {
            $categories = self::loadCache(
                'category',
                '\ultimate\system\cache\builder\CategoryCacheBuilder',
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
                '\ultimate\system\cache\builder\CategoryCacheBuilder',
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
     * @return boolean $isAvailable
     */
    public static function isAvailableSlug($categorySlug, $categoryParent = 0) {
        $categorySlug = StringUtil::trim($categorySlug);
        $categoryParent = intval($categoryParent);
        $isAvailable = true;
        if ($categoryParent) {
            $categories = self::loadCache(
                'category',
                '\ultimate\system\cache\builder\CategoryCacheBuilder',
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
                    '\ultimate\system\cache\builder\CategoryCacheBuilder',
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
     * @return \ultimate\data\category\Category[]
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