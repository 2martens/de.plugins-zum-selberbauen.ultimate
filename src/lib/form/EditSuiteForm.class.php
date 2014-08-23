<?php
/**
 * Contains the EditSuiteForm class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	form
 * @category	Ultimate CMS
 */
namespace ultimate\form;
use ultimate\page\IEditSuitePage;
use wcf\form\AbstractForm;
use wcf\system\WCF;

/**
 * This is the framing class for all forms in the edit suite.
 * 
 * The whole edit suite is JS-only and works with AJAX. The specific forms and pages (for example lists)
 * are loaded via AJAX as Action. To allow non-JS testing, every form and page will get a proper class in
 * the form or page directory. The Action class used for AJAX will relay the information to the Form or Page class
 * and return the template output. This procedure allows for a non-JS interaction while making use of performance improvements via AJAX. 
 * 
 * The whole edit suite is designed on a modular basis so that it is easy to add more editing features later on.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	form
 * @category	Ultimate CMS
 */
class EditSuiteForm extends AbstractForm implements IEditSuitePage {
	/**
	 * name of the template for the called page
	 * @var	string
	 */
	public $templateName = 'editSuite';
	
	/**
	 * name of the active menu item
	 * @var	string
	 */
	public $activeMenuItem = '';
	
	/**
	 * indicates if you need to be logged in to access this page
	 * @var	boolean
	 */
	public $loginRequired = true;
	
	/**
	 * needed modules to view this page
	 * @var	string[]
	 */
	public $neededModules = array();
	
	/**
	 * needed permissions to view this page
	 * @var	string[]
	 */
	public $neededPermissions = array();
	
	/**
	 * A list of active EditSuite menu items.
	 * @var string[]
	 */
	protected $activeMenuItems = array();
	
	/**
	 * @see \ultimate\page\IEditSuitePage::getActiveMenuItems()
	 */
	public function getActiveMenuItems() {
		return $this->activeMenuItems;
	}
	
	/**
	 * @see \ultimate\page\IEditSuitePage::getJavascript()
	 */
	public function getJavascript() {
		return WCF::getTPL()->fetch('__editSuiteJS.Main', 'ultimate');
	}
	
	/**
	 * Assigns variables to the template engine.
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'activeMenuItems' => $this->activeMenuItems,
			'pageContent' => WCF::getTPL()->fetch('__editSuite.Main', 'ultimate'),
			'pageJS' => WCF::getTPL()->fetch('__editSuiteJS.Main', 'ultimate'),
			'initialController' => 'EditSuiteForm',
			'initialRequestType' => 'form',
		    'initialURL' => '/EditSuite/'
		));
	}
	
	/**
	 * Shows the requested page.
	 */
	public function show() {
		parent::show();
		if (!$this->useTemplate) {
			WCF::getTPL()->display($this->templateName, 'ultimate', false);
		}
	}
}
