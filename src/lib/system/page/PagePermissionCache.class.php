<?php
/**
 * Contains the PagePermissionCache class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 *
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.page
 * @category	Ultimate CMS
 */
namespace ultimate\system\page;
use ultimate\system\cache\builder\PagePermissionCacheBuilder;
use wcf\data\user\User;
use wcf\system\acl\ACLHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Caches content permissions.
 *
 * @author      Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license     http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package	    de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.page
 * @category	Ultimate CMS
 */
class PagePermissionCache extends SingletonFactory {
	/**
	 * list of permissions
	 * @var	array
	 */
	protected $pagePermissions = array();

	/**
	 * Returns the permissions for given user.
	 *
	 * @param   \wcf\data\user\User $user
	 */
	protected function loadPermissions(User $user) {
		// get groups permissions
		$this->pagePermissions[$user->userID] = PagePermissionCacheBuilder::getInstance()->getData($user->getGroupIDs());

		// get user permissions
		if ($user->userID) {
			// load storage
			UserStorageHandler::getInstance()->loadStorage(array($user->userID));

			// get ids
			$data = UserStorageHandler::getInstance()->getStorage(array($user->userID), 'ultimatePagePermissions');

			// cache does not exist or is outdated
			if ($data[$user->userID] === null) {
				$moderatorPermissions = $userPermissions = array();

				$sql = "SELECT	option_to_user.objectID AS pageID, option_to_user.optionValue,
						acl_option.optionName AS permission, acl_option.categoryName
					FROM	wcf".WCF_N."_acl_option acl_option,
						wcf".WCF_N."_acl_option_to_user option_to_user
					WHERE	acl_option.objectTypeID = ?
						AND option_to_user.optionID = acl_option.optionID
						AND option_to_user.userID = ?";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array(
								ACLHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.page'),
								$user->userID
							));
				while ($row = $statement->fetchArray()) {
					if (StringUtil::startsWith($row['categoryName'], 'user.')) {
						$userPermissions[$row['pageID']][$row['permission']] = $row['optionValue'];
					}
					else {
						$moderatorPermissions[$row['pageID']][$row['permission']] = $row['optionValue'];
					}
				}

				// update storage data
				UserStorageHandler::getInstance()->update($user->userID, 'ultimatePagePermissions', serialize(array(
					'user' => $userPermissions
				)));
			}
			else {
				$tmp = unserialize($data[$user->userID]);
				$userPermissions = $tmp['user'];
			}

			foreach ($userPermissions as $pageID => $permissions) {
				foreach ($permissions as $name => $value) {
					$this->pagePermissions[$user->userID][$pageID][$name] = $value;
				}
			}
		}
	}

	/**
	 * Returns the page permissions for given user.
	 *
	 * @param	\wcf\data\user\User	$user
	 * @return	array
	 */
	public function getPermissions(User $user) {
		if (!isset($this->pagePermissions[$user->userID])) {
			$this->loadPermissions($user);
		}

		return $this->pagePermissions[$user->userID];
	}
}
