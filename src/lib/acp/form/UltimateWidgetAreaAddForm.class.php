<?php
/**
 * The UltimateWidgetAreaAdd form.
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
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\data\widget\area\WidgetAreaAction;
use ultimate\data\widget\WidgetNodeList;
use ultimate\system\cache\builder\WidgetTypeCacheBuilder;
use wcf\acp\form\DashboardOptionForm;
use wcf\data\dashboard\box\DashboardBoxList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows the UltimateWidgetAreaAdd form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateWidgetAreaAddForm extends DashboardOptionForm {
	/**
	 * The template name.
	 * @var	string
	 */
	public $templateName = 'ultimateWidgetAreaAdd';
	
	/**
	 * An array of needed permissions.
	 * @var	string[]
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canManageWidgetAreas'
	);
	
	/**
	 * The active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance.widgetArea.add';
	
	/**
	 * The widget area name.
	 * @var string
	 */
	public $widgetAreaName = '';
	
	/**
	 * Reads the parameters.
	 */
	public function readParameters() {
		AbstractForm::readParameters();
		
		// load object type
		$this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.user.dashboardContainer', 'de.plugins-zum-selberbauen.ultimate.template');
		$this->objectTypeID = $this->objectType->__get('objectTypeID');
		if ($this->objectType === null) {
			throw new IllegalLinkException();
		}
		
		$boxList = new DashboardBoxList();
		$boxList->getConditionBuilder()->add("dashboard_box.boxType IN (?)", array('sidebar'));
		$boxList->readObjects();
		$this->boxes = $boxList->getObjects();
	}
	
	/**
	 * Reads the form input.
	 */
	public function readFormParameters() {
		parent::readFormParameters();
	
		if (isset($_POST['widgetAreaName'])) $this->widgetAreaName = StringUtil::trim($_POST['widgetAreaName']);
	}
	
	/**
	 * Validates the input.
	 */
	public function validate() {
		parent::validate();
		$this->validateName();
	}
	
	/**
	 * Saves the input.
	 */
	public function save() {
		AbstractForm::save();
	
		$parameters = array(
			'data' => array(
				'widgetAreaName' => $this->widgetAreaName
			)
		);
	
		$this->objectAction = new WidgetAreaAction(array(), 'create', $parameters);
		$this->objectAction->executeAction();
	
		$returnValues = $this->objectAction->getReturnValues();
		$widgetAreaID = $returnValues['returnValues']->__get('widgetAreaID');
		
		// insert new settings
		if (!empty($this->enabledBoxes)) {
			$sql = "INSERT INTO ultimate".WCF_N."_widget_area_option
			               (widgetAreaID, boxID, showOrder)
			        VALUES (?, ?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
				
			WCF::getDB()->beginTransaction();
			$showOrder = 1;
			foreach ($this->enabledBoxes as $boxID) {
				$statement->execute(array(
					$widgetAreaID,
					$boxID,
					$showOrder
				));
		
				$showOrder++;
			}
			WCF::getDB()->commitTransaction();
		}
		
		$this->saved();
		
		WCF::getTPL()->assign(
			'success', true
		);
		
		$url = LinkHandler::getInstance()->getLink('UltimateWidgetAreaEdit',
			array(
				'id' => $widgetAreaID
			)
		);
		HeaderUtil::redirect($url);
		exit;
	}
	
	/**
	 * Assigns template variables.
	 */
	public function assignVariables() {
		parent::assignVariables();
	
		WCF::getTPL()->assign(array(
			'widgetAreaName' => $this->widgetAreaName,
			'action' => 'add'
		));
	}
	
	/**
	 * Validates the widget area name.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateName() {
		if (empty($this->widgetAreaName)) {
			throw new UserInputException('widgetAreaName');
		}
		// TODO: WidgetAreaUtil
// 		if (!WidgetAreaUtil::isAvailableName($this->widgetAreaName)) {
// 			throw new UserInputException('widgetAreaName', 'notUnique');
// 		}
	}
}
