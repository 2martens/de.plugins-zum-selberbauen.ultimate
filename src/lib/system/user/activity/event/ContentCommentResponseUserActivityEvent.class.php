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
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.activity.event
 * @category	Ultimate CMS
 */
namespace ultimate\system\user\activity\event;
use ultimate\system\cache\builder\ContentCacheBuilder;
use wcf\data\comment\response\CommentResponseList;
use wcf\system\cache\builder\CommentCacheBuilder;
use wcf\system\cache\builder\CommentResponseCacheBuilder;
use wcf\system\cache\builder\UserCacheBuilder;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * User activity event implementation for content comment responses.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.activity.event
 * @category	Ultimate CMS
 */
class ContentCommentResponseUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * Contains the read comments.
	 * @var \wcf\data\comment\Comment[]
	 */
	protected $comments = array();
	
	/**
	 * Contains the read comment responses.
	 * @var \wcf\data\comment\response\CommentResponse[]
	 */
	protected $responses = array();
	
	/**
	 * Contains the read contents.
	 * @var \ultimate\data\content\Content[]
	 */
	protected $contents = array();
	
	/**
	 * Contains the read users.
	 * @var \wcf\data\user\User[]
	*/
	protected $users = array();
	
	/**
	 * @see	wcf\system\user\activity\event\IUserActivityEvent::prepare()
	 */
	public function prepare(array $events) {
		$responseIDs = array();
		foreach ($events as $event) {
			/* @var $event \wcf\data\user\activity\event\UserActivityEvent */
			$responseIDs[] = $event->__get('objectID');
		}
		
		// get responses
		$responses = array();
		foreach ($this->responses as $responseID => $response) {
			/* @var $response \wcf\data\comment\response\CommentResponse */
			if (!in_array($responseID, $responseIDs)) continue;
			$responses[$responseID] = $response;
		}
		
		// get comments
		$commentIDs = array();
		foreach ($responses as $response) {
			$commentIDs[] = $response->__get('commentID');
		}
		$comments = array();
		foreach ($this->comments as $commentID => $comment) {
			/* @var $response \wcf\data\comment\Comment */
			if (!in_array($commentID, $commentIDs)) continue;
			$comments[$commentID] = $comment;
		}
		
		// get contents and users
		$contentIDs = array();
		$userIDs = array();
		foreach ($comments as $comment) {
			$contentIDs[] = $comment->__get('objectID');
			$userIDs[] = $comment->__get('userID');
		}
		
		$contents = array();
		foreach ($this->contents as $contentID => $content) {
			/* @var $content \ultimate\data\content\Content */
			if (!in_array($contentID, $contentIDs)) continue;
			$contents[$contentID] = $content;
		}
		
		foreach ($contents as $content) {
			$userIDs[] = $content->__get('authorID');
		}
		
		$users = array();
		foreach ($this->users as $userID => $user) {
			/* @var $user \wcf\data\user\User */
			if (!in_array($userID, $userIDs)) continue;
			$users[$authorID] = $user;
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
							// title
							$text = WCF::getLanguage()->getDynamicVariable('wcf.user.profile.recentActivity.contentCommentResponse', array(
								'commentAuthor' => $users[$comment->__get('userID')],
								'author' => $users[$content->__get('authorID')],
								'content' => $content
							));
							$event->setTitle($text);
							
							// description
							$event->setDescription($response->getFormattedMessage());
						}
					}
				}
			}
		}
	}
	
	/**
	 * Initializes the cache.<br />
	 *
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.SingletonFactory.html#init
	 */
	protected function init() {
		// load cache
		$this->comments = CommentCacheBuilder::getInstance()->getData(array(), 'comments');
		$this->responses = CommentResponseCacheBuilder::getInstance()->getData(array(), 'responses');
		$this->contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		$this->users = UserCacheBuilder::getInstance()->getData(array(), 'users');
	}
}
