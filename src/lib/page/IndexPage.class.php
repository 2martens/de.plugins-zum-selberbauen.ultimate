<?php
/**
 * Contains the IndexPage class.
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
 * @subpackage	page
 * @category	Ultimate CMS
 */
namespace ultimate\page;
use ultimate\system\layout\LayoutHandler;
use ultimate\system\template\TemplateHandler;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows the index page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
class IndexPage extends AbstractPage {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$useTemplate
	 * @var	boolean
	 */
	public $useTemplate = false;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededModules
	 * @var	string[]
	 */
	public $neededModules = array();
	
	/**
	 * The output.
	 * @var string
	 */
	public $output = '';
	
	/**
	 * The layout.
	 * @var \ultimate\data\layout\Layout
	 */
	public $layout = null;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		parent::readData();
		$this->layout = LayoutHandler::getInstance()->getLayoutFromObjectData(0, 'index');
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		// get output
		$this->output = TemplateHandler::getInstance()->getOutput('index', $this->layout, null, $this);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#show
	 */
	public function show() {
		parent::show();
		HeaderUtil::sendHeaders();
		echo $this->output;
		exit;
	}
}
