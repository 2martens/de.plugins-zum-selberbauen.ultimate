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
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
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
	 * 
	 * @param	integer	$blockTypeID
	 * @return \ultimate\system\blocktype\IBlockType[]|null	null if there is no such object
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
	 * Loads the cache.
	 * 
	 * @see \wcf\system\SingletonFactory::init()
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