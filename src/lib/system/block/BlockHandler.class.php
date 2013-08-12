<?php
/**
 * Contains the BlockHandler class.
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
 * @subpackage	system.block
 * @category	Ultimate CMS
 */
namespace ultimate\system\block;
use ultimate\data\block\Block;
use ultimate\system\cache\builder\BlockCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Handles blocks.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.block
 * @category	Ultimate CMS
 */
class BlockHandler extends SingletonFactory {
	/**
	 * The cached blocks.
	 * @var	\ultimate\data\block\Block[]
	 */
	protected $blocks = array();
	
	/**
	 * Returns all block objects.
	 *
	 * @since	1.0.0
	 * @api
	 *
	 * @return	\ultimate\data\block\Block[]
	*/
	public function getBlocks() {
		return $this->blocks;
	}
	
	/**
	 * Returns the block object with the given block id or null if there is no such object.
	 *
	 * @since	1.0.0
	 * @api
	 *
	 * @param	integer	$blockID
	 * @return	\ultimate\data\block\Block|null
	 */
	public function getBlock($blockID) {
		if (isset($this->blocks[$blockID])) {
			return $this->blocks[$blockID];
		}
	
		return null;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.SingletonFactory.html#init
	 */
	protected function init() {
		$this->blocks = BlockCacheBuilder::getInstance()->getData(array(), 'blocks');
	}
	
	/**
	 * Reloads the block cache.
	 *
	 * @internal Calls the init method.
	 */
	public function reloadCache() {
		BlockCacheBuilder::getInstance()->reset();
	
		$this->init();
	}
}
