<?php
/**
 * Contains the LinkCategoryType class.
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
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.category
 * @category	Ultimate CMS
 */
namespace ultimate\system\category;
use wcf\system\category\AbstractCategoryType;
use wcf\system\WCF;

/**
 * Manages the link category type.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.category
 * @category	Ultimate CMS
 */
class LinkCategoryType extends AbstractCategoryType {
	/**
	 * If true the user has to enter a description.
	 * @var boolean
	 */
	protected $forceDescription = false;
	
	/**
	 * Symbolizes if categories of this type have descriptions.
	 * @var boolean
	 */
	protected $hasDescription = false;
	
	/**
	 * language category which contains the language variables of i18n values
	 * @var	string
	 */
	protected $i18nLangVarCategory = 'ultimate.link';
	
	/**
	 * prefix used for language variables in templates
	 * @var	string
	 */
	protected $langVarPrefix = 'wcf.acp.ultimate.linkCategory';
	
	/**
	 * permission prefix for the add/delete/edit permissions
	 * @var	string
	 */
	protected $permissionPrefix = 'admin.content.ultimate';
	
	/**
	 * name of the object types associated with categories of this type (the key is the definition name and value the object type name)
	 * @var string[]
	 */
	protected $objectTypes = array(
		'com.woltlab.wcf.clipboardItem' => 'de.plugins-zum-selberbauen.ultimate.link'
	);
	
	/**
	 * Returns true if the active user can add a category of this type.
	 * 
	 * @return	boolean
	 */
	public function canAddCategory() {
		return WCF::getSession()->getPermission($this->permissionPrefix.'.canManageCategories');
	}
	
	/**
	 * Returns true if the active user can delete a category of this type.
	 * 
	 * @return	boolean
	 */
	public function canDeleteCategory() {
		return WCF::getSession()->getPermission($this->permissionPrefix.'.canManageCategories');
	}
	
	/**
	 * Returns true if the active user can edit a category of this type.
	 * 
	 * @return	boolean
	 */
	public function canEditCategory() {
		return WCF::getSession()->getPermission($this->permissionPrefix.'.canManageCategories');
	}
	
	/**
	 * Returns abbreviation of the application this category type belongs to.
	 * 
	 * @return	string
	 */
	public function getApplication() {
		return 'ultimate';
	}
}
