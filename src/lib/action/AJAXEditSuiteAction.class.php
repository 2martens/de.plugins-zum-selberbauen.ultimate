<?php
/**
 * Contains AJAXEditSuiteAction class.
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
 * @subpackage	action
 * @category	Ultimate CMS
 */
namespace ultimate\action;
use wcf\action\AbstractSecureAction;
use wcf\action\AJAXInvokeAction;
use wcf\util\StringUtil;

/**
 * This class is the server-side counterpart for the AJAX Edit Suite.
 * 
 * Upon AJAX request, the controller-specific HTML will be loaded (via template engine)
 * and then returned via JSON to the frontend.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	action
 * @category	Ultimate CMS
 */
class AJAXEditSuiteAction extends AJAXInvokeAction {
	/**
	 * indicates if you need to be logged in to execute this action
	 * @var	boolean
	 */
	public $loginRequired = true;
	
	/**
	 * needed modules to execute this action
	 * @var	string[]
	 */
	public $neededModules = array();
	
	/**
	 * needed permissions to execute this action
	 * @var	string[]
	*/
	public $neededPermissions = array();
	
	/**
	 * The requested controller. 
	 * @var string
	 */
	public $controller = '';
	
	/**
	 * The request type.
	 * @var string
	 */
	public $requestType = '';
	
	/**
	 * Reads the given parameters.
	 */
	public function readParameters() {
		AbstractSecureAction::readParameters();
		
		if (isset($_POST['actionName'])) $this->actionName = StringUtil::trim($_POST['actionName']);
		if (isset($_POST['controller'])) $this->controller = StringUtil::trim($_POST['controller']);
		if (isset($_POST['requestType'])) $this->requestType = StringUtil::trim($_POST['requestType']);
	}
	
	/**
	 * Returns controller output.
	 */
	protected function invoke() {
		$classNameFQCN = 'ultimate\\'.$this->requestType.'\\'.$this->controller;
		
		/* @var $controllerObj \ultimate\page\IEditSuitePage */
		$controllerObj = new $classNameFQCN();
		$this->{$this->actionName}($controllerObj);
	}
	
	/**
	 * Performs a JS only request.
	 * 
	 * @param	\ultimate\page\IEditSuitePage	$controllerObj
	 */
	protected function jsOnly($controllerObj) {
		$this->response = array(
			'controller' => $this->controller,
			'requestType' => $this->requestType,
			'js' => $controllerObj->getJavascript()
		);
	}
	
	/**
	 * Performs a full HTML request.
	 * 
	 * @param	\ultimate\page\IEditSuitePage	$controllerObj
	 */
	protected function fullHTML($controllerObj) {
		$tmpPost = $_POST;
		$_POST = array();
		
		ob_start();
		$controllerObj->useTemplate = false;
		$controllerObj->__run();
		$output = ob_get_contents();
		ob_end_clean();
		$_POST = $tmpPost;
		
		$this->response = array(
			'controller' => $this->controller,
			'requestType' => $this->requestType,
			'html' => $output,
			'js' => $controllerObj->getJavascript(),
			'activeMenuItems' => $controllerObj->getActiveMenuItems()
		);
	}
}
