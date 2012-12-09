<?php
namespace ultimate\acp\form;
use ultimate\data\template\Template;
use ultimate\data\template\TemplateAction;
use wcf\acp\form\ACPForm;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the UltimateTemplateEdit form.
 * 
 * This form is used to configure additional data for each template after the template itself has been created in VisualEditor.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateTemplateEditForm extends UltimateTemplateAddForm {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canEditTemplate'
	);
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.acp.form.ACPForm.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance';
	
	/**
	 * Contains the id of the edited template.
	 * @var integer
	 */
	public $templateID = 0;
	
	/**
	 * Contains the object of the edited template.
	 * @var \ultimate\data\template\Template
	 */
	public $template = null;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readParameters
	 */
	public function readParameters() {
		parent::readParameters();
		if (isset($_REQUEST['id'])) $this->templateID = intval($_REQUEST['id']);
		$template = new Template($this->templateID);
		if (!$template->__get('templateID')) {
			throw new IllegalLinkException();
		}
		
		$this->template = $template;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		parent::readData();
		// read template data
		$this->ultimateTemplateName = $this->template->__get('templateName');
		$this->showWidgetArea = $this->template->__get('showWidgetArea');
		$this->selectedMenu = $this->template->__get('menu')->__get('menuID');
		$this->selectedWidgetArea = $this->template->__get('widgetArea')->__get('widgetAreaID');
		$this->widgetAreaSide = $this->template->__get('widgetAreaSide');
		$this->blocks = $this->template->__get('blocks');
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
				'showWidgetArea' => $this->showWidgetArea
			),
			'menuID' => $this->selectedMenu,
			'widgetAreaID' => $this->selectedWidgetArea
		);
		$this->objectAction = new TemplateAction(array($this->templateID), 'update', $parameters);
		$this->objectAction->executeAction();
		
		$this->saved();
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
