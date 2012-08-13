<?php
namespace ultimate\system\blocktype;
use ultimate\data\block\Block;
use wcf\system\cache\CacheHandler;
use wcf\system\event\EventHandler;
use wcf\util\StringUtil;

/**
 * Abstract class for all blockTypes.
 * 
 * Use this class for creating own BlockType classes. If you do that, you offer the chance for others to
 * modify and/or add functionality and you ensure that all methods of IBlockType are implemented.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimateCore
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
abstract class AbstractBlockType implements IBlockType {
	/**
	 * Contains the template name.
	 * @var	string
	 */
	public $templateName = '';
	
	/**
	 * Contains the read rows of custom query.
	 * @var	array[]
	 */
	public $queryResult = array();
	
	/**
	 * Contains the read objects (no custom query specified).
	 * @var	object[]
	 */
	public $objects = array();
	
	/**
	 * Contains the request type.
	 * The request type is one of the following values: page, content, category.
	 * @var	string
	 */
	public $requestType = '';
	
	/**
	 * Contains the block id.
	 * @var	integer
	 */
	public $blockID = 0;
	
	/**
	 * Contains a Block object.
	 * @var	\wcf\data\ultimate\block\Block
	 */
	public $block = null;
	
	/**
	 * Contains the cache name.
	 * @var	string
	 */
	protected $cacheName = '';
	
	/**
	 * Contains the CacheBuilder class name.
	 * @var	string
	 */
	protected $cacheBuilderClassName = '';
	
	/**
	 * Contains the cache index.
	 * @var	string
	 */
	protected $cacheIndex = '';
	
	/**
	 * Creates a new BlockType object.
	 * 
	 * @internal The constructor does nothing and is final, because you can't control what the constructor
	 * should do. A subclass could easily overwrite this one and do some other stuff. 
	 */
	public final function __construct() {}
	
	/**
	 * @internal Calls the methods readData and assignVariables.
	 * @see	\wcf\system\ultimate\blockType\IBlockType::run()
	 */
	public function run($requestType, $blockID) {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'run');
		
		$this->requestType = StringUtil::trim($requestType);
		$this->blockID = intval($blockID);
		$this->block = new Block($this->blockID);
		
		$this->readData();
		$this->assignVariables();
	}
	
	/**
	 * @internal Calls the method loadCache().
	 * @see	\wcf\system\ultimate\blockType\IBlockType::readData()
	 */
	public function readData() {
	   // fire event
	   EventHandler::getInstance()->fireAction($this, 'readData');
	   $this->loadCache();
	   
	}
	
	/**
	 * @see	\wcf\system\ultimate\blockType\IBlockType::assignVariables()
	 */
	public function assignVariables() {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'assignVariables');
	}
	
	/**
	 * @internal You have to override this method.
	 * @see	\wcf\system\ultimate\blockType\IBlockType::getHTML()
	 */
	public function getHTML() {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'getHTML');
		return ''; // you have to override this method
	}
	
	/**
	 * Loads the cache.
	 * 
	 * Use this method instead of defining an own one. Each BlockType should only need one kind of objects.
	 * If a custom query for the block type exists, use the results from it instead of reading the general cache.
	 * 
	 * @since 1.0.0
	 */
	protected function loadCache() {
		if (!empty($this->block->query)) {
			$cacheName = 'ultimate-block-'.PACKAGE_ID;
			$cacheBuilderClassName = '\wcf\system\cache\builder\UltimateBlockCacheBuilder';
			$file = WCF_DIR.'cache/cache'.$cacheName.'.php';
			CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
			$result = CacheHandler::getInstance()->get($cacheName, 'cachedQueryToBlockID');
			$this->queryResult = $result[$this->blockID];
		} else {
			$file = WCF_DIR.'cache/cache.'.$this->cacheName.'.php';
			CacheHandler::getInstance()->addResource($this->cacheName, $file, $this->cacheBuilderClassName);
			$this->objects = CacheHandler::getInstance()->get($this->cacheName, $this->cacheIndex);
		}
	}
}
