<?php
/**
 * Contains the UltimateTemplateAdd form.
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
use wcf\acp\form\ACPForm;
use wcf\system\WCF;

/**
 * Insert description here.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateTemplateAddForm extends ACPForm {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$templateName
	 */
	public $templateName = 'ultimateTemplateAdd';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canAddTemplate'
	);
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.acp.form.ACPForm.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance.template.add';
	
	/**
	 * Contains the name of the edited template.
	 *
	 * Has prefix ultimate to prevent overriding the parent's property.
	 * @var string
	 */
	public $ultimateTemplateName = '';
	
	/**
	 * If true the widget area is shown.
	 * @var boolean
	 */
	public $showWidgetArea = true;
	
	/**
	 * Contains all read widget area configurations.
	 * @var \ultimate\data\widget\area\WidgetArea[]
	 */
	public $widgetAreas = array();
	
	/**
	 * Contains the id of the selected widget area.
	 * @var integer
	*/
	public $selectedWidgetArea = 0;
	
	/**
	 * Contains the widget area side.
	 * @var string
	 */
	public $widgetAreaSide = 'right';
	
	/**
	 * Contains all read custom menus.
	 * @var \ultimate\data\menu\Menu[]
	 */
	public $menus = array();
	
	/**
	 * Contains the id of the selected custom menu.
	 * @var integer
	*/
	public $selectedMenu = 0;
	
	/**
	 * Contains all blocks of this template.
	 * @var \ultimate\data\block\Block[]
	 */
	public $blocks = array();
	
	/**
	 * Contains all block types.
	 * @var \ultimate\data\blocktype\Blocktype[]
	 */
	public $blocktypes = array();
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		// load cache
		$cacheName = 'menu';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\MenuCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->menus = CacheHandler::getInstance()->get($cacheName, 'menus');
		
		$cacheName = 'widget-area';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\WidgetAreaCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->widgetAreas = CacheHandler::getInstance()->get($cacheName, 'widgetAreas');
		
		$cacheName = 'blocktype';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\BlockTypeCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->blocktypes = CacheHandler::getInstance()->get($cacheName, 'blockTypes');
		
		parent::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#readFormParameters
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_POST['templateName'])) $this->ultimateTemplateName = StringUtil::trim($_POST['templateName']);
		if (isset($_POST['showWidgetArea'])) $this->showWidgetArea = (boolean) intval($_POST['showWidgetArea']);
		if (isset($_POST['widgetAreaSide'])) $this->widgetAreaSide = StringUtil::trim($_POST['widgetAreaSide']);
		if (isset($_POST['selectWidgetArea'])) $this->selectedWidgetArea = intval($_POST['selectWidgetArea']);
		if (isset($_POST['selectMenu'])) $this->selectedMenu = intval($_POST['selectMenu']);
		// TODO: add missing block statements (object variable, readFormParameters, validate, save)
		// TODO: create a blocktype transfer JS "class" 
	}
	
	/**
	 * @throws	\wcf\system\exception\UserInputException	on wrong/malformed user input
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#validate
	 */
	public function validate() {
		parent::validate();
		$this->validateTemplateName();
		$this->validateShowWidgetArea();
		$this->validateWidgetAreaSide();
		$this->validateSelectWidgetArea();
		$this->validateSelectMenu();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		ACPForm::save();
		$parameters = array(
			'data' => array(
				'templateName' => $this->ultimateTemplateName,
				'widgetAreaSide' => $this->widgetAreaSide,
				'showWidgetArea' => $this->showWidgetArea
			),
			'menuID' => $this->selectedMenu,
			'widgetAreaID' => $this->selectedWidgetArea
		);
		$this->objectAction = new TemplateAction(array(), 'create', $parameters);
		$this->objectAction->executeAction();
		
		$this->saved();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'ultimateTemplateName' => $this->ultimateTemplateName,
			'showWidgetArea' => $this->showWidgetArea,
			'widgetAreas' => $this->widgetAreas,
			'widgetAreaSide' => $this->widgetAreaSide,
			'selectedWidgetArea' => $this->selectedWidgetArea,
			'menus' => $this->menus,
			'selectedMenu' => $this->selectedMenu,
			'blocks' => $this->blocks,
			'blocktypes' => $this->blocktypes
		));
	}
}
