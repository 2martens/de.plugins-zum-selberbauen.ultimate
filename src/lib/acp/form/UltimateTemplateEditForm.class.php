<?php
/**
 * The UltimateTemplateEdit form.
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
use ultimate\data\template\Template;
use ultimate\data\template\TemplateAction;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows the UltimateTemplateEdit form.
 * 
 * This form is used to configure additional data for each template after the template itself has been created in UltimateTemplateAddForm.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateTemplateEditForm extends UltimateTemplateAddForm {
	/**
	 * The active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance';
	
	/**
	 * The id of the edited template.
	 * @var integer
	 */
	public $templateID = 0;
	
	/**
	 * The object of the edited template.
	 * @var \ultimate\data\template\Template
	 */
	public $template = null;
	
	/**
	 * Reads parameters.
	 * @see UltimateTemplateAddForm::readParameters()
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
	 * Reads data.
	 * @see UltimateTemplateAddForm::readData()
	 */
	public function readData() {
		parent::readData();
		// read template data
		$this->ultimateTemplateName = $this->template->__get('templateName');
		$this->showWidgetArea = $this->template->__get('showWidgetArea');
		$menu = $this->template->__get('menu');
		
		if ($menu !== null) $this->selectedMenu = $menu->__get('menuID');
		else $this->selectedMenu = 0;
		
		$widgetArea = $this->template->__get('widgetArea');
		if ($widgetArea !== null) $this->selectedWidgetArea = $widgetArea->__get('widgetAreaID');
		else $this->selectedWidgetArea = 0;
		
		$this->widgetAreaSide = $this->template->__get('widgetAreaSide');
		$this->blocks = $this->template->__get('blocks');
	}
	
	/**
	 * Saves the form input.
	 * @see UltimateTemplateAddForm::save()
	 */
	public function save() {
		AbstractForm::save();
		$parameters = array(
			'data' => array(
				'templateName' => $this->ultimateTemplateName,
				'widgetAreaSide' => $this->widgetAreaSide,
				'showWidgetArea' => intval($this->showWidgetArea)
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
		
		$url = LinkHandler::getInstance()->getLink('UltimateTemplateEdit',
			array(
				'id' => $this->template->__get('templateID'),
				'application' => 'ultimate'
			)
		);
		HeaderUtil::redirect($url);
		// after initiating the redirect, no other code should be executed as the request for the original resource has ended
		exit;
	}
	
	/**
	 * Assigns the template variables.
	 * @see UltimateTemplateAddForm::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'templateID' => $this->templateID
		));
	}
}
