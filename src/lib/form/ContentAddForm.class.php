<?php
/**
 * Contains the ContentAddForm class.
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
 * Provides a form to add a new content.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	form
 * @category	Ultimate CMS
 */
class ContentAddForm extends AbstractForm implements IEditSuitePage {
	/**
	 * name of the template for the called page
	 * @var	string
	 */
	public $templateName = 'editSuite';
	
	/**
	 * indicates if you need to be logged in to access this page
	 * @var	boolean
	 */
	public $loginRequired = true;
	
	/**
	 * enables template usage
	 * @var	string
	 */
	public $useTemplate = true;
	
	/**
	 * A list of active EditSuite menu items.
	 * @var string[]
	 */
	protected $activeMenuItems = array(
		'ContentAddForm',
		'ultimate.edit.contents'
	);
	
	/**
	 * @see \ultimate\page\IEditSuitePage::getActiveMenuItems()
	*/
	public function getActiveMenuItems() {
		return $this->activeMenuItems;
	}
	
	/**
	 * Assigns variables to the template engine.
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'activeMenuItems' => $this->activeMenuItems,
			'pageContent' => WCF::getTPL()->fetch('__editSuite.ContentAddForm', 'ultimate')
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
