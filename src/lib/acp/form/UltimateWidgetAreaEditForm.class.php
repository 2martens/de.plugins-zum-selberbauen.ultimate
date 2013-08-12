<?php
/**
 * The UltimateWidgetAreaEditForm class.
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
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\data\widget\area\WidgetAreaAction;
use ultimate\data\widget\area\WidgetArea;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows the UltimateWidgetAreaEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateWidgetAreaEditForm extends UltimateWidgetAreaAddForm {
	/**
	 * The active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance';
	
	/**
	 * The widget area id.
	 * @var	integer
	 */
	public $widgetAreaID = 0;
	
	/**
	 * The WidgetArea object.
	 * @var	\ultimate\data\widget\area\WidgetArea
	 */
	public $widgetArea = null;
	
	/**
	 * Reads parameters.
	 * @see UltimateWidgetAreaAddForm::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
	
		if (isset($_REQUEST['id'])) $this->widgetAreaID = intval($_REQUEST['id']);
		$widgetArea = new WidgetArea($this->widgetAreaID);
		if (!$widgetArea->__get('widgetAreaID')) {
			throw new IllegalLinkException();
		}
	
		$this->widgetArea = $widgetArea;
	}
	
	/**
	 * Reads data.
	 * @see UltimateWidgetAreaAddForm::readData()
	 */
	public function readData() {
		// reading object fields
		$this->widgetAreaName = $this->widgetArea->__get('widgetAreaName');
		
		// load settings
		$sql = "SELECT      boxID
		        FROM        ultimate".WCF_N."_widget_area_option
		        WHERE       widgetAreaID = ?
		        ORDER BY    showOrder ASC";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->widgetAreaID
		));
		while ($row = $statement->fetchArray()) {
			$this->enabledBoxes[] = $row['boxID'];
		}
		
		AbstractForm::readData();
	}
	
	/**
	 * Saves the form input.
	 * @see UltimateWidgetAreaAddForm::save()
	 */
	public function save() {
		AbstractForm::save();
	
		$parameters = array(
			'data' => array(
				'widgetAreaName' => $this->widgetAreaName
			)
		);
	
		$this->objectAction = new WidgetAreaAction(array($this->widgetAreaID), 'update', $parameters);
		$this->objectAction->executeAction();
		
		// remove previous settings
		$sql = "DELETE FROM ultimate".WCF_N."_widget_area_option
		        WHERE       widgetAreaID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->widgetAreaID
		));
		
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
					$this->widgetAreaID,
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
	}
	
	/**
	 * Assigns the template variables.
	 * @see	UltimateWidgetAreaAddForm::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
	
		WCF::getTPL()->assign(array(
			'widgetAreaID' => $this->widgetAreaID,
			'action' => 'edit'
		));
	}
}
