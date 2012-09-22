<?php
namespace ultimate\data\content;
use wcf\system\tagging\ITagged;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;

/**
 * Represents a tagged content.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class TaggedContent extends TaggableContent implements ITagged {
	/**
	 * Contains the tags of this content.
	 * @var array[]
	 */
	protected $tags = array();
	
	/**
	 * Creates a new TaggedContent object.
	 * 
	 * @param \wcf\data\DatabaseObject $object
	 */
	public function __construct(\wcf\data\DatabaseObject $object) {
		parent::__construct($object);
		$this->tags = $this->getTags();
	}
	
	/**
	 * @see \wcf\data\DatabaseObjectDecorator::__get()
	 */
	public function __get($name) {
		$value = parent::__get($name);
		
		if ($value === null) {
			if ($name == 'tags') $value = $this->tags;
		}
		return $value;
	}
	
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
		return $this;
	}
	
	/**
	 * Returns the tags of this content.
	 *
	 * @return array[]
	 */
	protected function getTags() {
		$languages = WCF::getLanguage()->getLanguages();
		$tags = array();
		foreach ($languages as $languageID => $language) {
			/* @var $language \wcf\data\language\Language */
			$tmpTags = TagEngine::getInstance()->getObjectTags(
				'de.plugins-zum-selberbauen.ultimate.contentTaggable',
				$this->__get('contentID'),
				$languageID
			);
			if (!empty($tmpTags)) $tags[$languageID] = $tmpTags;
		}
		return $tags;
	}
}
