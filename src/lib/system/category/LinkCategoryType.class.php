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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.category
 * @category	Ultimate CMS
 */
class LinkCategoryType extends AbstractCategoryType {
	/**
	 * @see \wcf\system\category\AbstractCategoryType::$forceDescription
	 */
	protected $forceDescription = false;
	
	/**
	 * @see \wcf\system\category\AbstractCategoryType::$hasDescription
	 */
	protected $hasDescription = false;
	
	/**
	 * language category which contains the language variables of i18n values
	 * @var	string
	 */
	protected $i18nLangVarCategory = 'ultimate.link';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.category.AbstractCategoryType.html#$langVarPrefix
	 */
	protected $langVarPrefix = 'wcf.acp.ultimate.linkCategory';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.category.AbstractCategoryType.html#$permissionPrefix
	 */
	protected $permissionPrefix = 'admin.content.ultimate';
	
	/**
	 * @var string[]
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.category.AbstractCategoryType.html#$objectTypes
	 */
	protected $objectTypes = array(
		'com.woltlab.wcf.clipboardItem' => 'de.plugins-zum-selberbauen.ultimate.link'
	);
	
	/**
	 * @see	wcf\system\category\ICategoryType::canAddCategory()
	 */
	public function canAddCategory() {
		return WCF::getSession()->getPermission($this->permissionPrefix.'.canManageCategories');
	}
	
	/**
	 * @see	wcf\system\category\ICategoryType::canDeleteCategory()
	 */
	public function canDeleteCategory() {
		return WCF::getSession()->getPermission($this->permissionPrefix.'.canManageCategories');
	}
	
	/**
	 * @see	wcf\system\category\ICategoryType::canEditCategory()
	 */
	public function canEditCategory() {
		return WCF::getSession()->getPermission($this->permissionPrefix.'.canManageCategories');
	}
}
