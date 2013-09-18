<?php
/**
 * Contains the SearchResultContent class.
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use wcf\data\search\ISearchResultObject;
use wcf\data\user\UserProfile;
use wcf\system\request\UltimateLinkHandler;
use wcf\system\search\SearchResultTextParser;

/**
 * Represents a content in a search result list.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class SearchResultContent extends CategorizedContent implements ISearchResultObject {
	/**
	 * @see	\ultimate\data\content\Content::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		return SearchResultTextParser::getInstance()->parse($this->getDecoratedObject()->getSimplifiedFormattedMessage());
	}
	
	/**
	 * @see	\ultimate\data\content\Content::getLangTitle()
	 */
	public function getSubject() {
		return $this->getLangTitle();
	}
	
	/**
	 * @see	\ultimate\data\content\Content::getLink()
	 */
	public function getLink($query = '') {
		$parameters = array(
			'application' => 'ultimate',
			'date' => $this->publishDateObject->format('Y-m-d'),
			'contentSlug' => $this->contentSlug
		);
		
		if ($query) {
			$parameters['highlight'] = urlencode($query);
		}
			
		return UltimateLinkHandler::getInstance()->getLink(null, $parameters);
	}
	
	/**
	 * @see	\ultimate\data\content\Content::getTime()
	 */
	public function getTime() {
		return $this->getDecoratedObject()->getTime();
	}
	
	/**
	 * Returns author's user profile.
	 * 
	 * @return	\wcf\data\user\UserProfile
	 */
	public function getUserProfile() {
		return new UserProfile($this->author);
	}
	
	/**
	 * Returns the object type name.
	 * 
	 * @return	string
	 */
	public function getObjectTypeName() {
		return 'de.plugins-zum-selberbauen.ultimate.content';
	}
	
	/**
	 * Returns the title of object's container.
	 * 
	 * @return	string
	 */
	public function getContainerTitle() {
		return '';
	}
	
	/**
	 * Returns the link to object's container.
	 * 
	 * @return	string
	 */
	public function getContainerLink() {
		return '';
	}
}
