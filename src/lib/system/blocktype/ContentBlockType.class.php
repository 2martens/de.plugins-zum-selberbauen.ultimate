<?php
namespace ultimate\system\blocktype;
use wcf\system\WCF;

/**
 * Represents the content block type.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
class ContentBlockType extends AbstractBlockType {
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$templateName
	 */
	protected $templateName = 'contentBlockType';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$useTemplate
	 */
	protected $useTemplate = false;
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheName
	 */
	protected $cacheName = 'content';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheBuilderClassName
	 */
	protected $cacheBuilderClassName = '\ultimate\system\cache\builder\ContentCacheBuilder';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheIndex
	 */
	protected $cacheIndex = 'contents';
	
	/**
	 * Contains all contents for the current request.
	 * @var \ultimate\data\content\TaggedContent[]
	 */
	protected $contents = array();
	
	/**
	 * @see \ultimate\system\blocktype\IBlockType::readData()
	 */
	public function readData() {
		if ($this->requestType != 'content') {
			$this->cacheName = $this->requestType;
			$this->cacheBuilderClassName = '\ultimate\system\cache\builder\Content'.
				ucfirst($this->requestType).
				'CacheBuilder';
			$this->cacheIndex = 'contentsTo'.ucfirst($this->requestType).'ID';
		}
		parent::readData();
		
		// gathering contents which shall be displayed
		switch ($this->requestType) {
			case 'category':
				$this->contents = $this->objects[$this->requestObject->__get('categoryID')];
				break;
			case 'content':
				$this->contents[$this->requestObject->__get('contentID')] = $this->requestObject;
				break;
			case 'page':
				$content = $this->objects[$this->requestObj->__get('pageID')];
				$this->contents[$content->__get('contentID')] = $content;
				break;
		}
	}
	
	/**
	 * @see \ultimate\system\blocktype\IBlockType::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'contents' => $this->contents
		));
	}
	
	/**
	 * @see \ultimate\system\blocktype\IBlockType::getHTML()
	 */
	public function getHTML() {
		// TODO implement custom query
	}
}
