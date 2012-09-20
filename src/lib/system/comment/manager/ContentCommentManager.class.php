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
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.comment.manager
 * @category	Ultimate CMS
 */
namespace ultimate\system\comment\manager;
use wcf\system\comment\manager\AbstractCommentManager;
use wcf\system\WCF;

/**
 * Content comment manager implementation.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.comment.manager
 * @category	Ultimate CMS
 */
class ContentCommentManager extends AbstractCommentManager {
	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::canAdd()
	 */
	public function canAdd($objectID) {
		return $this->canAdd;
	}
	
	/**
	 * Initializes the permissions for this comment manager.
	 * 
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.SingletonFactory.html#init
	 */
	protected function init() {
		if (WCF::getUser()->userID) {
			if (WCF::getSession()->getPermission('user.ultimate.content.canAddComment')) {
				$this->canAdd = true;
			}
			if (WCF::getSession()->getPermission('user.ultimate.content.canEditComment')) {
				$this->canEdit = true;
			}
			if (WCF::getSession()->getPermission('user.ultimate.content.canDeleteComment')) {
				$this->canDelete = true;
			}
		}
		// set setting to option
		$this->commentsPerPage = ULTIMATE_GENERAL_CONTENT_COMMENTS_PER_PAGE;
	}
}
