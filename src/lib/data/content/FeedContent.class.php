<?php
/**
 * Contains the FeedContent class.
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
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use wcf\data\IFeedEntry;
use wcf\system\comment\CommentHandler;
use wcf\system\request\LinkHandler;

/**
 * Represents a viewable content for rss feeds.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class FeedContent extends CategorizedContent implements IFeedEntry {
	/**
	 * Returns the link to the content.
	 * 
	 * @return	string
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink(null, array(
			'application' => 'ultimate',
			'date' => $this->__get('publishDateObject')->format('Y-m-d'),
			'contentslug' => $this->__get('contentSlug'),
			'appendSession' => false,
			'encodeTitle' => true
		));
	}
	
	/**
	 * Returns the language interpreted title of the content.
	 * 
	 * @return	string
	 */
	public function getTitle() {
		return $this->getDecoratedObject()->getTitle();
	}
	
	/**
	 * Returns the formatted message of the content.
	 * 
	 * @return	string
	 */
	public function getFormattedMessage() {
		return $this->getDecoratedObject()->getFormattedMessage();
	}
	
	/**
	 * Returns the plain message of the content.
	 * 
	 * @return	string
	 */
	public function getMessage() {
		return $this->getDecoratedObject()->getMessage();
	}
	
	/**
	 * Returns an excerpt of the content.
	 * 
	 * @param	integer	$maxLength	default: 255
	 * 
	 * @return	string
	 */
	public function getExcerpt($maxLength = 255) {
		return $this->getDecoratedObject()->getExcerpt($maxLength);
	}
	
	/**
	 * Returns the author id of the content.
	 * 
	 * @return	integer
	 */
	public function getUserID() {
		return $this->getDecoratedObject()->getUserID();
	}
	
	/**
	 * Returns the username of the author of the content.
	 * 
	 * @return	string
	 */
	public function getUsername() {
		return $this->getDecoratedObject()->getUsername();
	}
	
	/**
	 * Returns the publish date of the content.
	 * 
	 * @return	integer
	 */
	public function getTime() {
		return $this->getDecoratedObject()->getTime();
	}
	
	/**
	 * Returns the formatted message of the content.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return $this->getDecoratedObject()->__toString();
	}
	
	/**
	 * Returns the amount of comments for the content.
	 * 
	 * @return	integer
	 */
	public function getComments() {
		$objectTypeID = CommentHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.content.comment');
		/* @var $objectType \wcf\data\object\type\ObjectType */
		$objectType = CommentHandler::getInstance()->getObjectType($objectTypeID);
		$commentManager = $objectType->getProcessor();
		$commentList = CommentHandler::getInstance()->getCommentList($commentManager, $objectTypeID, $this->__get('contentID'));
		/* @var $commentList \wcf\data\comment\CommentList */
		return $commentList->countObjects();
	}
	
	/**
	 * Returns the categories, the content is in.
	 * 
	 * @return	\ultimate\data\category\Category[]	(categoryID => category)
	 */
	public function getCategories() {
		return $this->categories;
	}
	
	/**
	 * Returns if the content is visible.
	 * 
	 * @return	boolean	true if the content is visible
	 */
	public function isVisible() {
		return $this->getDecoratedObject()->isVisible();
	}
}
