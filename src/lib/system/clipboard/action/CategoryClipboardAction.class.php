<?php
/**
 * Contains the CategoryClipboardAction class.
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
 * @subpackage	system.clipboard.action
 * @category	Ultimate CMS
 */
namespace ultimate\system\clipboard\action;
use wcf\system\clipboard\action\IClipboardAction;
use wcf\system\clipboard\ClipboardEditorItem;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

/**
 * Prepares clipboard editor items for category objects.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.clipboard.action
 * @category	Ultimate CMS
 */
class CategoryClipboardAction implements IClipboardAction {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.clipboard.action.IClipboardAction.html#getTypeName
	 */
	public function getTypeName() {
		return 'de.plugins-zum-selberbauen.category';
	}
	
	/**
	 * @param	\ultimate\data\category\Category[]	$objects
	 * @param	string								$actionName
	 * @param	array								$typeData
	 * @return	\wcf\system\clipboard\ClipboardEditorItem|null
	 * 
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.clipboard.action.IClipboardAction.html#execute
	 */
	public function execute(array $objects, $actionName, $typeData = array()) {
		$item = new ClipboardEditorItem();
		
		// handle actions
		switch (strtolower($actionName)) {
			case 'deleteCategory':
				$categoryIDs = array();
				$categoryIDs = $this->validateDelete($objects);
				if (empty($categoryIDs)) {
					return null;
				}
				
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.category.delete.confirmMessage', array('count' => count($categoryIDs))));
				$item->addParameter('actionName', 'delete');
				$item->addParameter('className', '\ultimate\data\category\CategoryAction');
				$item->addParameter('objectIDs', $categoryIDs);
				$item->setName('category.delete');
				break;
			default:
				throw new SystemException("Action '".$actionName."' is invalid.");
				break;
		}
		
		return $item;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.clipboard.action.IClipboardAction.html#getEditorLabel
	 */
	public function getEditorLabel(array $objects) {
		return WCF::getLanguage()->getDynamicVariable('wcf.clipboard.label.category.marked', array('count' => count($objects)));
	}
	
	/**
	 * Validates the delete action.
	 * 
	 * @param	array	$objects
	 * @return	integer[]
	 */
	protected function validateDelete(array $objects) {
		// checking permission
		if (!WCF::getSession()->getPermission('admin.content.ultimate.canDeleteCategory')) {
			return array();
		}
		
		// prevent possible problems with array_keys
		if (empty($objects)) return array();
		
		// get ids
		$categoryIDs = array_keys($objects);
		return $categoryIDs;		
	}
}
