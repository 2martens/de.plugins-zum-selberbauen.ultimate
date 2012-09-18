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
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
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
	 * Contains the block options template name.
	 * @var string
	 */
	protected $blockOptionsTemplateName = '';
	
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
	 * Contains the layout object.
	 * @var \ultimate\data\layout\Layout
	 */
	protected $layout = null;
	
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
	 * True if the request is in connection with the VisualEditor.
	 * @var boolean
	 */
	protected $visualEditorMode = false;
	
	/**
	 * Creates a new BlockType object.
	 * 
	 * @internal The constructor does nothing and is final, because you can't control what the constructor
	 * should do. A subclass could easily overwrite this one and do some other stuff. 
	 */
	public final function __construct() {}
	
	/**
	 * @see	\ultimate\system\blocktype\IBlockType::init()
	 */
	public function init($requestType, \ultimate\data\layout\Layout $layout, $blockID, $visualEditorMode = false) {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'init');
		
		$this->requestType = StringUtil::trim($requestType);
		$this->layout = $layout;
		$this->blockID = intval($blockID);
		$this->visualEditorMode = $visualEditorMode;
		$this->block = new Block($this->blockID);
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
			'requestType' => $this->requestType,
			'visualEditorMode' => $this->visualEditorMode
		));
	}
	
	/**
	 * @internal If you want to do more than fetching a template, you have to override this method.<br />
	 * Returns the fetched template if $this->useTemplate is true and otherwise a string {include file='$this->templateName'}.<br />
	 * Calls readData and assignVariables.
	 * @see	\ultimate\system\blocktype\IBlockType::getHTML()
	 */
	public function getHTML() {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'getHTML');
		$this->readData();
		$this->assignVariables();
		
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
			$output = "{include file='".$this->templateName."'}";
		}
		return $output;
	}
	
	/**
	 * @internal If you want to do more than fetching a template, you have to override this method.<br />
	 * Calls readData and assignVariables.
	 * @see \ultimate\system\blocktype\IBlockType::getOptionsHTML()
	 */
	public function getOptionsHTML() {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'getOptionsHTML');
		$this->readData();
		$this->assignVariables();
		
		// guess template name
		if (empty($this->blockOptionsTemplateName)) {
			$classParts = explode('\\', get_class($this));
			$className = array_pop($classParts);
			$this->blockOptionsTemplateName = str_replace('Type', 'Options', lcfirst($className));
		}
		$output = '';
		$output = WCF::getTPL()->fetch($this->blockOptionsTemplateName);
		return $output;
	}
	
	/**
	 * Returns variables.
	 * 
	 * @since	1.0.0
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
	 * If the optional parameter loadCustomQuery is given and setted with true the saved custom query will be loaded.
	 * If no custom query is saved, then the cache defined by the object variables is loaded.
	 * 
	 * @since	1.0.0
	 * 
	 * @param	boolean	$loadCustomQuery	optional
	 */
	protected function loadCache($loadCustomQuery = false) {
		if ($loadCustomQuery && !empty($this->block->__get('query'))) {
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