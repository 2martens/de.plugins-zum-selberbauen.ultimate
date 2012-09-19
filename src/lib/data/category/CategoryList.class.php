<?php
namespace ultimate\data\category;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of categories.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.category
 * @category	Ultimate CMS
 */
class CategoryList extends DatabaseObjectList {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObjectList.html#$className
	 */
	public $className = '\ultimate\data\category\Category';
}
