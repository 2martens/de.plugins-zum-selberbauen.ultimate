<?php
namespace ultimate\data\content;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\tagging\ITagged;

/**
 * Represents a tagged content.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class TaggedContent extends DatabaseObjectDecorator implements ITagged {
	/**
	 * @see \wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = '\ultimate\data\content\Content';
	
	/**
	 * @see \wcf\system\tagging\ITagged::getObjectID()
	 */
	public function getObjectID() {
		return $this->__get('contentID');
	}
	
	/**
	 * @see \wcf\system\tagging\ITagged::getTaggable()
	 */
	public function getTaggable() {
		return $this->object;
	}
}
