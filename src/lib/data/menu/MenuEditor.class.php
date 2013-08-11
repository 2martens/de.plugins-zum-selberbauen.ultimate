<?php
/**
 * Contains the menu data model editor class.
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
 * @subpackage	data.menu
 * @category	Ultimate CMS
 */
namespace ultimate\data\menu;
use ultimate\system\cache\builder\MenuCacheBuilder;
use ultimate\system\cache\builder\MenuTemplateCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\clipboard\ClipboardHandler;

/**
 * Provides functions to edit menus.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.menu
 * @category	Ultimate CMS
 */
class MenuEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * The base class.
	 * @var	string
	 */
	protected static $baseClass = '\ultimate\data\menu\Menu';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableObject.html#deleteAll
	 */
	public static function deleteAll(array $objectIDs = array()) {
		// unmark contents
		ClipboardHandler::getInstance()->unmark($objectIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.menu'));
		
		return parent::deleteAll($objectIDs);
	}
	
	/**
	 * Resets the cache.
	 */
	public static function resetCache() {
		MenuCacheBuilder::getInstance()->reset();
		MenuTemplateCacheBuilder::getInstance()->reset();
	}
}
