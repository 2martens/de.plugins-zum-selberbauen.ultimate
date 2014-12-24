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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.comment.manager
 * @category	Ultimate CMS
 */
namespace ultimate\system\comment\manager;
use ultimate\system\cache\builder\ContentCacheBuilder;
use wcf\system\comment\manager\AbstractCommentManager;
use wcf\system\request\UltimateLinkHandler;
use wcf\system\WCF;

/**
 * Content comment manager implementation.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.comment.manager
 * @category	Ultimate CMS
 */
class ContentCommentManager extends AbstractCommentManager {
	/**
	 * The permission to add a comment.
	 * @var	string
	 */
	protected $permissionAdd = 'user.ultimate.content.canAddComment';
	
	/**
	 * The permission to edit a comment.
	 * @var string
	 */
	protected $permissionEdit = 'user.ultimate.content.canEditComment';
	
	/**
	 * The permission to delete a comment.
	 * @var string
	 */
	protected $permissionDelete = 'user.ultimate.content.canDeleteComment';
	
	/**
	 * The mod permission to delete a comment.
	 * @var string 
	 */
	protected $permissionModDelete = 'mod.ultimate.content.canDeleteComment';
	
	/**
	 * The mod permission to edit a comment.
	 * @var string
	 */
	protected $permissionModEdit = 'mod.ultimate.content.canEditComment';
	
	/**
	 * The permission to be able to moderate.
	 * @var string
	 */
	protected $permissionCanModerate = 'mod.ultimate.content.canModerateComment';
	
	/**
	 * Initializes the permissions for this comment manager.
	 * 
	 * @internal
	 */
	protected function init() {
		// set setting to option
		$this->commentsPerPage = ULTIMATE_GENERAL_CONTENT_COMMENTS_PER_PAGE;
	}
	
	/**
	 * Returns true if comments and responses for given object id are accessible by current user.
	 * 
	 * @api
	 * @since	1.0.0
	 * 
	 * @param	integer	$objectID
	 * @param	boolean	$validateWritePermission	(optional) false by default
	 * @return	boolean
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
	 * Returns a link to given object type id and object id.
	 * 
	 * @api
	 * @since	1.0.0
	 * 
	 * @param	integer	$objectTypeID
	 * @param	integer	$objectID
	 * @return	string
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
	 * Returns the title for a comment or response.
	 * 
	 * @api
	 * @since	1.0.0
	 * 
	 * @param	integer	$objectTypeID
	 * @param	integer	$objectID
	 * @param	boolean	$isResponse	(optional) false by default
	 * @return	string
	 */
	public function getTitle($objectTypeID, $objectID, $isResponse = false) {
		if ($isResponse) return WCF::getLanguage()->get('ultimate.content.commentResponse');
		
		return WCF::getLanguage()->get('ultimate.content.comment');
	}
	
	/**
	 * Updates total count of comments (includes responses).
	 * 
	 * {@internal Does nothing. }}
	 * 
	 * @param	integer	$objectID
	 * @param	integer	$value
	 */
	public function updateCounter($objectID, $value) {
	}
}
