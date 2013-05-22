<?php
/**
 * Contains the ContentCommentUserActivityEvent class.
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
use ultimate\system\cache\builder\AuthorCacheBuilder;
use ultimate\system\cache\builder\ContentCacheBuilder;
use wcf\system\cache\builder\CommentCacheBuilder;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * User activity event implementation for content comments.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.activity.event
 * @category	Ultimate CMS
 */
class ContentCommentUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * Contains the read comments.
	 * @var \wcf\data\comment\Comment[]
	 */
	protected $comments = array();
	
	/**
	 * Contains the read contents.
	 * @var \ultimate\data\content\Content[]
	 */
	protected $contents = array();
	
	/**
	 * Contains the read authors.
	 * @var \wcf\data\user\User[]
	 */
	protected $authors = array();
	
	/**
	 * @see	\wcf\system\user\activity\event\IUserActivityEvent::prepare()
	 */
	public function prepare(array $events) {
		$commentIDs = array();
		foreach ($events as $event) {
			/* @var $event \wcf\data\user\activity\event\UserActivityEvent */
			$commentIDs[] = $event->__get('objectID');
		}
		
		// get comments
		$comments = array();
		foreach ($this->comments as $commentID => $comment) {
			/* @var $comment \wcf\data\comment\Comment */
			if (!in_array($commentID, $commentIDs)) continue;
			$comments[$commentID] = $comment;
		}
		
		// get contents
		$contentIDs = array();
		foreach ($comments as $comment) {
			$contentIDs[] = $comment->__get('objectID');
		}
		
		$contents = array();
		foreach ($this->contents as $contentID => $content) {
			/* @var $content \ultimate\data\content\Content */
			if (!in_array($contentID, $contentIDs)) continue;
			$contents[$contentID] = $content;
		}
		
		// get authors
		$authorIDs = array();
		foreach ($contents as $content) {
			$authorIDs[] = $content->__get('authorID');
		}
		
		$authors = array();
		foreach ($this->authors as $authorID => $author) {
			/* @var $author \wcf\data\user\User */
			if (!in_array($authorID, $authorIDs)) continue;
			$authors[$authorID] = $author;
		}
		
		// set message
		foreach ($events as $event) {
			if (isset($comments[$event->__get('objectID')])) {
				// short output
				$comment = $comments[$event->__get('objectID')];
				if (isset($contents[$comment->__get('objectID')])) {
					$content = $contents[$comment->__get('objectID')];
					if (isset($authors[$content->__get('authorID')])) {
						$author = $authors[$content->__get('authorID')];
						$text = WCF::getLanguage()->getDynamicVariable('wcf.user.profile.recentActivity.contentComment', array(
							'author' => $author,
							'content' => $content
						));
						$event->setTitle($text);
						
						// output
						$event->setDescription($comment->getFormattedMessage());
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
		$this->contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		$this->authors = AuthorCacheBuilder::getInstance()->getData(array(), 'authors');
	}
}

