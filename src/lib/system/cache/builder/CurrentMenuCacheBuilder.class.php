<?php
/**
 * Contains the CurrentMenuCacheBuilder class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\system\menu\custom\CustomMenu;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the current menu (last built menu of CustomMenu).
 * 
 * Used to get the menuItems of the last built menu (for AJAX requests).
 * 
 * Provides two variables:
 * 
 * * \ultimate\data\menu\item\MenuItem[][] currentMenuItems (parentName => (childItemID => childItem)) 
 * * \ultimate\data\menu\item\MenuItem[] activeMenuItems (level => menuItem)
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class CurrentMenuCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.ICacheBuilder.html#rebuild
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'currentMenuItems' => array(),
			'activeMenuItems' => array()
		);
	
		$currentMenuItems = CustomMenu::getInstance()->getMenuItems();
		$activeMenuItems = CustomMenu::getInstance()->getActiveMenuItems();
		
		if ($currentMenuItems === null) return $data;
		
		$data['currentMenuItems'] = $currentMenuItems;
		$data['activeMenuItems'] = $activeMenuItems;
		return $data;
	}
}
