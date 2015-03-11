<?php
/**
 * Contains the ContentPermissionCacheBuilder class.
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
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use wcf\system\acl\ACLHandler;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caches the content permissions for a combination of user groups.
 *
 * @author	    Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license	    http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package	    de.plugins-zum-selberbauen.de
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class ContentPermissionCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	protected function rebuild(array $parameters) {
		$data = array();

		if (!empty($parameters)) {
			$conditionBuilder = new PreparedStatementConditionBuilder();
			$conditionBuilder->add('acl_option.objectTypeID = ?', array(ACLHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.content')));
			$conditionBuilder->add('acl_option.categoryName LIKE ?', array('user.%'));
			$conditionBuilder->add('option_to_group.optionID = acl_option.optionID');
			$conditionBuilder->add('option_to_group.groupID IN (?)', array($parameters));
			$sql = "SELECT		option_to_group.groupID, option_to_group.objectID AS contentID, option_to_group.optionValue,
						acl_option.optionName AS permission
				FROM		wcf".WCF_N."_acl_option acl_option,
						wcf".WCF_N."_acl_option_to_group option_to_group
						".$conditionBuilder;
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute($conditionBuilder->getParameters());
			while ($row = $statement->fetchArray()) {
				if (!isset($data[$row['contentID']][$row['permission']])) $data[$row['contentID']][$row['permission']] = $row['optionValue'];
				else $data[$row['contentID']][$row['permission']] = $row['optionValue'] || $data[$row['contentID']][$row['permission']];
			}
		}

		return $data;
	}
}
