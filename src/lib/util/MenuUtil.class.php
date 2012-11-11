<?php
namespace ultimate\util;
use wcf\system\cache\CacheHandler;
use wcf\util\StringUtil;

/**
 * Provides useful functions for menus.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	util
 * @category	Ultimate CMS
 */
class MenuUtil {
	/**
	 * Checks whether the given name is available or not.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$menuName
	 * @param	integer	$menuID
	 * @return	boolean	$isAvailable
	 */
	public static function isAvailableName($menuName, $menuID) {
		$menuName = StringUtil::trim($menuName);
		$menuID = intval($menuID);
		$isAvailable = true;
		
		$menus = self::loadCache(
			'menu',
			'\ultimate\system\cache\builder\MenuCacheBuilder',
			'menus'
		);
		
		foreach ($menus as $menu) {
			/* @var $menu \ultimate\data\menu\Menu */
			if ($menu->__get('menuName') != $menuName || $menu->__get('menuID') == $menuID) continue;
			$isAvailable = false;
			break;
		}
		
		return $isAvailable;
	}
	
	/**
	 * Loads the cache.
	 * 
	 * @return	\ultimate\data\menu\Menu[]
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