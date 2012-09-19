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
 * @subpackage	system.widgettype
 * @category	Ultimate CMS
 */
namespace ultimate\system\widgettype;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles the widget types.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.widgettype
 * @category	Ultimate CMS
 */
class WidgetTypeHandler extends SingletonFactory {
	/**
	 * Contains the read objects.
	 * @var	\ultimate\data\widgettype\WidgetType[]
	 */
	protected $objects = array();
	
	/**
	 * Contains the widget type objects.
	 * @var \ultimate\system\widget\IWidgetType[]
	 */
	protected $widgetTypes = array();
	
	/**
	 * Returns the widget type of the given id.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	integer	$widgetTypeID
	 * @return	\ultimate\system\widget\IWidget[]|null	null if there is no such object
	 */
	public function getWidgetType($widgetTypeID) {
		$widgetTypeID = intval($widgetTypeID);
		// if already initialized, return WidgetType
		if (isset($this->widgetTypes[$widgetTypeID])) {
			return $this->widgetTypes[$widgetTypeID];
		}
	
		// otherwise create new object, save it and return it
		if (isset($this->objects[$widgetTypeID])) {
			/* @var $widgetType \ultimate\data\widgettype\WidgetType */
			$widgetType = $this->objects[$widgetTypeID];
			$widgetTypeClassName = $widgetType->__get('widgetTypeClassName');
			$this->widgetTypes[$widgetTypeID] = new $widgetTypeClassName();
			return $this->widgetTypes[$widgetTypeID];
		}
		// the given widget id is not available by cache (one way or another)
		return null;
	}
	
	/**
	 * Returns the widget type object with the given type.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$widgetTypeName	widget type in lowercase
	 * @return	\ultimate\system\widget\IWidget[]|null	null if there is no such object
	 */
	public function getWidgetTypeByName($widgetTypeName) {
		foreach ($this->objects as $widgetTypeID => $widgetTypeType) {
			$className = $widgetType->__get('widgetTypeClassName');
			$parts = explode('\\', $className);
			$parts = array_reverse($parts);
			$className = $parts[0];
			$widgetType = strtolower(str_replace('WidgetType', '', $className));
			if (strtolower($widgetTypeName) == $widgetType) {
				return $this->getWidgetType($widgetTypeID);
			}
			continue;
		}
		return null;
	}
	
	/**
	 * Returns the widget type id of the widget type with the given name.
	 * 
	 * @param	string	$widgetTypeName
	 * @return	integer	0 if there is no such id
	 */
	public function getWidgetTypeIDByName($widgetTypeName) {
		foreach ($this->objects as $widgetTypeID => $widgetType) {
			$className = $widgetType->__get('widgetTypeClassName');
			$parts = explode('\\', $className);
			$parts = array_reverse($parts);
			$className = $parts[0];
			$widgetType = strtolower(str_replace('WidgetType', '', $className));
			if (strtolower($widgetTypeName) == $widgetType) {
				return intval($widgetTypeID);
				break;
			}
			continue;
		}
		return 0;
	}
	
	/**
	 * Loads the cache.
	 * 
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.SingletonFactory.html#init
	 */
	protected function init() {
		$this->loadCache();
	}
	
	/**
	 * Reads the widgets from cache.
	 * 
	 * @since	1.0.0
	 */
	protected function loadCache() {
		$cacheName = 'widget-type';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\WidgetTypeCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->objects = CacheHandler::getInstance()->get($cacheName, 'widgetTypes');
	}
}
