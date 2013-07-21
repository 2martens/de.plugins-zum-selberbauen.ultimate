<?php
/**
 * Contains the LayoutCacheBuilder class.
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
use ultimate\data\layout\LayoutList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the layouts.
 * 
 * Provides four variables:
 * * \ultimate\data\layout\Layout[] layouts (layoutID => layout)
 * * integer[] layoutIDs
 * * \ultimate\data\layout\Layout[] layoutsToObjectData ('objectID,objectType' => layout)
 * * \ultimate\data\template\Template[] templatesToLayoutID (layoutID => template)
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.layout
 * @category	Ultimate CMS
 */
class LayoutCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.AbstractCacheBuilder.html#rebuild
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'layouts' => array(),
			'layoutIDs' => array(),
			'layoutsToObjectData' => array(),
			'templatesToLayoutID' => array()
		);
		
		$layoutList = new LayoutList();
		$layoutList->readObjects();
		$layouts = $layoutList->getObjects();
		$layoutIDs = $layoutList->getObjectIDs();
		if (empty($layouts)) return $data;
		
		foreach ($layouts as $layoutID => $layout) {
			/* @var $layout \ultimate\data\layout\Layout */
			$data['layoutsToObjectData'][$layout->__get('objectID') .','. $layout->__get('objectType')] = $layout;
			$data['templatesToLayoutID'][$layoutID] = $layout->__get('template');
		}
		
		$data['layouts'] = $layouts;
		$data['layoutIDs'] = $layoutIDs;
		
		return $data;
	}
}
