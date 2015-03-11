<?php
/**
 * Contains the BlockTypeCacheBuilder class.
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
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\blocktype\BlockTypeList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the blockTypes.
 * 
 * Provides three variables:
 * * \ultimate\data\blocktype\BlockType[] blockTypes (blockTypeID => blockType)
 * * integer[] blockTypeIDs
 * * \ultimate\data\blocktype\BlockType[] blockTypesToName (blockTypeCssIdentifier => blockType)
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class BlockTypeCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 * 
	 * @param	array	$parameters
	 * 
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'blockTypes' => array(),
			'blockTypeIDs' => array(),
			'blockTypesToName' => array()
		);
		
		$blockTypeList = new BlockTypeList();
		
		$blockTypeList->readObjects();
		$blockTypes = $blockTypeList->getObjects();
		$blockTypeIDs = $blockTypeList->getObjectIDs();
		if (empty($blockTypes)) return $data;
		
		foreach ($blockTypes as $blockType) {
			$data['blockTypesToName'][$blockType->__get('cssIdentifier')] = $blockType;
		}
		
		$data['blockTypes'] = $blockTypes;
		$data['blockTypeIDs'] = $blockTypeIDs;
		
		return $data;
	}
}
