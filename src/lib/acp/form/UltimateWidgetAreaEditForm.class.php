<?php
/**
 * Contains the UltimateWidgetAreaEditForm class.
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
use ultimate\data\widget\WidgetNodeList;
use ultimate\data\widget\area\WidgetArea;
use ultimate\system\cache\builder\WidgetTypeCacheBuilder;
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
	 * @var	string
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance';
	
	/**
	 * Contains the widget area id.
	 * @var	integer
	 */
	public $widgetAreaID = 0;
	
	/**
	 * Contains the WidgetArea object.
	 * @var	\ultimate\data\widget\area\WidgetArea
	 */
	public $widgetArea = null;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readParameters
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
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
	
		// reading object fields
		$this->widgetAreaName = $this->widgetArea->__get('widgetAreaName');
		$this->widgetNodeList = new WidgetNodeList($this->widgetAreaID, true);
			
		// read widget type cache
		$this->widgetTypes = WidgetTypeCacheBuilder::getInstance()->getData(array(), 'widgetTypes');
			
		AbstractForm::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		AbstractForm::save();
	
		$parameters = array(
			'data' => array(
				'widgetAreaName' => $this->widgetAreaName
			)
		);
	
		$this->objectAction = new MenuAction(array($this->widgetAreaID), 'update', $parameters);
		$this->objectAction->executeAction();
	
		$this->saved();
	
		WCF::getTPL()->assign(
			'success', true
		);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
	
		WCF::getTPL()->assign(array(
			'widgetAreaID' => $this->widgetAreaID,
			'action' => 'edit'
		));
	}
}
