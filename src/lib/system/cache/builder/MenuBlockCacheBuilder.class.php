<?php
namespace ultimate\system\cache\builder;
use wcf\system\WCF;

use ultimate\data\menu\MenuList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the menu block relation.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class MenuBlockCacheBuilder implements ICacheBuilder {
	/**
	 * @see \wcf\system\cache\builder\ICacheBuilder::getData()
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'menusToBlockID' => array()
		);
		
		$menuList = new MenuList();
		$menuList->readObjects();
		$menus = $menuList->getObjects();
		
		if (empty($menus)) return $data;
		
		$sql = 'SELECT menuID, blockID
		        FROM   ultimate'.ULTIMATE_N.'_menu_to_block';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		while ($row = $statement->fetchArray()) {
			$data['menusToBlockID'][intval($row['blockID'])] = $menus[intval($row['menuID'])];
		}
		
		return $data;
	}
}
