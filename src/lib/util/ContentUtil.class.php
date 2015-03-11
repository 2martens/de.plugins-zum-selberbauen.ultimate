<?php
/**
 * Contains the ContentUtil class.
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
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	util
 * @category	Ultimate CMS
 */
namespace ultimate\util;
use wcf\util\StringUtil;

/**
 * Provides some useful functions for Contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
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
	 * @param	string	$cacheBuilderClass
	 * @param	string	$cacheIndex
	 * @return	\ultimate\data\content\Content[]
	 */
	protected static function loadCache($cacheBuilderClass, $cacheIndex) {
		$instance = call_user_func($cacheBuilderClass.'::getInstance');
		return $instance->getData(array(), $cacheIndex);
	}
	
	/**
	 * Constructor not supported.
	 */
	private function __construct() {
	}
}
