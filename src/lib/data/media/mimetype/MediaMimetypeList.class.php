<?php
namespace ultimate\data\media\mimetype;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of MIME types.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.media.mimetype
 * @category	Ultimate CMS
 */
class MediaMimetypeList extends DatabaseObjectList {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObjectList.html#$className
	 */
	public $className = '\ultimate\data\media\mimetype\Mimetype';
}
