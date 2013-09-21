<?php
/**
 * Contains the BlockCacheBuilder class.
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\block\BlockList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches the blocks.
 * 
 * Provides three variables:
 * * \ultimate\data\block\Block[] blocks (blockID => block)
 * * integer[] blockIDs
 * * string[][][] cachedQueryToBlockID (blockID => cachedQuery ( => fetchedRowArray ( columnName => columnRowContent)))
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class BlockCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 * 
	 * @param	array	$parameters
	 * 
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'blocks' => array(),
			'blockIDs' => array(),
			'cachedQueryToBlockID' => array()
		);
		
		$blockList = new BlockList();
		$blockList->readObjects();
		$blocks = $blockList->getObjects();
		$blockIDs = $blockList->getObjectIDs();
		if (empty($blocks)) return $data;
		
		$data['blocks'] = $blocks;
		$data['blockIDs'] = $blockIDs;
		
		foreach ($data['blocks'] as $blockID => $block) {
			/* @var $block \wcf\data\ultimate\block\Block */
			
			// cache custom queries
			$sql = $block->__get('query');
			if (empty($sql)) continue;
			$parameters = $block->__get('parameters');
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute($parameters);
			
			$cachedQuery = array();
			while ($row = $statement->fetchArray()) {
				$cachedQuery[] = $row;
			}
			$data['cachedQueryToBlockID'][$blockID] = $cachedQuery;
		}
		return $data;
	}
}
