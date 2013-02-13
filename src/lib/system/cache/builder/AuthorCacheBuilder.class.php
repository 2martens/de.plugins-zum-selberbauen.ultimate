<?php
/**
 * Contains the AuthorCacheBuilder class.
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
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use wcf\data\user\UserList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\cache\CacheHandler;

/**
 * Caches the authors.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class AuthorCacheBuilder implements AbstractCacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.AbstractCacheBuilder.html#rebuild
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'authors' => array(),
			'authorIDs' => array()
		);
		
		$userList = new UserList();
		$userList->readObjects();
		$users = $userList->getObjects();
		$userIDs = $userList->getObjectIDs();
		
		if (empty($users)) return $data;
		
		$authors = array();
		$authorIDs = array();
		
		// reading groups
		$cacheName = 'user-group';
		$cacheBuilderClassName = '\wcf\system\cache\builder\UserGroupCacheBuilder';
		$cacheIndex = 'groups';
		CacheHandler::getInstance()->addResource($cacheName, WCF_DIR.'cache/cache.'.$cacheName.'.php', $cacheBuilderClassName);
		$groups = CacheHandler::getInstance()->get($cacheName, $cacheIndex);
		
		// permissions for being author
		$permissions = array(
			'admin.content.ultimate.canAddContent',
			'admin.content.ultimate.canEditContent'
		);
		
		// determine authors
		foreach ($users as $userID => $user) {
			/* @var $user \wcf\data\user\User */
			$groupIDs = $user->getGroupIDs();
			$__groups = array();
			$isAuthor = false;
			foreach ($groupIDs as $groupID) {
				$__groups[$groupID] = $groups[$groupID];
			}
			foreach ($__groups as $group) {
				/* @var $group \wcf\data\user\group\UserGroup */
				foreach ($permissions as $permission) {
					$result = $group->getGroupOption($permission);
					if (!$result) continue;
					elseif ($result === true) {
						$isAuthor = true;
						break 2;
					}
				} 
				
			}
			if (!$isAuthor) continue;
			$authors[$userID] = $user;
			$authorIDs[] = $userID;
		}
		$data['authors'] = $authors;
		$data['authorIDs'] = $authorIDs;
		return $data;
	}
}
