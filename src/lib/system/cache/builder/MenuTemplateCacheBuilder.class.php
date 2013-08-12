<?php
/**
 * Contains the MenuTemplateCacheBuilder class.
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\menu\MenuList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches the menu template relation.
 * 
 * Provides one variables:
 * * \ultimate\data\menu\Menu[] menusToTemplateID (templateID => menu)
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class MenuTemplateCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 * 
	 * @param	array	$parameters
	 * 
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'menusToTemplateID' => array()
		);
		
		$menuList = new MenuList();
		$menuList->readObjects();
		$menus = $menuList->getObjects();
		
		if (empty($menus)) return $data;
		
		$sql = 'SELECT menuID, templateID
		        FROM   ultimate'.WCF_N.'_menu_to_template';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		while ($row = $statement->fetchArray()) {
			$data['menusToTemplateID'][intval($row['templateID'])] = $menus[intval($row['menuID'])];
		}
		
		return $data;
	}
}
