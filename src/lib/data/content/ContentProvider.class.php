<?php
namespace ultimate\data\content;
use wcf\data\object\type\AbstractObjectTypeProvider;

/**
 * Provides contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class ContentProvider extends AbstractObjectTypeProvider {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.object.type.AbstractObjectTypeProvider.html#$className
	 */
	public $className = '\ultimate\data\content\Content';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.object.type.AbstractObjectTypeProvider.html#$listClassName
	 */
	public $listClassName = '\ultimate\data\content\ContentList';
}
