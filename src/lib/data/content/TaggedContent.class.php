<?php
/**
 * Contains the tagged content data model class.
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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use wcf\data\DatabaseObject;
use wcf\system\tagging\ITagged;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;

/**
 * Represents a tagged content.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 * 
 * @property-read	\wcf\data\tag\Tag[][]	$tags	(languageID => (tagID => tag))
 */
class TaggedContent extends TaggableContent implements ITagged {
	/**
	 * The tags of this content.
	 * @var array[]
	 */
	protected $tags = array();
	
	/**
	 * Creates a new TaggedContent object.
	 * 
	 * @param \wcf\data\DatabaseObject $object
	 */
	public function __construct(DatabaseObject $object) {
		parent::__construct($object);
		$this->tags = $this->getTags();
	}
	
	/**
	 * Returns the value of a object data variable with the given name.
	 * 
	 * @param	string	$name
	 * @return	mixed
	 */
	public function __get($name) {
		$value = parent::__get($name);
		
		if ($value === null) {
			if ($name == 'tags') $value = $this->tags;
		}
		return $value;
	}
	
	/**
	 * Returns the content id.
	 * 
	 * @return	integer
	 */
	public function getObjectID() {
		return $this->__get('contentID');
	}
	
	/**
	 * Returns the taggable object.
	 * 
	 * @return	self
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
				'de.plugins-zum-selberbauen.ultimate.content',
				$this->__get('contentID'),
				array($languageID)
			);
			$tags[$languageID] = $tmpTags;
		}
		return $tags;
	}
}
