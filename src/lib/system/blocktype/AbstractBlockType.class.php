<?php
/**
 * Contains the AbstractBlockType class.
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
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
namespace ultimate\system\blocktype;
use ultimate\data\block\Block;
use ultimate\system\cache\builder\BlockCacheBuilder;
use wcf\system\event\EventHandler;
use wcf\system\exception\SystemException;
use wcf\system\request\RequestHandler;
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
	 * Contains the block option form element ids.
	 * 
	 * @var	string[]
	 */
	protected $blockOptionIDs = array();
	
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
	 * @var \ultimate\data\AbstractUltimateDatabaseObject
	 */
	protected $requestObject = null;
	
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
	 * @see	\ultimate\system\blocktype\IBlockType::init()
	 */
	public function init($requestType, \ultimate\data\layout\Layout $layout, $requestObject, $blockID) {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'init');
		
		$this->requestType = StringUtil::trim($requestType);
		$this->requestObject = $requestObject;
		$this->layout = $layout;
		
		if (!($this->requestObject instanceof \ultimate\data\AbstractUltimateDatabaseObject) && $this->layout->__get('objectType') != 'index') {
			throw new SystemException('The given request object is not an instance of \ultimate\data\AbstractUltimateDatabaseObject.');
		}
		
		$this->blockID = intval($blockID);
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
			'requestType' => $this->requestType
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
		if ($this->useTemplate) $output = WCF::getTPL()->fetch($this->templateName, 'ultimate');
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
		// create blank Block if there is no block given (for example in the ACP)
		if ($this->block === null) {
			$this->block = new Block(null, array(
				'blockID' => 0,
				'blockTypeID' => 0,
				'query' => '',
				'parameters' => '',
				'additionalData' => ''
			));
		}
		
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
		
		if (RequestHandler::getInstance()->isACPRequest()) {
			WCF::getTPL()->addApplication('ultimate', ULTIMATE_DIR.'templates/');
			$output = WCF::getTPL()->fetch($this->blockOptionsTemplateName, 'ultimate');
			WCF::getTPL()->addApplication('ultimate', ULTIMATE_DIR.'acp/templates');
		}
		
		$blockOptionIDs = $this->blockOptionIDs;
		
		foreach ($blockOptionIDs as &$optionID) {
			$optionID = StringUtil::replace('{$blockID}', $this->blockID, $optionID);
		}
		return array(
			$blockOptionIDs, 
			$output
		);
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
		$query = $this->block->__get('query');
		if ($loadCustomQuery && !empty($query)) {
			$result = BlockCacheBuilder::getInstance()->getData(array(), 'cachedQueryToBlockID');
			$this->queryResult = $result[$this->blockID];
		} else {
			// prevents error
			if (empty($this->cacheBuilderClassName)) return;
			/* @var $instance \wcf\system\cache\builder\ICacheBuilder */
			$instance = call_user_func($this->cacheBuilderClassName.'::getInstance');
			$this->objects = $instance->getData(array(), $this->cacheIndex);
		}
	}
}
