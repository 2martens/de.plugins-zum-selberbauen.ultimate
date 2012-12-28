<?php
/**
 * Contains the PageUtil class.
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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	util
 * @category	Ultimate CMS
 */
namespace ultimate\util;
use wcf\system\cache\CacheHandler;
use wcf\util\StringUtil;

/**
 * Provides some useful functions for Pages.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	util
 * @category	Ultimate CMS
 */
class PageUtil {
	/**
	 * Checks whether the given title is available or not.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$pageTitle
	 * @param	integer	$pageID
	 * @param	integer	$pageParent	optional
	 * @return	boolean	$isAvailable
	 */
	public static function isAvailableTitle($pageTitle, $pageID, $pageParent = 0) {
		$pageTitle = StringUtil::trim($pageTitle);
		$pageID = intval($pageID);
		$pageParent = intval($pageParent);
		$isAvailable = true;
		
		if ($pageParent) {
			$pages = self::loadCache(
					'page',
					'\ultimate\system\cache\builder\PageCacheBuilder',
					'pagesToParent'
			);
			
			$relevantPages = $pages[$pageParent];
			foreach ($relevantPages as $page) {
				/* @var $page \ultimate\data\page\Page */
				if ($page->__get('pageID') == $pageID || $page->__get('pageTitle') != $pageTitle) continue;
				$isAvailable = false;
				break;
			}
		}
		else {
			$pages = self::loadCache(
				'page',
				'\ultimate\system\cache\builder\PageCacheBuilder',
				'pages'
			);
			
			foreach ($pages as $page) {
				/* @var $page \ultimate\data\page\Page */
				if ($page->__get('pageID') == $pageID || $page->__get('pageTitle') != $pageTitle) continue;
				$isAvailable = false;
				break;
			}
		}
		return $isAvailable;
	}
	
	/**
	 * Checks whether the given slug is available or not.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$pageSlug
	 * @param	integer	$pageID
	 * @param	integer	$pageParent	optional
	 * @return	boolean	$isAvailable
	 */
	public static function isAvailableSlug($pageSlug, $pageID, $pageParent = 0) {
		$pageSlug = StringUtil::trim($pageSlug);
		$pageID = intval($pageID);
		$pageParent = intval($pageParent);
		$isAvailable = true;
		
		if ($pageParent) {
			$pages = self::loadCache(
					'page',
					'\ultimate\system\cache\builder\PageCacheBuilder',
					'pagesToParent'
			);
			
			$relevantPages = $pages[$pageParent];
			foreach ($relevantPages as $page) {
				/* @var $page \ultimate\data\page\Page */
				if ($page->__get('pageID') == $pageID || $page->__get('pageSlug') != $pageSlug) continue;
				$isAvailable = false;
				break;
			}
		}
		else {
			$pages = self::loadCache(
				'page',
				'\ultimate\system\cache\builder\PageCacheBuilder',
				'pages'
			);
			
			foreach ($pages as $page) {
				/* @var $page \ultimate\data\page\Page */
				if ($page->__get('pageID') == $pageID || $page->__get('pageSlug') != $pageSlug) continue;
				$isAvailable = false;
				break;
			}
		}
		return $isAvailable;
	}
	
	/**
	 * Returns all pages which are available.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	integer	$pageID	0 by default; give page id to get all pages except the one belonging to the given page id
	 * @return	\ultimate\data\page\Page[]
	 */
	public static function getAvailablePages($pageID = 0) {
		$pages = self::loadCache(
			'page',
			'\ultimate\system\cache\builder\PageCacheBuilder',
			'pages'
		);
		
		if ($pageID) unset($pages[$pageID]);
		return $pages;
	}
	
	/**
	 * Returns the real page for a hierarchy of page slugs.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	\ultimate\data\page\Page $page
	 * @param	integer					 $i
	 * @param	string[]				 $pageSlugs
	 * @return	\ultimate\data\page\Page
	 */
	public static function getRealPage(\ultimate\data\page\Page $page, $i = 1, $pageSlugs) {
		$childPages = $page->__get('childPages');
		$maxI = count($pageSlugs) - 1;
		/* @var $returnPage \ultimate\data\page\Page */
		$returnPage = null;
		foreach ($childPages as $pageID => $page) {
			if ($page->__get('pageSlug') != $pageSlugs[$i]) continue;
			if ($i == $maxI) {
				$returnPage = $page;
				break;
			}
			$returnPage = self::getRealPage($page, ++$i, $pageSlugs);
		}
		return $returnPage;
	}
	
	/**
	 * Returns all contents which are available.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	integer	$pageID	0 by default; give page id to get all unbound contents plus the one already in use by the page
	 * @return	\ultimate\data\content\Content[]
	 */
	public static function getAvailableContents($pageID = 0) {
		$contents = self::loadCache(
			'content',
			'\ultimate\system\cache\builder\ContentCacheBuilder',
			'contents'
		);
		$unavailableContentIDs = self::loadCache(
			'content-to-page',
			'\ultimate\system\cache\builder\ContentPageCacheBuilder',
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
	 * @return	(\ultimate\data\content\Content|integer|\ultimate\data\page\Page)[]
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
