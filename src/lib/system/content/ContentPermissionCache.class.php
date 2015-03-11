<?php
/**
 * Contains the ContentPermissionCache class.
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
 * @subpackage	system.content
 * @category	Ultimate CMS
 */
namespace ultimate\system\content;
use ultimate\system\cache\builder\ContentPermissionCacheBuilder;
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
 * @subpackage	system.content
 * @category	Ultimate CMS
 */
class ContentPermissionCache extends SingletonFactory {
	/**
	 * list of permissions
	 * @var	array
	 */
	protected $contentPermissions = array();

	/**
	 * Returns the permissions for given user.
	 *
	 * @param   \wcf\data\user\User $user
	 */
	protected function loadPermissions(User $user) {
		// get groups permissions
		$this->contentPermissions[$user->userID] = ContentPermissionCacheBuilder::getInstance()->getData($user->getGroupIDs());

		// get user permissions
		if ($user->userID) {
			// load storage
			UserStorageHandler::getInstance()->loadStorage(array($user->userID));

			// get ids
			$data = UserStorageHandler::getInstance()->getStorage(array($user->userID), 'ultimateContentPermissions');

			// cache does not exist or is outdated
			if ($data[$user->userID] === null) {
				$moderatorPermissions = $userPermissions = array();

				$sql = "SELECT	option_to_user.objectID AS contentID, option_to_user.optionValue,
						acl_option.optionName AS permission, acl_option.categoryName
					FROM	wcf".WCF_N."_acl_option acl_option,
						wcf".WCF_N."_acl_option_to_user option_to_user
					WHERE	acl_option.objectTypeID = ?
						AND option_to_user.optionID = acl_option.optionID
						AND option_to_user.userID = ?";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array(
								ACLHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.content'),
								$user->userID
							));
				while ($row = $statement->fetchArray()) {
					if (StringUtil::startsWith($row['categoryName'], 'user.')) {
						$userPermissions[$row['contentID']][$row['permission']] = $row['optionValue'];
					}
					else {
						$moderatorPermissions[$row['contentID']][$row['permission']] = $row['optionValue'];
					}
				}

				// update storage data
				UserStorageHandler::getInstance()->update($user->userID, 'ultimateContentPermissions', serialize(array(
					'user' => $userPermissions
				)));
			}
			else {
				$tmp = unserialize($data[$user->userID]);
				$userPermissions = $tmp['user'];
			}

			foreach ($userPermissions as $contentID => $permissions) {
				foreach ($permissions as $name => $value) {
					$this->contentPermissions[$user->userID][$contentID][$name] = $value;
				}
			}
		}
	}

	/**
	 * Returns the content permissions for given user.
	 *
	 * @param	\wcf\data\user\User	$user
	 * @return	array
	 */
	public function getPermissions(User $user) {
		if (!isset($this->contentPermissions[$user->userID])) {
			$this->loadPermissions($user);
		}

		return $this->contentPermissions[$user->userID];
	}
}
