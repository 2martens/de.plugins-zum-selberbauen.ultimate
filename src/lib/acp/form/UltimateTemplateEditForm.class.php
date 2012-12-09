<?php
namespace ultimate\acp\form;
use ultimate\data\template\Template;
use ultimate\data\template\TemplateAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

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
		if (!$this->templateID) {
			throw new IllegalLinkException();
		}
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
		
		WCF::getTPL()->assign(
			'success', true
		);
	}
}
