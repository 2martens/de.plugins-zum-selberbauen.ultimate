<?php
/**
 * Contains the WidgetAreaCacheBuilder class.
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
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\widget\area\WidgetAreaList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the widget areas.
 * 
 * Provides two variables:
 * * \ultimate\data\widget\area\WidgetArea[] widgetAreas (widgetAreaID => widgetArea)
 * * integer[] widgetAreaIDs
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class WidgetAreaCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 * 
	 * @param	array	$parameters
	 * 
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'widgetAreas' => array(),
			'widgetAreaIDs' => array()
		);
		
		$widgetAreaList = new WidgetAreaList();
		$widgetAreaList->readObjects();
		$widgetAreas = $widgetAreaList->getObjects();
		$widgetAreaIDs = $widgetAreaList->getObjectIDs();
		
		if (empty($widgetAreas)) return $data;
		
		$data['widgetAreas'] = $widgetAreas;
		$data['widgetAreaIDs'] = $widgetAreaIDs;
		
		return $data;
	}
}
