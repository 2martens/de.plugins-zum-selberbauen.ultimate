<?php
/**
 * Contains the content data model action class.
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
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\ValidateActionException;
use wcf\util\ArrayUtil;

/**
 * Executes content-related functions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class ContentAction extends AbstractDatabaseObjectAction {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$className
	 */
	public $className = '\ultimate\data\content\ContentEditor';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddContent');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canDeleteContent');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canEditContent');
	
	/**
	 * Creates new content.
	 * 
	 * @return	Content
	 */
	public function create() {
		$content = parent::create();
		$contentEditor = new ContentEditor($content);
		
		// insert categories
		$categoryIDs = (isset($this->parameters['categories'])) ? $this->parameters['categories'] : array();
		$contentEditor->addToCategories($categoryIDs, false);
		
		// connect with userGroups
		$groupIDs = (isset($this->parameters['groupIDs'])) ? ArrayUtil::toIntegerArray($this->parameters['groupIDs']) : array();
		if (!empty($groupIDs)) {
			$contentEditor->addGroups($groupIDs);
		}
		
		return $content;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#update
	 */
	public function update() {
		if (isset($this->parameters['data'])) {
			parent::update();
		}
		else {
			if (empty($this->objects)) {
				$this->readObjects();
			}
		}
		
		$categoryIDs = (isset($this->parameters['categories'])) ? $this->parameters['categories'] : array();
		$removeCategories = (isset($this->parameters['removeCategories'])) ? $this->parameters['removeCategories'] : array();
		$groupIDs = (isset($this->parameters['groupIDs'])) ? ArrayUtil::toIntegerArray($this->parameters['groupIDs']) : array();
		
		foreach ($this->objects as $contentEditor) {
			/* @var $contentEditor \ultimate\data\content\ContentEditor */
			if (!empty($categoryIDs)) {
				$contentEditor->addToCategories($categoryIDs);
			}
			
			if (!empty($removeCategories)) {
				$contentEditor->removeFromCategories($removeCategories);
			}
			
			if (!empty($groupIDs)) {
				$contentEditor->addGroups($groupIDs);
			}
		}
	}
}
