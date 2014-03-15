<?php
/**
 * Contains the MenuClipboardAction class.
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
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.clipboard.action
 * @category	Ultimate CMS
 */
namespace ultimate\system\clipboard\action;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\IClipboardAction;
use wcf\system\clipboard\ClipboardEditorItem;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

/**
 * Prepares the clipboard editor items for menu objects.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.clipboard.action
 * @category	Ultimate CMS
 */
class MenuClipboardAction implements IClipboardAction {
	/**
	 * Returns type name identifier.
	 * 
	 * @return	string
	 */
	public function getTypeName() {
		return 'de.plugins-zum-selberbauen.ultimate.menu';
	}
	
	/**
	 * Returns editor item for the clipboard action with the given name or null if the action is not applicable to the given objects.
	 * 
	 * @param	\ultimate\data\menu\Menu[]						$objects
	 * @param	\wcf\data\clipboard\action\ClipboardAction		$action
	 * @return	\wcf\system\clipboard\ClipboardEditorItem|null
	 * 
	 * @throws	\wcf\system\exception\SystemException	if given action name is invalid
	 */
	public function execute(array $objects, ClipboardAction $action) {
		$item = new ClipboardEditorItem();
		$actionName = $action->__get('actionName');
		// handle actions
		switch ($actionName) {
			case 'deleteMenu':
				$menuIDs = array();
				$menuIDs = $this->validateDelete($objects);
				if (empty($menuIDs)) {
					return null;
				}
	
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.menu.delete.confirmMessage', array('count' => count($menuIDs))));
				$item->addParameter('actionName', 'delete');
				$item->addParameter('className', '\ultimate\data\menu\MenuAction');
				$item->addParameter('objectIDs', $menuIDs);
				$item->setName('menu.delete');
				break;
			default:
				throw new SystemException("Action '".$actionName."' is invalid.");
				break;
		}
	
		return $item;
	}
	
	/**
	 * Returns label for item editor.
	 * 
	 * @param	array	$objects
	 * @return	string
	 */
	public function getEditorLabel(array $objects) {
		return WCF::getLanguage()->getDynamicVariable('wcf.clipboard.label.menu.marked', array('count' => count($objects)));
	}
	
	/**
	 * Returns action class name.
	 * 
	 * @return	string
	 */
	public function getClassName() {
		return '\ultimate\system\clipboard\action\MenuClipboardAction';
	}
	
	/**
	 * Filters the given objects by the given type data and returns the filtered list.
	 * 
	 * @param	array	$objects
	 * @param	array	$typeData
	 * @return	array
	 */
	public function filterObjects(array $objects, array $typeData) {
		return $objects;
	}
	
	/**
	 * Validates the delete action.
	 *
	 * @param	\ultimate\data\menu\Menu[]	$objects
	 * @return	integer[]
	 */
	protected function validateDelete(array $objects) {
		// checking permission
		if (!WCF::getSession()->getPermission('admin.content.ultimate.canManageMenus')) {
			return array();
		}
	
		// prevent possible problems with array_keys
		if (empty($objects)) return array();
	
		// get ids
		$menuIDs = array_keys($objects);
		return $menuIDs;
	}
}
