<?php
/**
 * Contains the WidgetAreaBoxCacheBuilder class.
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
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use wcf\data\dashboard\box\DashboardBoxList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caches the widget area dashboard box options.
 * 
 * Provides two variables:
 * * \wcf\data\dashboard\box\DashboardBox[] boxes (boxID => box)
 * * integer[][] pages (widgetAreaID => boxArray ( => boxID))
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class WidgetAreaBoxCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 * 
	 * @param	array	$parameters
	 * 
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'boxes' => array(),
			'pages' => array()
		);
		
		// load boxes
		$boxList = new DashboardBoxList();
		$boxList->readObjects();
		
		foreach ($boxList as $box) {
			$data['boxes'][$box->boxID] = $box;
		}
		
		// load settings
		$widgetAreaIDs = WidgetAreaCacheBuilder::getInstance()->getData(array(), 'widgetAreaIDs');
		
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add("widgetAreaID IN (?)", array($widgetAreaIDs));
		
		$sql = "SELECT  *
		        FROM    ultimate".WCF_N."_widget_area_option
		        ".$conditions."
		        ORDER BY showOrder ASC";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());
		while ($row = $statement->fetchArray()) {
			if (!isset($data['pages'][$row['widgetAreaID']])) {
				$data['pages'][$row['widgetAreaID']] = array();
			}
				
			$data['pages'][$row['widgetAreaID']][] = $row['boxID'];
		}
	
		return $data;
	}
}
