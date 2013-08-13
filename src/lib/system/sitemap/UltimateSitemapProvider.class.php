<?php
/**
 * Contains the UltimateSitemapProvider class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.sitemap
 * @category	Ultimate CMS
 */
namespace ultimate\system\sitemap;
use ultimate\system\cache\builder\CurrentMenuCacheBuilder;
use wcf\system\sitemap\ISitemapProvider;
use wcf\system\WCF;

/**
 * Provides a sitemap for the Ultimate CMS.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.sitemap
 * @category	Ultimate CMS
 */
class UltimateSitemapProvider implements ISitemapProvider {
	/**
	 * @see	wcf\system\sitemap\ISitemapProvider::getTemplate()
	 */
	public function getTemplate() {
		$menuItems = CurrentMenuCacheBuilder::getInstance()->getData(array(), 'currentMenuItems');
		$menuItems = $menuItems[''];
		$remainingItems = array();
		foreach ($menuItems as $menuItemID =>$menuItem) {
			if ($menuItem->__get('menuItemController') === null) {
				$remainingItems[$menuItemID] = $menuItem;
			}
		}
		
		WCF::getTPL()->assign('menuItems', $remainingItems);
		
		return WCF::getTPL()->fetch('sitemapUltimate', 'ultimate');
	}
}
