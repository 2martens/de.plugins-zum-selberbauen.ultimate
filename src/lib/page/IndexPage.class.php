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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
namespace ultimate\page;
use ultimate\system\layout\LayoutHandler;
use ultimate\system\template\TemplateHandler;
use wcf\page\AbstractPage;
use wcf\util\HeaderUtil;

/**
 * Shows the index page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
class IndexPage extends AbstractPage {
	/**
	 * If true, the template shall be used.
	 * @var	boolean
	 */
	public $useTemplate = false;
	
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
	 * Reads/Gets the data to be displayed on this page.
	 */
	public function readData() {
		parent::readData();
		$this->layout = LayoutHandler::getInstance()->getLayoutFromObjectData(0, 'index');
	}
	
	/**
	 * Assigns variables to the template engine.
	 */
	public function assignVariables() {
		parent::assignVariables();
		// get output
		$this->output = TemplateHandler::getInstance()->getOutput('index', $this->layout, null, $this);
	}
	
	/**
	 * Shows the requested page.
	 */
	public function show() {
		parent::show();
		HeaderUtil::sendHeaders();
		echo $this->output;
	}
}
