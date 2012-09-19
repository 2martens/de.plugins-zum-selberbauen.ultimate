<?php
/**
 * Contains the UltimateWidgetAreaAdd form.
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
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\data\widget\WidgetNodeList;
use ultimate\data\widget\area\WidgetAreaAction;
use wcf\acp\form\ACPForm;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\UserInputException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows the UltimateWidgetAreaAdd form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateWidgetAreaAddForm extends ACPForm {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$templateName
	 */
	public $templateName = 'ultimateWidgetAreaAdd';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canAddWidgetArea'
	);
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.acp.form.ACPForm.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance.widgetArea.add';
	
	/**
	 * Contains the widget area name.
	 * @var string
	 */
	public $widgetAreaName = '';
	
	/**
	 * Contains the WidgetNodeList.
	 * @var	\ultimate\data\widget\WidgetNodeList
	 */
	public $widgetNodeList = null;
	
	/**
	 * Contains all widget types.
	 * @var \ultimate\data\widgettype\WidgetType[]
	 */
	public $widgetTypes = array();
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		$this->widgetNodeList = new WidgetNodeList(0, 0, true);
		// read widget cache
		$cacheName = 'widget-type';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\WidgetTypeCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->widgetTypes = CacheHandler::getInstance()->get($cacheName, 'widgetTypes');
		
		parent::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#readFormParameters
	 */
	public function readFormParameters() {
		parent::readFormParameters();
	
		if (isset($_POST['widgetAreaName'])) $this->widgetAreaName = StringUtil::trim($_POST['widgetAreaName']);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#validate
	 */
	public function validate() {
		parent::validate();
		$this->validateName();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		parent::save();
	
		$parameters = array(
			'data' => array(
				'widgetAreaName' => $this->widgetAreaName
			)
		);
	
		$this->objectAction = new WidgetAreaAction(array(), 'create', $parameters);
		$this->objectAction->executeAction();
	
		$returnValues = $this->objectAction->getReturnValues();
		$widgetAreaID = $returnValues['returnValues']->__get('widgetAreaID');
		$updateValues = array();
	
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
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
	
		WCF::getTPL()->assign(array(
			'widgetAreaName' => $this->widgetAreaName,
			'widgetNodeList' => $this->widgetNodeList,
			'widgetTypes' => $this->widgetTypes,
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
		if (!WidgetAreaUtil::isAvailableName($this->widgetAreaName)) {
			throw new UserInputException('widgetAreaName', 'notUnique');
		}
	}
}
