<?php
/**
 * Contains the ContentCommentManager class.
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
 * @subpackage	system.comment.manager
 * @category	Ultimate CMS
 */
namespace ultimate\system\comment\manager;
use ultimate\system\cache\builder\ContentCacheBuilder;
use ultimate\system\request\UltimateLinkHandler;
use wcf\system\comment\manager\AbstractCommentManager;
use wcf\system\WCF;

/**
 * Content comment manager implementation.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.comment.manager
 * @category	Ultimate CMS
 */
class ContentCommentManager extends AbstractCommentManager {
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.comment.manager.AbstractCommentManager.html#$permissionAdd
	 */
	protected $permissionAdd = 'user.ultimate.content.canAddComment';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.comment.manager.AbstractCommentManager.html#$permissionEdit
	 */
	protected $permissionEdit = 'user.ultimate.content.canEditComment';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.comment.manager.AbstractCommentManager.html#$permissionDelete
	 */
	protected $permissionDelete = 'user.ultimate.content.canDeleteComment';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.comment.manager.AbstractCommentManager.html#$permissionModDelete
	 */
	protected $permissionModDelete = 'mod.ultimate.content.canDeleteComment';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.comment.manager.AbstractCommentManager.html#$permissionModEdit
	 */
	protected $permissionModEdit = 'mod.ultimate.content.canEditComment';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.comment.manager.AbstractCommentManager.html#$permissionModDelete
	 */
	protected $permissionCanModerate = 'mod.ultimate.content.canModerateComment';
	
	/**
	 * Initializes the permissions for this comment manager.
	 * 
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.SingletonFactory.html#init
	 */
	protected function init() {
		// set setting to option
		$this->commentsPerPage = ULTIMATE_GENERAL_CONTENT_COMMENTS_PER_PAGE;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.comment.manager.ICommentManager.html#isAccessible
	 */
	public function isAccessible($objectID, $validateWritePermission = false) {
		// check object id
		$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		if (!isset($contents[$objectID])) {
			return false;
		}
		
		/* @var $content \ultimate\data\content\Content */
		$content = $contents[$objectID];
		
		if ($content === null) {
			return false;
		}
	
		// check visibility
		$visibility = $content->__get('visibility');
		/* @var $user \wcf\data\user\User */
		$user = WCF::getUser();
		switch ($visibility) {
			case 'protected':
				$userGroups = $user->getGroupIDs();
				$contentGroups = array_keys($content->__get('groups'));
				foreach ($contentGroups as $groupID) {
					if (isset($userGroups[$groupID])) {
						return true;
					}
				}
				return false;
			case 'private':
				if ($content->__get('authorID') != $user->__get('userID')) {
					return false;
				}
				break;
		}
		
		return true;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.comment.manager.ICommentManager.html#getLink
	 */
	public function getLink($objectTypeID, $objectID) {
		$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		/* @var $content \ultimate\data\content\Content */
		$content = $contents[$objectID];
		/* @var $date \DateTime */
		$date = $content->__get('publishDateObject');
		return UltimateLinkHandler::getInstance()->getLink(null, array(
			'date' => ''. $date->format('Y-m-d'),
			'contentSlug' => $content->__get('contentSlug')
		));
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.comment.manager.ICommentManager.html#getTitle
	 */
	public function getTitle($objectTypeID, $objectID, $isResponse = false) {
		if ($isResponse) return WCF::getLanguage()->get('ultimate.content.commentResponse');
		
		return WCF::getLanguage()->get('ultimate.content.comment');
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.comment.manager.ICommentManager.html#updateCounter
	 */
	public function updateCounter($objectID, $value) { }
}
