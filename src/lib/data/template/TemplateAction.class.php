<?php
/**
 * Contains the template data model action class.
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.template
 * @category	Ultimate CMS
 */
namespace ultimate\data\template;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use ultimate\data\block\BlockAction;

/**
 * Executes template-related actions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.ultimate.template
 * @category	Ultimate CMS
 */
class TemplateAction extends AbstractDatabaseObjectAction {
	/**
	 * The class name.
	 * @var	string
	 */
	public $className = '\ultimate\data\template\TemplateEditor';
	
	/**
	 * Array of permissions that are required for create action.
	 * @var	string[]
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canManageTemplates');
	
	/**
	 * Array of permissions that are required for delete action.
	 * @var	string[]
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canManageTemplates');
	
	/**
	 * Array of permissions that are required for update action.
	 * @var	string[]
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canManageTemplates');
	
	/**
	 * Creates a new template and returns a JS-friendly array.
	 * 
	 * @since	1.0.0
	 * @internal	Calls create.
	 * 
	 * @return int[]|string[]
	 */
	public function createAJAX() {
		/* @var $template \ultimate\data\template\Template */
		$template = $this->create();
		$returnArray = array(
			'templateID' => $template->__get('templateID'),
			'templateName' => $template->getTitle()
		);
		$this->action = 'create';
		return $returnArray;
	}
	
	/**
	 * Validates the 'createAJAX' action.
	 * 
	 * @since	1.0.0
	 * @internal	Calls validateCreate.
	 */
	public function validateCreateAJAX() {
		$this->validateCreate();
	}
	
	/**
	 * Creates a template.
	 * 
	 * @return	Template
	 */
	public function create() {
		$template = parent::create();
		$templateID = $template->__get('templateID');
		$this->objectIDs = array($templateID);
		$this->updateRelations();
		return $template;
	}
	
	/**
	 * Updates one or more objects.
	 */
	public function update() {
		parent::update();
		$this->updateRelations();
	}
	
	/**
	 * Deletes a template.
	 * 
	 * @return	integer
	 */
	public function delete() {
		// determine connected blocks
		$affectedBlocks = array();
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('templateID IN (?)', array($this->objectIDs));
		$sql = 'SELECT blockID
		        FROM   ultimate'.WCF_N.'_block_to_template
		        '.$conditionBuilder;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		
		while ($row = $statement->fetchArray()) {
			$affectedBlocks[] = $row['blockID'];
		}
		
		// call parent
		$affectedCount = parent::delete();
		
		// remove connected blocks
		$action = new BlockAction($affectedBlocks, 'delete');
		$action->executeAction();
		
		return $affectedCount;
	}
	
	/**
	 * Updates the relations (menu_to_template and widget_area_to_template) 
	 * for all templates that have their IDs in $this->objectIDs.
	 */
	protected function updateRelations() {
		// delete existing entries
		$sql = 'DELETE FROM ultimate'.WCF_N.'_menu_to_template
		        WHERE       templateID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($this->objectIDs as $objectID) {
			$statement->executeUnbuffered(array($objectID));
		}
		WCF::getDB()->commitTransaction();
		
		$sql = 'DELETE FROM ultimate'.WCF_N.'_widget_area_to_template
		        WHERE       templateID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($this->objectIDs as $objectID) {
			$statement->executeUnbuffered(array($objectID));
		}
		WCF::getDB()->commitTransaction();
		
		// insert new entries
		$sql = 'INSERT INTO ultimate'.WCF_N.'_menu_to_template
		               (menuID, templateID)
		        VALUES (?, ?)';
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($this->objectIDs as $objectID) {
			if (!$this->parameters['menuID']) continue;
			$statement->executeUnbuffered(array(
				$this->parameters['menuID'],
				$objectID
			));
		}
		WCF::getDB()->commitTransaction();
		
		$sql = 'INSERT INTO ultimate'.WCF_N.'_widget_area_to_template
		               (templateID, widgetAreaID)
		        VALUES (?, ?)';
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($this->objectIDs as $objectID) {
			if (!$this->parameters['widgetAreaID']) continue;
			$statement->executeUnbuffered(array(
				$objectID,
				$this->parameters['widgetAreaID']
			));
		}
		WCF::getDB()->commitTransaction();
	}
}
