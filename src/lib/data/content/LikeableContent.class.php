<?php
/**
 * Contains the LikeableContent class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use wcf\data\like\object\AbstractLikeObject;
use wcf\system\request\UltimateLinkHandler;

/**
 * Likeable object implementation for contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class LikeableContent extends AbstractLikeObject {
	/**
	 * The base class.
	 * @var	string
	 */
	protected static $baseClass = 'ultimate\data\content\Content';
	
	/**
	 * Returns the language interpreted title of the content.
	 * 
	 * @return	string
	 */
	public function getTitle() {
		return $this->__call('getTitle', array());
	}
	
	/**
	 * Returns the link to the content.
	 * 
	 * @return	string
	 */
	public function getURL() {
		return UltimateLinkHandler::getInstance()->getLink(null, array(
			'application' => 'ultimate', 
			'date' => $this->__get('publishDateObject')->format('Y-m-d'),
			'contentSlug' => $this->__get('contentSlug')
		));
	}
	
	/**
	 * Returns the author id of the content.
	 * 
	 * @return	integer
	 */
	public function getUserID() {
		return $this->__get('authorID');	
	}
		
	/**
	 * Returns the content id of the content.
	 * 
	 * @return	integer
	 */
	public function getObjectID() {
		return $this->__get('contentID');
	}
	
	/**
	 * Updates the cumulative like counter.
	 * 
	 * @param	integer	$cumulativeLikes
	 */
	public function updateLikeCounter($cumulativeLikes) {
		// update cumulative likes
		$contentEditor = new ContentEditor($this->getDecoratedObject());
		$contentEditor->update(array(
			'cumulativeLikes' => $cumulativeLikes
		));
	}
}
