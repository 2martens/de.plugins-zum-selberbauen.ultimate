<?php
namespace ultimate\util;
use wcf\system\cache\CacheHandler;
use wcf\util\StringUtil;

/**
 * Provides some useful functions for Pages.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	util
 * @category	Ultimate CMS
 */
class PageUtil {
	/**
	 * Checks whether the given title is available or not.
	 * 
	 * @param	string	$pageTitle
	 * @param	integer	$pageParent
	 * @return	boolean	$isAvailable
	 */
	public static function isAvailableTitle($pageTitle, $pageParent = 0) {
		$pageTitle = StringUtil::trim($pageTitle);
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
				if ($page->__get('pageTitle') != $pageTitle) continue;
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
				if ($page->__get('pageTitle') != $pageTitle) continue;
				$isAvailable = false;
				break;
			}
		}
		return $isAvailable;
	}
	
	/**
	 * Checks whether the given slug is available or not.
	 *
	 * @param	string	$pageSlug
	 * @param	integer	$pageParent
	 * @return	boolean	$isAvailable
	 */
	public static function isAvailableSlug($pageSlug, $pageParent = 0) {
		$pageSlug = StringUtil::trim($pageSlug);
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
				if ($page->__get('pageSlug') != $pageSlug) continue;
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
				if ($page->__get('pageSlug') != $pageSlug) continue;
				$isAvailable = false;
				break;
			}
		}
		return $isAvailable;
	}
	
	/**
	 * Returns all pages which are available.
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
			$returnPage = self::getRealPage($page, ++$i);
		}
		return $returnPage;
	}
	
	/**
	 * Returns all contents which are available.
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
