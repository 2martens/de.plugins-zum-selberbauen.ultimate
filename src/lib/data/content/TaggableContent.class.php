<?php
namespace ultimate\data\content;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\tagging\ITaggable;

/**
 * Represents a taggable content.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
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
