<?php
/**
 * Contains the taggable content data model class.
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
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use wcf\data\tag\Tag;
use wcf\system\tagging\ITaggable;

/**
 * Represents a taggable content.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class TaggableContent extends CategorizedContent implements ITaggable {
	/**
	 * Returns the application of the result template.
	 * 
	 * @return	string
	 */
	public function getApplication() {
		return 'ultimate';
	}
	
	/**
	 * Returns the template name for the result output.
	 *
	 * @return	string
	 */
	public function getTemplateName() {
		return 'searchResultContentList';
	}
	
	/**
	 * Returns a list of tagged objects.
	 *
	 * @param	\wcf\data\tag\Tag	$tag
	 * 
	 * @return	\wcf\data\DatabaseObjectList
	 */
	public function getObjectList(Tag $tag) {
		return new TaggedContentList($tag);
	}
}
