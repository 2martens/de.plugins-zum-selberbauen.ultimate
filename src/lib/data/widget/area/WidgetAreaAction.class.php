<?php
/**
 * Contains the widget area data model action class.
 * 
 * Long desc
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
 * @subpackage	data.widget.area
 * @category	Ultimate CMS
 */
namespace ultimate\data\widget\area;
use ultimate\system\widget\WidgetHandler;

use wcf\data\dashboard\box\DashboardBoxList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISortableAction;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Executes widget area-related functions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.widget.area
 * @category	Ultimate CMS
 */
class WidgetAreaAction extends AbstractDatabaseObjectAction implements ISortableAction {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$className
	 */
	public $className = '\ultimate\data\widget\area\WidgetAreaEditor';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canManageWidgetAreas');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsDelete
	*/
	protected $permissionsDelete = array('admin.content.ultimate.canManageWidgetAreas');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsUpdate
	*/
	protected $permissionsUpdate = array('admin.content.ultimate.canManageWidgetAreas');
	
	/**
	 * list of available dashboard boxes
	 * @var	\wcf\data\dashboard\box\DashboardBox[]
	 */
	public $boxes = array();
	
	/**
	 * box structure
	 * @var	integer[]
	*/
	public $boxStructure = array();
	
	/**
	 * object type object
	 * @var	\wcf\data\object\type\ObjectType
	*/
	public $objectType = null;
	
	/**
	 * widget area id
	 * @var integer
	 */
	public $widgetAreaID = 0;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.ISortableAction.html#validateUpdatePosition
	 */
	public function validateUpdatePosition() {
		// validate permissions
		WCF::getSession()->checkPermissions($this->permissionsUpdate);
	
		$this->readString('boxType');
		$this->readInteger('widgetAreaID');
	
		// validate object type
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.user.dashboardContainer', 'de.plugins-zum-selberbauen.template');
		$this->widgetAreaID = $this->parameters['widgetAreaID'];
		
		// read all dashboard boxes of the relevant box type
		$boxList = new DashboardBoxList();
		$boxList->getConditionBuilder()->add("dashboard_box.boxType = ?", array($this->parameters['boxType']));
		$boxList->readObjects();
		$this->boxes = $boxList->getObjects();
		
		// parse structure
		if (isset($this->parameters['data']) & isset($this->parameters['data']['structure']) && isset($this->parameters['data']['structure'][0])) {
			$this->boxStructure = ArrayUtil::toIntegerArray($this->parameters['data']['structure'][0]);
			
			// validate box ids
			if (!empty($this->boxStructure)) {
				foreach ($this->boxStructure as $boxID) {
					if (!isset($this->boxes[$boxID])) {
						throw new UserInputException('boxID');
					}
				}
			}
		}
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.ISortableAction.html#updatePosition
	 */
	public function updatePosition() {
		// remove previous settings
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add("widgetAreaID = ?", array($this->widgetAreaID));
		if (!empty($this->boxes)) {
			$conditions->add("boxID IN (?)", array(array_keys($this->boxes)));
		}
		
		$sql = "DELETE FROM ultimate".WCF_N."_widget_area_option
		       ".$conditions;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());
		
		// update settings
		if (!empty($this->boxStructure)) {
			$sql = "INSERT INTO ultimate".WCF_N."_widget_area_option
			               (widgetAreaID, boxID, showOrder)
			        VALUES     (?, ?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
				
			WCF::getDB()->beginTransaction();
			foreach ($this->boxStructure as $index => $boxID) {
				$showOrder = $index + 1;
	
				$statement->execute(array(
					$this->widgetAreaID,
					$boxID,
					$showOrder
				));
			}
			WCF::getDB()->commitTransaction();
		}
		
		// reset cache
		WidgetHandler::clearCache();
	}
}
