<?php
/**
 * Contains the ContentCommentUserNotificationObjectType class.
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
 * @subpackage	system.user.notification.object.type
 * @category	Ultimate CMS
 */
namespace ultimate\system\user\notification\object\type;
use wcf\system\cache\CacheHandler;

use wcf\data\comment\Comment;
use wcf\data\object\type\AbstractObjectTypeProcessor;
use wcf\system\user\notification\object\type\ICommentUserNotificationObjectType;
use wcf\system\user\notification\object\type\IUserNotificationObjectType;
use wcf\system\user\notification\object\CommentUserNotificationObject;
use wcf\system\WCF;

/**
 * Represents the content comment notification object type.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.notification.object.type
 * @category	Ultimate CMS
 */
class ContentCommentUserNotificationObjectType extends AbstractObjectTypeProcessor implements ICommentUserNotificationObjectType, IUserNotificationObjectType {
	/**
	 * @see \wcf\system\user\notification\object\type\IUserNotificationObjectType::getObjectByID()
	 */
	public function getObjectByID($objectID) {
		$object = new Comment($objectID);
		if (!$object->commentID) {
			// create empty object for unknown request id
			$object = new Comment(null, array('commentID' => $objectID));
		}
		
		return array($object->commentID => new CommentUserNotificationObject($object));
	}
	
	/**
	 * @see \wcf\system\user\notification\object\type\IUserNotificationObjectType::getObjectsByIDs()
	 */
	public function getObjectsByIDs(array $objectIDs) {
		$objectList = new CommentList();
		$objectList->getConditionBuilder()->add("comment.commentID IN (?)", array($objectIDs));
		$objectList->readObjects();
		
		$objects = array();
		foreach ($objectList as $object) {
			$objects[$object->commentID] = new CommentUserNotificationObject($object);
		}
		
		foreach ($objectIDs as $objectID) {
			// append empty objects for unknown ids
			if (!isset($objects[$objectID])) {
				$objects[$objectID] = new CommentUserNotificationObject(new Comment(null, array('commentID' => $objectID)));
			}
		}
		
		return $objects;
	}
	
	/**
	 * Returns the user id of the user who wrote the content which has been commented.<br />
	 * 
	 * @see	\wcf\system\user\notification\object\type\ICommentUserNotificationObjectType::getOwnerID()
	 */
	public function getOwnerID($objectID) {
		// read cache
		$cacheName = 'content';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\ContentCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$contents = CacheHandler::getInstance()->get($cacheName, 'contents');
		
		$content = $contents[$objectID];
		
		return $content->__get('authorID');
	}
}

