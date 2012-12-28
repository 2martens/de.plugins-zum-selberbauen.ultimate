<?php
/**
 * Contains the MenuBlockCacheBuilder class.
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
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use wcf\system\WCF;

use ultimate\data\menu\MenuList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the menu block relation.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
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
