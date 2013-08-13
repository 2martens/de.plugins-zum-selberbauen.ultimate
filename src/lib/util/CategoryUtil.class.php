<?php
/**
 * Contains the CategoryUtil class.
 * 
 * LICENSE:
 * This file is part of the Ultimate CMS.
 *
 * The Ultimate CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * The Ultimate CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	util
 * @category	Ultimate CMS
 */
namespace ultimate\util;
use ultimate\data\category\Category;
use wcf\util\StringUtil;

/**
 * Provides category-related functions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	util
 * @category	Ultimate CMS
 */
class CategoryUtil {
	/**
	 * Returns all categories which are available.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	integer	$categoryID	0 by default; give category id to get all categories except the one belonging to the given category id
	 * @return	\ultimate\data\category\Category[]
	 */
	public static function getAvailableCategories($categoryID = 0) {
		$categoryID = intval($categoryID);
		$categories = self::loadCache(
			'\ultimate\system\cache\builder\CategoryCacheBuilder',
			'categories'
		);
		
		if ($categoryID) unset($categories[$categoryID]);
		
		return $categories;
	}
	
	/**
	 * Checks if the given title is available.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$categoryTitle
	 * @param	integer	$categoryID
	 * @param	integer	$categoryParent	optional
	 * @return	boolean	$isAvailable
	 */
	public static function isAvailableTitle($categoryTitle, $categoryID, $categoryParent = 0) {
		$categoryTitle = StringUtil::trim($categoryTitle);
		$categoryID = intval($categoryID);
		$categoryParent = intval($categoryParent);
		$isAvailable = true;
		if ($categoryParent) {
			$categories = self::loadCache(
				'\ultimate\system\cache\builder\CategoryCacheBuilder',
				'categoriesToParent'
			);
			
			$relevantCategories = $categories[$categoryParent];
			foreach ($relevantCategories as $__categoryID => $category) {
				if ($__categoryID == $categoryID || $category->__get('categoryTitle') != $categoryTitle) continue;
				$isAvailable = false;
				break;
			}
		}
		else {
			$categories = self::loadCache(
				'\ultimate\system\cache\builder\CategoryCacheBuilder',
				'categories'
			);
			foreach ($categories as $__categoryID => $category) {
				if ($__categoryID == $categoryID || $category->__get('categoryTitle') != $categoryTitle) continue;
				$isAvailable = false;
				break;
			}
		}
		return $isAvailable;
	}
	
	/**
	 * Checks if the given slug is available.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$categorySlug
	 * @param	integer	$categoryID
	 * @param	integer	$categoryParent	optional
	 * @return	boolean	$isAvailable
	 */
	public static function isAvailableSlug($categorySlug, $categoryID, $categoryParent = 0) {
		$categorySlug = StringUtil::trim($categorySlug);
		$categoryID = intval($categoryID);
		$categoryParent = intval($categoryParent);
		$isAvailable = true;
		if ($categoryParent) {
			$categories = self::loadCache(
				'\ultimate\system\cache\builder\CategoryCacheBuilder',
				'categoriesToParent'
			);
			
			$relevantCategories = $categories[$categoryParent];
			foreach ($relevantCategories as $__categoryID => $category) {
				if ($__categoryID == $categoryID || $category->__get('categorySlug') != $categorySlug) continue;
				$isAvailable = false;
				break;
			}
		}
		else {
			$categories = self::loadCache(
					'\ultimate\system\cache\builder\CategoryCacheBuilder',
					'categories'
			);
			foreach ($categories as $__categoryID => $category) {
				if ($__categoryID == $categoryID || $category->__get('categorySlug') != $categorySlug) continue;
				$isAvailable = false;
				break;
			}
		}
		return $isAvailable;
	}
	
	/**
	 * Returns the real category for a hierarchy of category slugs.
	 *
	 * @since	1.0.0
	 * @api
	 *
	 * @param	\ultimate\data\category\Category	$category
	 * @param	integer					 			$i
	 * @param	string[]				 			$categorySlugs
	 * @return	\ultimate\data\category\Category
	 */
	public static function getRealCategory(Category $category, $i = 1, $categorySlugs) {
		$childCategories = $category->__get('childCategories');
		$maxI = count($categorySlugs) - 1;
		/* @var $returnCategory \ultimate\data\category\Category|null */
		$returnCategory = null;
		foreach ($childCategories as $categoryID => $category) {
			if ($category->__get('categorySlug') != $categorySlugs[$i]) continue;
			if ($i == $maxI) {
				$returnCategory = $category;
				break;
			}
			$returnCategory = self::getRealCategory($category, ++$i, $categorySlugs);
		}
		return $returnCategory;
	}
	
	/**
	 * Loads the cache.
	 * 
	 * @param	string	$cacheBuilderClass
	 * @param	string	$cacheIndex
	 * @return	\ultimate\data\category\Category[]
	 */
	protected static function loadCache($cacheBuilderClass, $cacheIndex) {
		$instance = call_user_func($cacheBuilderClass.'::getInstance');
		return $instance->getData(array(), $cacheIndex);
	}
	
	/**
	 * no constructor supported
	 */
	private function __construct() {}
}
