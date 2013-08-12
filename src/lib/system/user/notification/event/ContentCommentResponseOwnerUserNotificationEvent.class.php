<?php
/**
 * Contains the ContentCommentResponseUserNotificationEvent class.
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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 *
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.notification.event
 * @category	Ultimate CMS
 */
namespace ultimate\system\user\notification\event;
use wcf\data\comment\Comment;
use wcf\data\user\User;
use wcf\system\request\UltimateLinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;
use wcf\system\WCF;

/**
 * User notification event for content's owner for comment responses.
 *
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.notification.event
 * @category	Ultimate CMS
 */
class ContentCommentResponseOwnerUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * The determined content for this event.
	 *
	 * @var \ultimate\data\content\Content
	 */
	private $content = null;
	
	/**
	 * @see	wcf\system\user\notification\event\IUserNotificationEvent::getTitle()
	 */
	public function getTitle() {
		return $this->getLanguage()->get('wcf.user.notification.content.commentResponseOwner.title');
	}
	
	/**
	 * @see	wcf\system\user\notification\event\IUserNotificationEvent::getMessage()
	 */
	public function getMessage() {
		// @todo: use cache or a single query to retrieve required data
		$comment = new Comment($this->userNotificationObject->__get('commentID'));
		$commentAuthor = new User($comment->__get('userID'));
		
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.content.commentResponseOwner.message', array(
			'author' => $this->author,
			'commentAuthor' => $commentAuthor
		));
	}
	
	/**
	 * @see	wcf\system\user\notification\event\IUserNotificationEvent::getEmailMessage()
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment($this->userNotificationObject->__get('commentID'));
		$commentAuthor = new User($comment->__get('userID'));
		$content = $this->getContent();
		
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.content.commentResponseOwner.mail', array(
			'response' => $this->userNotificationObject,
			'author' => $this->author,
			'commentAuthor' => $commentAuthor,
			'owner' => $content->__get('author'),
			'notificationType' => $notificationType,
			'link' => $this->getLink()
		));
	}
	
	/**
	 * @see	wcf\system\user\notification\event\IUserNotificationEvent::getLink()
	 */
	public function getLink() {
		$content = $this->getContent();
		
		/* @var $date \DateTime */
		$date = $content->__get('publishDateObject');
		return UltimateLinkHandler::getInstance()->getLink(null, array(
			'date' => ''. $date->format('Y-m-d'),
			'contentSlug' => $content->__get('contentSlug')
		));
	}
	
	/**
	 * Determines the content and returns it.
	 *
	 * @return \ultimate\data\content\Content
	 */
	private function getContent()
	{
		if ($this->content === null) {
			$commentID = $this->userNotificationObject->__get('commentID');
				
			$sql = "SELECT objectID
			        FROM   wcf".WCF_N."_comment
			        WHERE  commentID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($commentID));
			$row = $statement->fetchArray();
				
			$contentID = $row['objectID'];
				
			$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
			/* @var $content \ultimate\data\content\Content */
			$this->content = $contents[$contentID];
		}
		return $this->content;
	}
}
