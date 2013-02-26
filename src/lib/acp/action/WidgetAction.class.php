<?php
/**
 * Contains the WidgetAction.
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
 * @subpackage	acp.action
 * @category	Ultimate CMS
 */
namespace ultimate\acp\action;
use ultimate\system\cache\builder\WidgetCacheBuilder;
use ultimate\system\widgettype\WidgetTypeHandler;
use wcf\action\AbstractSecureAction;
use wcf\action\AJAXProxyAction;
use wcf\system\event\EventHandler;
use wcf\system\exception\UserInputException;
use wcf\util\JSON;

/**
 * Handles widget actions initiated in the UltimateWidgetAreaEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.action
 * @category	Ultimate CMS
 */
class WidgetAction extends AJAXProxyAction {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.action.AJAXProxyAction.html#$className
	 */
	protected $className = '\ultimate\data\widget\WidgetAction';
	
	/**
	 * Contains all read widgets.
	 * @var \ultimate\data\widget\Widget
	 */
	protected $widgets = array();
	
	/**
	 * Contains the determined widget type.
	 * @var \ultimate\system\widgettype\AbstractWidgetType
	 */
	protected $widgetType = null;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.action.IAction.html#execute
	 */
	public function execute() {
		AbstractSecureAction::execute();
		
		$this->loadCache();
		
		// call method
		$this->determineWidgetType();
		
		$this->{$this->actionName}();
	
		$this->executed();
		
		// send JSON-encoded response
		header('Content-type: application/json');
		echo JSON::encode($this->response);
		exit;
	}
	
	/**
	 * Determines the widget type.
	 * 
	 * @internal	Saves the widget type object as object variable.
	 */
	protected function determineWidgetType() {
		$widgetTypeID = 0;
		if (isset($this->parameters['widgetTypeID'])) $widgetTypeID = intval($this->parameters['widgetTypeID']);
		else {
			/* @var $widget \ultimate\data\widget\Widget */
			$widget = $this->widgets[$this->parameters['widgetID']];
			$widgetTypeID = $widget->__get('widgetTypeID');
		}
		$this->widgetType = WidgetTypeHandler::getInstance()->getWidgetType($widgetTypeID);
	}
	
	/**
	 * Loads the widget options html.
	 */
	protected function loadWidgetOptions() {
		$widgetID = $this->parameters['widgetID'];
		$this->widgetType->init($widgetID);
		$this->response = $this->widgetType->getOptionsHTML();
	}
	
	/**
	 * Saves the widget options.
	 */
	protected function saveWidgetOptions() {
		$parameters = array(
			'data' => array(
				'additionalData' => $this->parameters['settings']
			)
		);
		$this->objectAction = new $this->className(array($this->parameters['widgetID']), 'update', $parameters);
		
		// validate action
		try {
			$this->objectAction->validateAction();
		}
		catch (UserInputException $e) {
			$this->throwException($e);
		}
		catch (ValidateActionException $e) {
			$this->throwException($e);
		}
		
		// execute action
		try {
			$this->response = $this->objectAction->executeAction();
		}
		catch (\Exception $e) {
			$this->throwException($e);
		}
	}
	
	/**
	 * Loads the cache.
	 */
	protected function loadCache() {
		$this->widgets = WidgetCacheBuilder::getInstance()->getData(array(), 'widgets');
	}
}
