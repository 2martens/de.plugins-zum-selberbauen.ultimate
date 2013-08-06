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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use wcf\data\IFeedEntry;
use wcf\system\comment\CommentHandler;
use wcf\system\request\UltimateLinkHandler;

/**
 * Represents a viewable content for rss feeds.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class FeedContent extends CategorizedContent implements IFeedEntry {
	/**
	 * @see	\wcf\data\ILinkableObject::getLink()
	 */
	public function getLink() {
		return UltimateLinkHandler::getInstance()->getLink(null, array(
			'application' => 'ultimate',
			'date' => $this->__get('publishDateObject')->format('Y-m-d'),
			'contentSlug' => $this->__get('contentSlug'),
			'appendSession' => false,
			'encodeTitle' => true
		));
	}
	
	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return $this->getDecoratedObject()->getLangTitle();
	}
	
	/**
	 * @see	\wcf\data\IMessage::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		return $this->getDecoratedObject()->getFormattedMessage();
	}
	
	/**
	 * @see	\wcf\data\IMessage::getMessage()
	 */
	public function getMessage() {
		return $this->getDecoratedObject()->getMessage();;
	}
	
	/**
	 * @see	\wcf\data\IMessage::getExcerpt()
	 */
	public function getExcerpt($maxLength = 255) {
		return $this->getDecoratedObject()->getExcerpt($maxLength);;
	}
	
	/**
	 * @see	\wcf\data\IMessage::getUserID()
	 */
	public function getUserID() {
		return $this->getDecoratedObject()->getUserID();
	}
	
	/**
	 * @see	\wcf\data\IMessage::getUsername()
	 */
	public function getUsername() {
		return $this->getDecoratedObject()->getUsername();
	}
	
	/**
	 * @see	\wcf\data\IMessage::getTime()
	 */
	public function getTime() {
		return $this->getDecoratedObject()->getTime();
	}
	
	/**
	 * @see	\wcf\data\IMessage::__toString()
	 */
	public function __toString() {
		return $this->getDecoratedObject()->__toString();
	}
	
	/**
	 * @see	\wcf\data\IFeedEntry::getComments()
	 */
	public function getComments() {
		$objectTypeID = CommentHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.content.comment');
		/* @var $objectType \wcf\data\object\type\ObjectType */
		$objectType = CommentHandler::getInstance()->getObjectType($objectTypeID);
		$commentManager = $objectType->getProcessor();
		$commentList = CommentHandler::getInstance()->getCommentList($commentManager, $objectTypeID,  $this->__get('contentID'));
		/* @var $commentList \wcf\data\comment\CommentList */
		return $commentList->countObjects();
	}
	
	/**
	 * @see	\wcf\data\IFeedEntry::getCategories()
	 */
	public function getCategories() {
		return $this->categories;
	}
	
	/**
	 * @see	\wcf\data\IMessage::isVisible()
	 */
	public function isVisible() {
		return $this->getDecoratedObject()->isVisible();
	}
}
