<?php
namespace ultimate\data\content;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\tagging\ITaggable;

/**
 * Represents a taggable content.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class TaggableContent extends CategorizedContent implements ITaggable {
	/**
	 * @see \wcf\system\tagging\ITaggable::getObjectTypeID()
	 */
	public function getObjectTypeID() {
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.tagging.taggableObject', 'de.plugins-zum-selberbauen.ultimate.contentTaggable');
		return $objectType->__get('objectTypeID');
	}
}
