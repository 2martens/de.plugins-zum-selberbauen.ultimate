<?php
namespace ultimate\form;
use wcf\form\AbstractForm;
use wcf\system\cache\CacheHandler;
use wcf\system\WCF;

/**
 * Shows the VisualEditor form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	form
 * @category	Ultimate CMS
 */
class VisualEditorForm extends AbstractForm {
	/**
	 * @var	string
	 * @see	\wcf\page\AbstractPage::$templateName
	 */
	public $templateName = 'visualEditor';
	
	/**
	 * @var	string[]
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array(
		'user.ultimate.canUseVisualEditor'
	);
	
	
	
	/**
	 * Contains all available block types.
	 * @var \ultimate\data\blocktype\BlockType[]
	 */
	protected $blockTypes = array();
	
	/**
	 * Contains all available categories.
	 * @var \ultimate\data\category\Category[]
	 */
	protected $categories = array();
	
	/**
	 * Contains all available contents.
	 * @var \ultimate\data\content\TaggableContent[]
	 */
	protected $categories = array();
	
	/**
	 * Contains all available pages.
	 * @var \ultimate\data\page\Page[]
	 */
	protected $pages = array();
	
	/**
	 * Contains all available templates.
	 * @var \ultimate\data\template\Template[]
	 */
	protected $templates = array();
	
	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		// reading cache
		$cacheName = 'blocktype';
		$cacheBuilderClassName = 'BlockTypeCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->blockTypes = CacheHandler::getInstance()->get($cacheName, 'blockTypes');
		
		$cacheName = 'category';
		$cacheBuilderClassName = 'CategoryCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->categories = CacheHandler::getInstance()->get($cacheName, 'categories');
		
		$cacheName = 'content';
		$cacheBuilderClassName = 'ContentCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->contents = CacheHandler::getInstance()->get($cacheName, 'contents');
		
		$cacheName = 'page';
		$cacheBuilderClassName = 'PageCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->pages = CacheHandler::getInstance()->get($cacheName, 'pages');
		
		$cacheName = 'template';
		$cacheBuilderClassName = 'TemplateCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->templates = CacheHandler::getInstance()->get($cacheName, 'templates');
		
		parent::readData();
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'blockTypes' => $this->blockTypes,
			'categories' => $this->categories,
			'contents' => $this->contents,			
			'pages' => $this->pages,
			'templates' => $this->templates
		));
	}
}
