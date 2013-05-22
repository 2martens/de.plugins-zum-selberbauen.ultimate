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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\data\template\TemplateAction;
use ultimate\system\cache\builder\BlockTypeCacheBuilder;
use ultimate\system\cache\builder\MenuCacheBuilder;
use ultimate\system\cache\builder\WidgetAreaCacheBuilder;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows the UltimateTemplateAdd form.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateTemplateAddForm extends AbstractForm {
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
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$activeMenuItem
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
	 * Contains the height of new blocks.
	 * @var	integer
	 */
	protected $height = 0;
	
	/**
	 * Contains the width of new blocks.
	 * @var	integer
	 */
	protected $width = 1;
	
	/**
	 * Contains the relative distance from the left border for new blocks.
	 * @var integer
	 */
	protected $left = 1;
	
	/**
	 * Contains the amount of pixels from the top border for new blocks.
	 * @var integer
	 */
	protected $top = 0;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		// load cache
		$this->menus = MenuCacheBuilder::getInstance()->getData(array(), 'menus');
		$this->widgetAreas = WidgetAreaCacheBuilder::getInstance()->getData(array(), 'widgetAreas');
		$this->blocktypes = BlockTypeCacheBuilder::getInstance()->getData(array(), 'blockTypes');
		
		parent::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#readFormParameters
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_POST['templateName'])) $this->ultimateTemplateName = StringUtil::trim($_POST['templateName']);
		if (isset($_POST['showWidgetArea'])) $this->showWidgetArea = true;
		else $this->showWidgetArea = false;
		
		if (isset($_POST['widgetAreaSide'])) $this->widgetAreaSide = StringUtil::trim($_POST['widgetAreaSide']);
		if (isset($_POST['selectWidgetArea'])) $this->selectedWidgetArea = intval($_POST['selectWidgetArea']);
		if (isset($_POST['selectMenu'])) $this->selectedMenu = intval($_POST['selectMenu']);
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
		parent::save();
		$parameters = array(
			'data' => array(
				'templateName' => $this->ultimateTemplateName,
				'widgetAreaSide' => $this->widgetAreaSide,
				'showWidgetArea' => intval($this->showWidgetArea)
			),
			'menuID' => $this->selectedMenu,
			'widgetAreaID' => $this->selectedWidgetArea
		);
		$this->objectAction = new TemplateAction(array(), 'create', $parameters);
		$returnValues = $this->objectAction->executeAction();
		
		$this->saved();
		
		WCF::getTPL()->assign(
			'success', true
		);
		
		$url = LinkHandler::getInstance()->getLink('UltimateTemplateEdit',
			array(
				'id' => $returnValues['returnValues']->__get('templateID')
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
			'ultimateTemplateName' => $this->ultimateTemplateName,
			'showWidgetArea' => $this->showWidgetArea,
			'widgetAreas' => $this->widgetAreas,
			'widgetAreaSide' => $this->widgetAreaSide,
			'selectedWidgetArea' => $this->selectedWidgetArea,
			'menus' => $this->menus,
			'selectedMenu' => $this->selectedMenu,
			'blocks' => $this->blocks,
			'blocktypes' => $this->blocktypes,
			'width' => $this->width,
			'height' => $this->height,
			'left' => $this->left,
			'top' => $this->top,
			'action' => 'add'
		));
	}
	
	/**
	 * Validates the template name.
	 *
	 * @throws \wcf\system\exception\UserInputException	if template name is empty
	 */
	protected function validateTemplateName() {
		if (empty($this->ultimateTemplateName)) {
			throw new UserInputException('templateName');
		}
	}
	
	/**
	 * Validates the show widget area setting.
	 */
	protected function validateShowWidgetArea() {
		// does nothing
		// if validation is necessary in future the method already exists
	}
	
	/**
	 * Validates the widget area side.
	 *
	 * @throws \wcf\system\exception\UserInputException	if selected widget area side is neither left nor right
	 */
	protected function validateWidgetAreaSide() {
		$allowed = array('left', 'right');
		if (!in_array(strtolower($this->widgetAreaSide), $allowed)) {
			throw new UserInputException('widgetAreaSide', 'notValid');
		}
	}
	
	/**
	 * Validates the selected widget area.
	 *
	 * @throws \wcf\system\exception\UserInputException	if selected widget area doesn't exist
	 */
	protected function validateSelectWidgetArea() {
		if ($this->selectedWidgetArea && !isset($this->widgetAreas[$this->selectedWidgetArea])) {
			throw new UserInputException('selectWidgetArea', 'notValid');
		}
	}
	
	/**
	 * Validates the selected menu.
	 *
	 * @throws \wcf\system\exception\UserInputException	if selected menu doesn't exist
	 */
	protected function validateSelectMenu() {
		if ($this->selectedMenu && !isset($this->menus[$this->selectedMenu])) {
			throw new UserInputException('selectMenu', 'notValid');
		}
	}
}
