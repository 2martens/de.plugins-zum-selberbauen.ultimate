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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.notification.object.type
 * @category	Ultimate CMS
 */
namespace ultimate\system\user\notification\object\type;
use ultimate\system\cache\builder\ContentCacheBuilder;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;
use wcf\system\user\notification\object\type\ICommentUserNotificationObjectType;
use wcf\system\WCF;

/**
 * Represents the content comment notification object type.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.notification.object.type
 * @category	Ultimate CMS
 */
class ContentCommentUserNotificationObjectType extends AbstractUserNotificationObjectType implements ICommentUserNotificationObjectType {
	/**
	 * @see	wcf\system\user\notification\object\type\AbstractUserNotificationObjectType::$decoratorClassName
	 */
	protected static $decoratorClassName = 'wcf\system\user\notification\object\CommentUserNotificationObject';
	
	/**
	 * @see	wcf\system\user\notification\object\type\AbstractUserNotificationObjectType::$objectClassName
	 */
	protected static $objectClassName = 'wcf\data\comment\Comment';
	
	/**
	 * @see	wcf\system\user\notification\object\type\AbstractUserNotificationObjectType::$objectListClassName
	 */
	protected static $objectListClassName = 'wcf\data\comment\CommentList';
	
	/**
	 * Returns the user id of the user who wrote the content which has been commented.<br />
	 * 
	 * @see	\wcf\system\user\notification\object\type\ICommentUserNotificationObjectType::getOwnerID()
	 */
	public function getOwnerID($commentID) {
		$sql = "SELECT objectID
		        FROM   wcf".WCF_N."_comment
		        WHERE  commentID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($commentID));
		$row = $statement->fetchArray();
		
		$contentID = $row['objectID'];
		
		// read cache
		$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		$content = $contents[$contentID];
		
		return $content->__get('authorID');
	}
}

