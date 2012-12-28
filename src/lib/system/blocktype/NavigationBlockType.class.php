<?php
/**
 * Contains the navigation block type class.
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
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
namespace ultimate\system\blocktype;
use wcf\system\WCF;

/**
 * Represents the navigation block type.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
class NavigationBlockType extends AbstractBlockType {
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$templateName
	 */
	protected $templateName = 'navigationBlockType';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$useTemplate
	 */
	protected $useTemplate = false;
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheName
	 */
	protected $cacheName = 'menu-to-block';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheBuilderClassName
	 */
	protected $cacheBuilderClassName = '\ultimate\system\cache\builder\MenuBlockCacheBuilder';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheIndex
	 */
	protected $cacheIndex = 'menusToBlockID';
	
	/**
	 * @see \ultimate\system\blocktype\IBlockType::readData()
	 */
	public function readData() {
		parent::readData();
		$menu = $this->objects[$this->blockID];
		WCF::getCustomMenu()->buildMenu($menu);
	}
}
