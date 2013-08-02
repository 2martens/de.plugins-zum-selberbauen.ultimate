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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
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
 * @copyright	2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class LikeableContent extends AbstractLikeObject {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'ultimate\data\content\Content';
	
	/**
	 * @see	\wcf\data\like\object\ILikeObject::getTitle()
	 */
	public function getTitle() {
		return $this->__call('getLangTitle', array());
	}
	
	/**
	 * @see	\wcf\data\like\object\ILikeObject::getURL()
	 */
	public function getURL() {
		return UltimateLinkHandler::getInstance()->getLink(null, array(
			'application' => 'ultimate', 
			'date' => $this->__get('publishDateObject')->format('Y-m-d'),
			'contentSlug' => $this->__get('contentSlug')
		));
	}
	
	/**
	 * @see	\wcf\data\like\object\ILikeObject::getUserID()
	 */
	public function getUserID() {
		return $this->__get('authorID');	
	}
		
	/**
	 * @see	\wcf\data\like\object\ILikeObject::getObjectID()
	 */
	public function getObjectID() {
		return $this->__get('contentID');
	}
	
	/**
	 * @see	\wcf\data\like\object\ILikeObject::updateLikeCounter()
	 */
	public function updateLikeCounter($cumulativeLikes) {
		// update cumulative likes
		$contentEditor = new ContentEditor($this->getDecoratedObject());
		$contentEditor->update(array(
			'cumulativeLikes' => $cumulativeLikes
		));
	}
}
