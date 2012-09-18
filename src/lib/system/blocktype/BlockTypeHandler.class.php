<?php
namespace ultimate\system\blocktype;
use wcf\system\cache\CacheHandler;
use wcf\system\SingletonFactory;

/**
 * Handles the blockTypes.
 * 
 * Instead of creating your own IBlockType objects, you should use this class to get them.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
class BlockTypeHandler extends SingletonFactory {
	/**
	 * Contains the read objects.
	 * @var	\ultimate\data\blocktype\BlockType[]
	 */
	protected $objects = array();
	
	/**
	 * Contains the block type objects.
	 * @var \ultimate\system\blocktype\IBlockType[]
	 */
	protected $blockTypes = array();
	
	/**
	 * Returns the block type of the given id.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	integer	$blockTypeID
	 * @return	\ultimate\system\blocktype\IBlockType[]|null	null if there is no such object
	 */
	public function getBlockType($blockTypeID) {
		$blockTypeID = intval($blockTypeID);
		// if already initialized, return BlockType
		if (isset($this->blockTypes[$blockTypeID])) {
			return $this->blockTypes[$blockTypeID];
		}
		
		// otherwise create new object, save it and return it
		if (isset($this->objects[$blockTypeID])) {
			/* @var $blockType \ultimate\data\blocktype\BlockType */
			$blockType = $this->objects[$blockTypeID];
			$blockTypeClassName = $blockType->__get('blockTypeClassName');
			$this->blockTypes[$blockTypeID] = new $blockTypeClassName();
			return $this->blockTypes[$blockTypeID];
		}
		// the given block type id is not available by cache (one way or another)
		return null;
	}
	
	/**
	 * Returns the block type object of the given type.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$blockTypeName	block type in lowercase
	 * @return	\ultimate\system\blocktype\IBlockType[]|null	null if there is no such object
	 */
	public function getBlockTypeByName($blockTypeName) {
		foreach ($this->objects as $blockTypeID => $blockType) {
			$className = $blockType->__get('blockTypeClassName');
			$parts = explode('\\', $className);
			$parts = array_reverse($parts);
			$className = $parts[0];
			$blockType = strtolower(str_replace('BlockType', '', $className));
			if (strtolower($blockTypeName) == $blockType) {
				return $this->getBlockType($blockTypeID);
			}
			continue;
		}
		return null;
	}
	
	/**
	 * Returns the block type id of the block type with the given name.
	 * 
	 * @param	string	$blockTypeName
	 * @return	integer	0 if there is no such id
	 */
	public function getBlockTypeIDByName($blockTypeName) {
		foreach ($this->objects as $blockTypeID => $blockType) {
			$className = $blockType->__get('blockTypeClassName');
			$parts = explode('\\', $className);
			$parts = array_reverse($parts);
			$className = $parts[0];
			$blockType = strtolower(str_replace('BlockType', '', $className));
			if (strtolower($blockTypeName) == $blockType) {
				return intval($blockTypeID);
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
	 * Reads the block types from cache.
	 * 
	 * @since	1.0.0
	 */
	protected function loadCache() {
		$cacheName = 'blocktype';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\BlockTypeCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->objects = CacheHandler::getInstance()->get($cacheName, 'blockTypes');
	}
}