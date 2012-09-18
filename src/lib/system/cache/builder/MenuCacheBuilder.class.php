<?php
namespace ultimate\system\cache\builder;
use ultimate\data\menu\MenuList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the menus.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class MenuCacheBuilder implements ICacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.ICacheBuilder.html#getData
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'menus' => array(),
			'menuIDs' => array()
		);
		
		$menuList = new MenuList();
		$menuList->readObjects();
		$menus = $menuList->getObjects();
		$menuIDs = $menuList->getObjectIDs();
		
		if (empty($menus)) return $data;
		
		$data['menus'] = $menus;
		$data['menuIDs'] = $menuIDs;
		
		return $data;
	}
}
