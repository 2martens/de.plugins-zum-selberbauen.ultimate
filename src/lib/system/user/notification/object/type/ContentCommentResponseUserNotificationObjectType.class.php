<?php
/**
 * Contains the ContentCommentResponseUserNotificationObjectType class.
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
 * Represents a content comment response notification object type.
 *
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.notification.object.type
 * @category	Ultimate CMS
 */
class ContentCommentResponseUserNotificationObjectType extends AbstractUserNotificationObjectType {
	/**
	 * The decorator class name.
	 * @var string
	 */
	protected static $decoratorClassName = 'wcf\system\user\notification\object\CommentResponseUserNotificationObject';
	
	/**
	 * The object class name.
	 * @var string
	 */
	protected static $objectClassName = 'wcf\data\comment\response\CommentResponse';
	
	/**
	 * The object list class name.
	 * @var string
	 */
	protected static $objectListClassName = 'wcf\data\comment\response\CommentResponseList';
}
