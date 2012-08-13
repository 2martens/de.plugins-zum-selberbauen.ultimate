<?php
namespace ultimate\system\blocktype;
use ultimate\data\template\Template;
use wcf\system\cache\CacheHandler;
use wcf\system\SingletonFactory;

/**
 * Handles the blockTypes.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimateCore
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
class BlockTypeHandler extends SingletonFactory {
	/**
	 * Contains the request type.
	 * @var	string
	 */
	protected $requestType = '';
	
	/**
	 * The template id of the current request.
	 * @var	integer
	 */
	protected $templateID = 0;
	
	/**
	 * Contains the read objects.
	 * @var	\wcf\data\ultimate\template\Template[]
	 */
	protected $objects = array();
	
	/**
	 * Returns the request type.
	 * 
	 * @return	string
	 */
	public function getRequestType() {
		return $this->requestType;
	}
	
	/**
	 * Handles the request.
	 * 
	 * @since	1.0.0
	 * @internal Calls the method getHTML on each relevant BlockType.
	 * 
	 * @param	string	$requestType
	 * @param	integer	$templateID
	 * @return	string[]
	 */
	public function handleRequest($requestType, $templateID) {
		$this->requestType = StringUtil::trim($requestType);
		$this->templateID = intval($templateID);
		
		$this->loadCache();
		/* @var $template \wcf\data\ultimate\template\Template */
		$template = $this->objects[$this->templateID];
		
		$resultArray = array();
		foreach ($template->__get('blocks') as $blockID => $block) {
			/* @var $block \wcf\data\ultimate\block\Block */
			/* @var $blockType \wcf\data\ultimate\blocktype\BlockType */
			
			$blockType = $block->__get('blockType');
			$className = $blockType->__get('blockTypeClassName');
			
			/* @var $blockTypeController \wcf\system\ultimate\blocktype\IBlockType */
			$blockTypeController = new $className();
			$blockTypeController->run($this->requestType, $blockID);
			$resultArray[$blockID] = $blockTypeController->getHTML();
		}
		return $resultArray;
	}
	
	/**
	 * Reads the templates from cache.
	 */
	protected function loadCache() {
		$cacheName = 'ultimate-template-'.PACKAGE_ID;
		$cacheBuilderClassName = '\wcf\system\cache\builder\UltimateTemplateCacheBuilder';
		$file = WCF_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->objects = CacheHandler::getInstance()->get($cacheName, 'templates');
	}
}