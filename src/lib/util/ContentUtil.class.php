<?php
namespace ultimate\util;
use wcf\system\cache\CacheHandler;
use wcf\util\StringUtil;

/**
 * Provides some useful functions for Contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	util
 * @category	Ultimate CMS
 */
class ContentUtil {
	/**
	 * Checks whether the given slug is available or not.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$contentSlug
	 * @param	integer	$contentID
	 * @return	boolean	$isAvailable
	 */
	public static function isAvailableSlug($contentSlug, $contentID) {
		$contentSlug = StringUtil::trim($contentSlug);
		$contentID = intval($contentID);
		$isAvailable = true;
		
		$contents = self::loadCache(
			'content',
			'\ultimate\system\cache\builder\ContentCacheBuilder',
			'contents'
		);
		
		foreach ($contents as $content) {
			/* @var $page \ultimate\data\content\Content */
			if ($content->__get('contentID') == $contentID || $content->__get('contentSlug') != $contentSlug) continue;
			$isAvailable = false;
			break;
		}
		
		return $isAvailable;
	}
	
	/**
	 * Loads the cache.
	 * 
	 * @return	\ultimate\data\content\Content[]
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
