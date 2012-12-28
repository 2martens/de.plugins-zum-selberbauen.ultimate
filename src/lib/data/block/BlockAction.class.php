<?php
/**
 * Contains the block data model action class.
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
 * @subpackage	data.block
 * @category	Ultimate CMS
 */
namespace ultimate\data\block;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\cache\CacheHandler;

/**
 * Executes block-related actions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.ultimate.block
 * @category	Ultimate CMS
 */
class BlockAction extends AbstractDatabaseObjectAction {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$className
	 */
	public $className = '\ultimate\data\block\BlockEditor';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddBlock');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsDelete
	*/
	protected $permissionsDelete = array('admin.content.ultimate.canDeleteBlock');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsUpdate
	*/
	protected $permissionsUpdate = array('admin.content.ultimate.canEditBlock');
	
	/**
	 * Returns an available block id.
	 */
	public function getAvailableBlockID() {
		// reading cache
		$cacheName = 'block';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\BlockCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$blockIDs = CacheHandler::getInstance()->get($cacheName, 'blockIDs');
		// determine next available id
		$blackList = (isset($this->parameters['data']['blockIDBlackList']) ? $this->parameters['data']['blockIDBlackList'] : array());
		$realBlackList = array_merge($blockIDs, $blackList);
		if (!empty($realBlackList)) $lastID = max($realBlackList);
		else $lastID = 0;
		
		$nextAvailableID = $lastID++;
		return $nextAvailableID;
	}
	
	/**
	 * Does nothing as the getAvailableBlockID method does not require any permission.
	 */
	public function validateGetAvailableBlockID() {
		// no permissions required
	}
	
	/**
	 * Creates a block and respects additional AJAX requirements.
	 * 
	 * @return	(integer|string)[]
	 */
	public function createAJAX() {
		// serializes additionalData and query parameters
		$parameters = $this->parameters['data'];
		if (isset($parameters['additionalData'])) {
			$parameters['additionalData'] = serialize($parameters['additionalData']);
		}
		if (isset($parameters['parameters'])) {
			$parameters['parameters'] = serialize($parameters['parameters']);
		}
		$this->parameters['data'] = $parameters;
		
		// create the block
		/* @var $block \ultimate\data\block\Block */
		$block = $this->create();
		
		// get blocktype name
		$cache = 'blocktype';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\BlocktypeCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$blocktypes = CacheHandler::getInstance()->get($cacheName, 'blockTypes');
		
		$blocktype = $blocktypes[$block->__get('blockTypeID')];
		
		return array(
			'blockID' => $block->__get('blockID'),
			'blockTypeID' => $block->__get('blockTypeID'),
			'blockTypeName' => $blocktype->__get('blockTypeName')
		);
	}
	
	/**
	 * Validates the createAJAX method.
	 */
	public function validateCreateAJAX() {
		$this->validateCreate();
	}
}
