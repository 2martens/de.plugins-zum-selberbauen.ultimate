<?php
/**
 * Contains the page data model action class.
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
 * @subpackage	data.page
 * @category	Ultimate CMS
 */
namespace ultimate\data\page;
use wcf\util\ArrayUtil;

use wcf\system\exception\ValidateActionException;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\database\util\PreparedStatementConditionBuilder;

/**
 * Executes page-related actions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.page
 * @category	Ultimate CMS
 */
class PageAction extends AbstractDatabaseObjectAction {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$className
	 */
	public $className = 'ultimate\data\page\PageEditor';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddPage');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canDeletePage');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canEditPage');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#create
	 */
	public function create() {
		$page = parent::create();
		$pageEditor = new PageEditor($page);
		
		// connect with content
		$contentID = (isset($this->parameters['contentID'])) ? intval($this->parameters['contentID']) : 0;
		$pageEditor->addContent($contentID, false);
		
		// connect with userGroups
		$groupIDs = (isset($this->parameters['groupIDs'])) ? ArrayUtil::toIntegerArray($this->parameters['groupIDs']) : array();
		if (!empty($groupIDs)) {
			$pageEditor->addGroups($groupIDs, false);
		}
		return $page;
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
		
		$contentID = (isset($this->parameters['contentID'])) ? intval($this->parameters['contentID']) : 0;
		$groupIDs = (isset($this->parameters['groupIDs'])) ? ArrayUtil::toIntegerArray($this->parameters['groupIDs']) : array();
		
		foreach ($this->objects as $pageEditor) {
			/* @var $pageEditor \ultimate\data\page\PageEditor */
			if ($contentID) {
				$pageEditor->addContent($contentID);
			}
			
			if (!empty($groupIDs)) {
				$pageEditor->addGroups($groupIDs);
			}
		}
	}
}
