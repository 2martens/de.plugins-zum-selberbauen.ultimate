<?php
/**
 * Contains the WidgetHandler class.
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
 * @subpackage	system.widget
 * @category	Ultimate CMS
 */
namespace ultimate\system\widget;
use ultimate\system\cache\builder\WidgetCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Handles widgets.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.widget
 * @category	Ultimate CMS
 */
class WidgetHandler extends SingletonFactory {
	/**
	 * Contains the cached widgets.
	 * @var	\ultimate\data\widget\Widget[]
	 */
	protected $widgets = array();
	
	/**
	 * Returns all widget objects.
	 *
	 * @since	1.0.0
	 * @api
	 *
	 * @return	\ultimate\data\widget\Widget[]
	*/
	public function getWidgets() {
		return $this->widgets;
	}
	
	/**
	 * Returns the widget object with the given widget id or null if there is no such object.
	 *
	 * @since	1.0.0
	 * @api
	 *
	 * @param	integer	$widgetID
	 * @return	\ultimate\data\widget\Widget|null
	 */
	public function getWidget($widgetID) {
		if (isset($this->widgets[$widgetID])) {
			return $this->widgets[$widgetID];
		}
	
		return null;
	}
	
	/**
	 * Returns the child widgets of the given widget.
	 *
	 * @since	1.0.0
	 * @api
	 *
	 * @param	\ultimate\data\widget\Widget	$widget
	 * @return	\ultimate\data\widget\Widget[]
	 */
	public function getChildWidgets(\ultimate\data\widget\Widget $widget) {
		$widgets = array();
		
		foreach ($this->widgets as $__widget) {
			if ($__widget->__get('widgetParent') == $widget->__get('widgetName') /*&& $menuItem->__get('menuItemID') */ && $__widget->__get('widgetAreaID') == $widget->__get('widgetAreaID')) {
				$widgets[$__widget->__get('widgetID')] = $__widget;
			}
		}
		
		return $widgets;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.SingletonFactory.html#init
	 */
	protected function init() {
		$this->widgets = WidgetCacheBuilder::getInstance()->getData(array(), 'widgetsShowOrder');
	}
	
	/**
	 * Reloads the widget cache.
	 *
	 * @internal Calls the init method.
	 */
	public function reloadCache() {
		WidgetCacheBuilder::getInstance()->reset();
	
		$this->init();
	}
}
