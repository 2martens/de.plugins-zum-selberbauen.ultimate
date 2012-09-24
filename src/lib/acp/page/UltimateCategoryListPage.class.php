<?php
namespace ultimate\acp\page;
use wcf\page\AbstractCachedListPage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows the UltimateCategoryList page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
class UltimateCategoryListPage extends AbstractCachedListPage {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$templateName
	 */
	public $templateName = 'ultimateCategoryList';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.MultipleLinkPage.html#$objectListClassName
	 */
	public $objectListClassName = '\ultimate\data\category\CategoryList';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.SortablePage.html#$validSortFields
	 */
	public $validSortFields = array(
		'categoryID',
		'categoryTitle',
		'categoryDescription',
		'categorySlug',
		'categoryContents'
	);
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.SortablePage.html#$defaultSortField
	*/
	public $defaultSortField = ULTIMATE_SORT_CATEGORY_SORTFIELD;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.SortablePage.html#$defaultSortOrder
	 */
	public $defaultSortOrder = ULTIMATE_SORT_CATEGORY_SORTORDER;
	
	/**
	 * @see \wcf\page\AbstractCachedListPage::$cacheBuilderClassName
	 */
	public $cacheBuilderClassName = '\ultimate\system\cache\builder\CategoryCacheBuilder';
	
	/**
	 * @see \wcf\page\AbstractCachedListPage::$cacheName
	 */
	public $cacheName = 'category';
	
	/**
	 * @see \wcf\page\AbstractCachedListPage::$cacheIndex
	 */
	public $cacheIndex = 'categories';
	
	/**
	 * Contains the active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.category.list';

	/**
	 * Contains the url.
	 * @var	string
	*/
	protected $url = '';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	*/
	public function readData() {
		parent::readData();
		$this->url = LinkHandler::getInstance()->getLink('UltimateCategoryList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.SortablePage.html#validateSortField
	 */
	public function validateSortField() {
		parent::validateSortField();
		
		if ($this->sortField == 'categoryContents') {
			$categories = $this->objects;
			$newCategories = array();
			// get array with content count
			foreach ($categories as $categoryID => $category) {
				$newCategories[count($category->__get('contents'))] = $category;
			}
			// actually sort the array
			if ($this->sortOrder == 'ASC') ksort($newCategories);
			else krsort($newCategories);
			// refill the original array with the sorted values
			foreach ($newCategories as $category) {
				$categories[$category->__get('categoryID')] = $category;
			}
			
			// save the sort field and order temporarily and restore them to default
			// prevents second sort attempt
			$this->tempSortField = $this->sortField;
			$this->tempSortOrder = $this->sortOrder;
			$this->sortField = $this->defaultSortField;
			$this->sortOrder = $this->defaultSortOrder;
			
			// return the sorted array
			$this->objects = $categories;
			$this->currentObjects = array_slice($this->objects, ($this->pageNo - 1) * $this->itemsPerPage, $this->itemsPerPage, true);
		}
	}
	
	/**
	 * @see \wcf\page\AbstractCachedListPage::loadCache
	 */
	public function loadCache($path = ULTIMATE_DIR) {
		parent::loadCache($path);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(),
			'url' => $this->url
		));
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#show
	 */
	public function show() {
		// set active menu item
		ACPMenu::getInstance()->setActiveMenuItem($this->activeMenuItem);
		
		parent::show();
	}
}
