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
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.page
 * @category	Ultimate CMS
 */
namespace ultimate\system\page;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles individual permissions for pages.
 *
 * @author	    Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license	    http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package	    de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.page
 * @category	Ultimate CMS
 */
class PagePermissionHandler extends SingletonFactory {
	/**
	 * list of permissions
	 * @var	array
	 */
	protected $pagePermissions = array();

	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->pagePermissions = PagePermissionCache::getInstance()->getPermissions(WCF::getUser());
	}

	/**
	 * Returns the page permission with the given name for the page with the given id.
	 *
	 * @param	integer	$pageID
	 * @param	string	$permission
	 * @return	boolean
	 */
	public function getPermission($pageID, $permission) {
		if (isset($this->pagePermissions[$pageID][$permission])) return $this->pagePermissions[$pageID][$permission];

		$editingKeywords = array(
			'add',
			'edit',
			'delete',
			'save',
			'publish'
		);
		
		$category = 'content';
		foreach ($editingKeywords as $keyword) {
			if (stripos($permission, $keyword) !== false) {
				$category = 'editing';
				break;
			}
		}
		
		return WCF::getSession()->getPermission('user.ultimate.'.$category.'.'.$permission);
	}
}
