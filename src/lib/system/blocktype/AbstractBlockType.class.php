<?php
namespace ultimate\system\blocktype;
use ultimate\data\block\Block;
use wcf\system\cache\CacheHandler;
use wcf\system\event\EventHandler;
use wcf\system\WCF;
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
	protected $templateName = '';
	
	/**
	 * True if the template shall be used.
	 * @var boolean
	 */
	protected $useTemplate = true;
	
	/**
	 * Contains the read rows of custom query.
	 * @var	array[]
	 */
	protected $queryResult = array();
	
	/**
	 * Contains the read objects (no custom query specified).
	 * @var	object[]
	 */
	protected $objects = array();
	
	/**
	 * Contains the request type.
	 * The request type is one of the following values: page, content, category.
	 * @var	string
	 */
	protected $requestType = '';
	
	/**
	 * Contains the request object.
	 * @var object
	 */
	protected $requestObject = null;
	
	/**
	 * Contains the block id.
	 * @var	integer
	 */
	protected $blockID = 0;
	
	/**
	 * Contains a Block object.
	 * @var	\ultimate\data\block\Block
	 */
	protected $block = null;
	
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
	 * @see	\ultimate\system\blocktype\IBlockType::run()
	 */
	public function run($requestType, \ultimate\data\AbstractUltimateDatabaseObject $requestObject, $blockID) {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'run');
		
		$this->requestType = StringUtil::trim($requestType);
		$this->requestObject = $requestObject;
		$this->blockID = intval($blockID);
		$this->block = new Block($this->blockID);
		
		$this->readData();
		$this->assignVariables();
	}
	
	/**
	 * @internal Calls the method loadCache().
	 * @see	\ultimate\system\blocktype\IBlockType::readData()
	 */
	public function readData() {
	   // fire event
	   EventHandler::getInstance()->fireAction($this, 'readData');
	   $this->loadCache();
	   
	}
	
	/**
	 * @see	\ultimate\system\blocktype\IBlockType::assignVariables()
	 */
	public function assignVariables() {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'assignVariables');
		WCF::getTPL()->assign(array(
			'blockID' => $this->blockID,
			'block' => $this->block,
			'requestType' => $this->requestType
		));
	}
	
	/**
	 * @internal If you want to do more than fetching a template, you have to override this method.
	 * Returns the fetched template if $this->useTemplate or a string {include file='$this->templateName'}.
	 * @see	\ultimate\system\blocktype\IBlockType::getHTML()
	 */
	public function getHTML() {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'getHTML');
		// guess template name
		if (empty($this->templateName)) {
			$classParts = explode('\\', get_class($this));
			$className = array_pop($classParts);
			$this->templateName = lcfirst($className);
		}
		$output = '';
		// only fetch template if the template should be used
		if ($this->useTemplate) $output = WCF::getTPL()->fetch($this->templateName);
		// otherwise include template
		else {
			$output = '{include file=\''.$this->templateName.'\'}';
		}
		return $output;
	}
	
	/**
	 * Returns variables.
	 * 
	 * @param	string	$name
	 * @return	mixed|null	null if no fitting variable was found
	 */
	public function __get($name) {
		if (isset($this->{$name})) {
			return $this->{$name};
		}
		
		return null;
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
			$cacheName = 'block';
			$cacheBuilderClassName = '\ultimate\system\cache\builder\BlockCacheBuilder';
			$file = ULTIMATE_DIR.'cache/cache'.$cacheName.'.php';
			CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
			$result = CacheHandler::getInstance()->get($cacheName, 'cachedQueryToBlockID');
			$this->queryResult = $result[$this->blockID];
		} else {
			// prevents error
			if (empty($this->cacheName)) return;
			$file = ULTIMATE_DIR.'cache/cache.'.$this->cacheName.'.php';
			CacheHandler::getInstance()->addResource($this->cacheName, $file, $this->cacheBuilderClassName);
			$this->objects = CacheHandler::getInstance()->get($this->cacheName, $this->cacheIndex);
		}
	}
}
