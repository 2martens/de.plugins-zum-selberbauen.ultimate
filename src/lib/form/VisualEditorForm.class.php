<?php
namespace ultimate\form;
use wcf\form\AbstractForm;
use wcf\system\cache\CacheHandler;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\util\StringUtil;

/**
 * Shows the VisualEditor form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	form
 * @category	Ultimate CMS
 */
class VisualEditorForm extends AbstractForm {
	/**
	 * @var	string
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$templateName
	 */
	public $templateName = 'visualEditor';
	
	/**
	 * @var	string[]
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		//'user.ultimate.canUseVisualEditor'
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
	protected $contents = array();
	
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
	 * If true, only the IFrame content is shown.
	 * @var boolean
	 */
	protected $iFrameContent = false;
	
	/**
	 * Contains the mode of the VisualEditor.
	 * @var string
	 */
	protected $mode = 'grid';
	
	/**
	 * Contains the grid width.
	 * @var integer
	 */
	protected $gridWidth = 0;
	
	/**
	 * Contains the column width.
	 * @var integer
	 */
	protected $columnWidth = ULTIMATE_GRID_COLUMNWIDTH;
	
	/**
	 * Contains the gutter width.
	 * @var integer
	 */
	protected $gutterWidth = ULTIMATE_GRID_GUTTERWIDTH;
	
	/**
	 * Contains the wrapper top margin.
	 * @var integer
	 */
	protected $wrapperTopMargin = ULTIMATE_GRID_WRAPPERTOPMARGIN;
	
	/**
	 * Contains the wrapper bottom margin.
	 * @var integer
	 */
	protected $wrapperBottomMargin = ULTIMATE_GRID_WRAPPERBOTTOMMARGIN;
	
	/**
	 * @see http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readParameters
	 */
	public function readParameters() {
		parent::readParameters();
		if (isset($_GET['visualEditorIFrame'])) $this->iFrameContent = true;
		// only grid mode is supported
		//if (isset($_REQUEST['mode'])) $this->mode = StringUtil::trim($_REQUEST['mode']);
	}
	
	/**
	 * @see http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		if ($this->iFrameContent) {
			parent::readData();
			return;
		}
		
		// reading cache
		$cacheName = 'blocktype';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\BlockTypeCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->blockTypes = CacheHandler::getInstance()->get($cacheName, 'blockTypesToName');
		
		$cacheName = 'category';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\CategoryCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->categories = CacheHandler::getInstance()->get($cacheName, 'categories');
		
		$cacheName = 'content';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\ContentCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->contents = CacheHandler::getInstance()->get($cacheName, 'contents');
		
		$cacheName = 'page';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\PageCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->pages = CacheHandler::getInstance()->get($cacheName, 'pages');
		
		$cacheName = 'template';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\TemplateCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->templates = CacheHandler::getInstance()->get($cacheName, 'templates');
		
		// checking option inputs
		if ($this->columnWidth < 10) $this->columnWidth = 10;
		if ($this->columnWidth > 80) $this->columnWidth = 80;
		if ($this->gutterWidth < 0) $this->gutterWidth = 0;
		if ($this->gutterWidth > 30) $this->gutterWidth = 30;
		if ($this->wrapperTopMargin < 0) $this->wrapperTopMargin = 0;
		if ($this->wrapperTopMargin > 100) $this->wrapperTopMargin = 100;
		if ($this->wrapperBottomMargin < 0) $this->wrapperBottomMargin = 0;
		if ($this->wrapperBottomMargin > 100) $this->wrapperBottomMargin = 100;
		
		// calculate grid width
		$this->gridWidth = ($this->columnWidth * 24) + ($this->gutterWidth * 23);
		
		parent::readData();
	}
	
	/**
	 * @see http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		if ($this->iFrameContent) {
			WCF::getTPL()->assign(array(
				'gridColor' => ULTIMATE_GRID_COLOR
			));
		}
		else {
			$blockTypesJSON = array();
			foreach ($this->blockTypes as $blockTypeName => $blockType) {
				$blockTypesJSON[$blockTypeName] = array(
					'fixedHeight' => $blockType->__get('fixedHeight')
				);
			}
			WCF::getTPL()->assign(array(
				'blockTypes' => $this->blockTypes,
				'blockTypesJSON' => JSON::encode($blockTypesJSON),
				'categories' => $this->categories,
				'contents' => $this->contents,			
				'pages' => $this->pages,
				'templates' => $this->templates/*,
				isn't used anymore
				'gridWidth' => $this->gridWidth,
				'columnWidth' => $this->columnWidth,
				'gutterWidth' => $this->gutterWidth,
				'wrapperTopMargin' => $this->wrapperTopMargin,
				'wrapperBottomMargin' => $this->wrapperBottomMargin*/
			));
		}
	}
	
	/**
	 * @see http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#show
	 */
	public function show() {
		if ($this->iFrameContent) {
			$this->templateName = 'visualEditorIFrame'.ucfirst($this->mode);
		}
		parent::show();
	}
}
