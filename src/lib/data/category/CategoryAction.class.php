<?php
/**
 * Contains the category data model action class.
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
 * @subpackage	data.category
 * @category	Ultimate CMS
 */
namespace ultimate\data\category;
use ultimate\data\layout\LayoutAction;
use ultimate\data\layout\LayoutList;
use ultimate\system\layout\LayoutHandler;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes category-related actions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.category
 * @category	Ultimate CMS
 */
class CategoryAction extends AbstractDatabaseObjectAction {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$className
	 */
	public $className = '\ultimate\data\category\CategoryEditor';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddCategory');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canDeleteCategory');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canEditCategory');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#delete
	 */
	public function delete() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		// get ids
		$objectIDs = array();
		foreach ($this->objects as $object) {
			$objectIDs[] = $object->getObjectID();
		}
		
		$layoutIDs = array();
		foreach ($this->objects as $object) {
			/* @var $layout \ultimate\data\layout\Layout */
			$layout = null;
			if (defined('TESTING_MODE') && TESTING_MODE) {
				$layoutList = new LayoutList();
				$layoutList->readObjects();
				$layouts = $layoutList->getObjects();
				foreach ($layouts as $__layout) {
					if ($__layout->__get('objectID') == $object->__get('categoryID') && $__layout->__get('objectType') == 'category') {
						$layout = $__layout;
					}
				}
			}
			else {
				$layout = LayoutHandler::getInstance()->getLayoutFromObjectData($object->__get('categoryID'), 'category');
			}
			$layoutIDs[] = $layout->__get('layoutID');
		}
		$layoutAction = new LayoutAction($layoutIDs, 'delete', array());
		$layoutAction->executeAction();
		// execute action
		return call_user_func(array($this->className, 'deleteAll'), $objectIDs);
	}
}
