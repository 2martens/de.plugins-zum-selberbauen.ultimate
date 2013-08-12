<?php
/**
 * Contains the ContentCommentResponseUserActivityEvent class.
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
 * @subpackage	system.user.activity.event
 * @category	Ultimate CMS
 */
namespace ultimate\system\user\activity\event;
use ultimate\system\cache\builder\ContentCacheBuilder;
use wcf\data\comment\response\CommentResponseList;
use wcf\data\comment\CommentList;
use wcf\data\user\UserProfileList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * User activity event implementation for content comment responses.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.activity.event
 * @category	Ultimate CMS
 */
class ContentCommentResponseUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * The read contents.
	 * @var \ultimate\data\content\Content[]
	 */
	protected $contents = array();
	
	/**
	 * The read users.
	 * @var \wcf\data\user\User[]
	*/
	protected $users = array();
	
	/**
	 * @see	wcf\system\user\activity\event\IUserActivityEvent::prepare()
	 */
	public function prepare(array $events) {
		$responseIDs = array();
		foreach ($events as $event) {
			/* @var $event \wcf\data\user\activity\event\ViewableUserActivityEvent */
			$responseIDs[] = $event->__get('objectID');
		}
		
		// fetch responses
		$responseList = new CommentResponseList();
		$responseList->getConditionBuilder()->add("comment_response.responseID IN (?)", array($responseIDs));
		$responseList->readObjects();
		$responses = $responseList->getObjects();
		
		// fetch comments
		$commentIDs = $comments = array();
		foreach ($responses as $response) {
			$commentIDs[] = $response->commentID;
		}
		if (!empty($commentIDs)) {
			$commentList = new CommentList();
			$commentList->getConditionBuilder()->add("comment.commentID IN (?)", array($commentIDs));
			$commentList->readObjects();
			$comments = $commentList->getObjects();
		}
		
		// fetch users
		$userIDs = $users = $contentIDs = $contents = array();
		foreach ($comments as $comment) {
			$contentIDs[] = $comment->objectID;
			$userIDs[] = $comment->userID;
		}
		
		foreach ($this->contents as $contentID => $content) {
			/* @var $content \ultimate\data\content\Content */
			if (!in_array($contentID, $contentIDs)) continue;
			$contents[$contentID] = $content;
		}
		
		foreach ($contents as $content) {
			$userIDs[] = $content->__get('authorID');
		}
		
		if (!empty($userIDs)) {
			$userList = new UserProfileList();
			$userList->getConditionBuilder()->add("user_table.userID IN (?)", array($userIDs));
			$userList->readObjects();
			$users = $userList->getObjects();
		}
		
		// set message
		foreach ($events as $event) {
			if (isset($responses[$event->__get('objectID')])) {
				$response = $responses[$event->__get('objectID')];
				if (isset($comments[$response->__get('commentID')])) {
					$comment = $comments[$response->__get('commentID')];
					if (isset($contents[$comment->__get('objectID')])) {
						$content = $contents[$comment->__get('objectID')];
						if (isset($users[$content->__get('authorID')]) && isset($users[$comment->__get('userID')])) {
							$event->setIsAccessible();
							// title
							$text = WCF::getLanguage()->getDynamicVariable('wcf.user.profile.recentActivity.contentCommentResponse', array(
								'commentAuthor' => $users[$comment->__get('userID')],
								'author' => $users[$content->__get('authorID')],
								'content' => $content
							));
							$event->setTitle($text);
							
							// description
							$event->setDescription($response->getExcerpt());
							continue;
						}
					}
				}
			}
			$event->isOrphaned();
		}
	}
	
	/**
	 * Initializes the cache.<br />
	 *
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.SingletonFactory.html#init
	 */
	protected function init() {
		// load cache
		$this->contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
	}
}
