<?php
namespace ultimate\data\menu;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of menus.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.menu
 * @category	Ultimate CMS
 */
class MenuList extends DatabaseObjectList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$className
	 */
	public $className = '\ultimate\data\menu\Menu';
}
