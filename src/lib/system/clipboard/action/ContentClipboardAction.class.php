<?php
/**
 * Contains the ContentClipboardAction class.
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
 * @subpackage	system.slipboard.action
 * @category	Ultimate CMS
 */
namespace ultimate\system\clipboard\action;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\IClipboardAction;
use wcf\system\clipboard\ClipboardEditorItem;
use wcf\system\exception\SystemException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Prepares the clipboard editor items for content objects.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.clipboard.action
 * @category	Ultimate CMS
 */
class ContentClipboardAction implements IClipboardAction {
	/**
	 * Returns type name identifier.
	 * 
	 * @return	string
	 */
	public function getTypeName() {
		return 'de.plugins-zum-selberbauen.ultimate.content';
	}
	
	/**
	 * Returns editor item for the clipboard action with the given name or null if the action is not applicable to the given objects.
	 * 
	 * @param	\ultimate\data\content\Content[]				$objects
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
			case 'assignContentToCategory':
				$item->setName('content.assignToCategory');
				$item->setURL(LinkHandler::getInstance()->getLink('UltimateContentAssignToCategory'));
				break;
			case 'deleteContent':
				$contentIDs = array();
				$contentIDs = $this->validateDelete($objects);
				if (empty($contentIDs)) {
					return null;
				}
				
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.content.delete.confirmMessage', array('count' => count($contentIDs))));
				$item->addParameter('actionName', 'delete');
				$item->addParameter('className', '\ultimate\data\content\ContentAction');
				$item->addParameter('objectIDs', $contentIDs);
				$item->setName('content.delete');
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
		return WCF::getLanguage()->getDynamicVariable('wcf.clipboard.label.content.marked', array('count' => count($objects)));
	}
	
	/**
	 * Returns action class name.
	 * 
	 * @return	string
	 */
	public function getClassName() {
		return '\ultimate\system\clipboard\action\ContentClipboardAction';
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
	 * @param	\ultimate\data\content\Content[]	$objects
	 * @return	integer[]
	 */
	protected function validateDelete(array $objects) {
		// checking permission
		if (!WCF::getSession()->getPermission('admin.content.ultimate.canDeleteContent')) {
			return array();
		}
		
		// prevent possible problems with array_keys
		if (empty($objects)) return array();
		
		// get ids
		$contentIDs = array_keys($objects);
		return $contentIDs;
	}
}
