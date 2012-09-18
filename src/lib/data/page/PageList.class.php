<?php
namespace ultimate\data\page;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of pages.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.page
 * @category	Ultimate CMS
 */
class PageList extends DatabaseObjectList {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObjectList.html#$className
	 */
	public $className = '\ultimate\data\page\Page';
}
