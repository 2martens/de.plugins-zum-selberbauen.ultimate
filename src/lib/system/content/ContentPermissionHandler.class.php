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
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.content
 * @category	Ultimate CMS
 */
namespace ultimate\system\content;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles individual permissions for contents.
 *
 * @author	    Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license	    http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package	    de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.content
 * @category	Ultimate CMS
 */
class ContentPermissionHandler extends SingletonFactory {
	/**
	 * list of permissions
	 * @var	array
	 */
	protected $contentPermissions = array();

	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->contentPermissions = ContentPermissionCache::getInstance()->getPermissions(WCF::getUser());
	}

	/**
	 * Returns the content permission with the given name for the content with the given id.
	 *
	 * @param	integer	$contentID
	 * @param	string	$permission
	 * @return	boolean
	 */
	public function getPermission($contentID, $permission) {
		if (isset($this->contentPermissions[$contentID][$permission])) return $this->contentPermissions[$contentID][$permission];

		return WCF::getSession()->getPermission('user.ultimate.content.'.$permission);
	}
}
