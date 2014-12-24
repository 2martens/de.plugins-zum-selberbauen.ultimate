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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.widget
 * @category	Ultimate CMS
 */
namespace ultimate\system\widget;
use ultimate\data\widget\area\WidgetArea;
use ultimate\system\cache\builder\WidgetAreaBoxCacheBuilder;
use wcf\page\IPage;
use wcf\system\exception\SystemException;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\util\ClassUtil;

/**
 * Handles widget boxes.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.widget
 * @category	Ultimate CMS
 */
class WidgetHandler extends SingletonFactory {
	/**
	 * Initializes the WidgetHandler.
	 * 
	 * @internal
	 */
	protected function init() {
		$this->boxCache = WidgetAreaBoxCacheBuilder::getInstance()->getData(array(), 'boxes');
		$this->pageCache = WidgetAreaBoxCacheBuilder::getInstance()->getData(array(), 'pages');
	}
	
	/**
	 * Returns active dashboard boxes for given widget area.
	 *
	 * @param	\ultimate\data\widget\area\WidgetArea	$widgetArea
	 * @param	\wcf\page\IPage	$page
	 * @throws  \wcf\system\exception\SystemException
	 */
	public function loadBoxes(WidgetArea $widgetArea, IPage $page) {
		$boxIDs = array();
		if (isset($this->pageCache[$widgetArea->__get('widgetAreaID')]) && is_array($this->pageCache[$widgetArea->__get('widgetAreaID')])) {
			foreach ($this->pageCache[$widgetArea->__get('widgetAreaID')] as $boxID) {
				$boxIDs[] = $boxID;
			}
		}
	
		$contentTemplate = $sidebarTemplate = '';
		foreach ($boxIDs as $boxID) {
			$className = $this->boxCache[$boxID]->className;
			if (!ClassUtil::isInstanceOf($className, 'wcf\system\dashboard\box\IDashboardBox')) {
				throw new SystemException("'".$className."' does not implement 'wcf\\system\\dashboard\\box\\IDashboardbox'");
			}
				
			$boxObject = new $className();
			$boxObject->init($this->boxCache[$boxID], $page);
				
			if ($this->boxCache[$boxID]->boxType == 'content') {
				$contentTemplate .= $boxObject->getTemplate();
			}
			else {
				$sidebarTemplate .= $boxObject->getTemplate();
			}
		}
	
		WCF::getTPL()->assign(array(
			'__boxContent' => $contentTemplate,
			'__boxSidebar' => $sidebarTemplate
		));
	}
	
	
	/**
	 * Clears widget area box cache.
	 * 
	 * @api
	 * @since	1.0.0
	 */
	public static function clearCache() {
		WidgetAreaBoxCacheBuilder::getInstance()->reset();
	}
}
