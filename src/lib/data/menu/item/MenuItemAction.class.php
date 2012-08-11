<?php
namespace ultimate\data\menu\item;
use ultimate\data\category\Category;
use ultimate\data\content\Content;
use ultimate\data\page\Page;
use ultimate\system\menu\item\MenuItemHandler;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\SystemException;
use wcf\system\exception\ValidateActionException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Executes menu item-related functions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.menu.item
 * @category	Ultimate CMS
 */
class MenuItemAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	public $className = '\ultimate\data\menu\item\MenuItemEditor';
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddMenuItem');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	*/
	protected $permissionsDelete = array('admin.content.ultimate.canDeleteMenuItem');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	*/
	protected $permissionsUpdate = array('admin.content.ultimate.canEditMenuItem');
	
	/**
	 * Contains all categories.
	 * @var	\ultimate\data\category\Category[]
	 */
	protected $categories = array();
	
	/**
	 * Contains all contents.
	 * @var	\ultimate\data\content\Content[]
	 */
	protected $contents = array();
	
	/**
	 * Contains all pages.
	 * @var	\ultimate\data\page\Page[]
	 */
	protected $pages = array();
	
	/**
	 * Creates a new MenuItemAction object.
	 * 
	 * @param	array	$objects
	 * @param	string	$action
	 * @param	array	$parameters
	 */
	public function __construct(array $objects, $action, array $parameters = array()) {
		parent::__construct($objects, $action, $parameters);
		$this->loadCache();
		I18nHandler::getInstance()->register('title');
	}
	
	/**
	 * Toggles the activity status of menu items.
	 */
	public function toggle() {
		foreach ($this->objects as $menuItemEditor) {
			/* @var $menuItemEditor \ultimate\data\menu\item\MenuItemEditor */
			/* @var $menuItem \ultimate\data\menu\item\MenuItem */
			$menuItemEditor->update(array(
				'isDisabled' => 1 - $menuItemEditor->__get('isDisabled')
			));
		}
	}
	
	/**
	 * Creates a bunch of menu items.
	 */
	public function createAJAX() {
		
		$this->loadCache();
		$menuItems = array();
		$parameters = array(
			'menuID' => intval($this->parameters['data']['menuID']),
			'menuItemParent' => '',
			'menuItemName' => '~TESTREPLACE~',
			'showOrder' => 0,
			'isDisabled' => 0
		);
		
		// if the new menu item is a custom link
		if ($this->parameters['data']['type'] == 'custom') {
			I18nHandler::getInstance()->readValues();
			if (I18nHandler::getInstance()->isPlainValue('title')) $parameters['menuItemName'] = StringUtil::trim(I18nHandler::getInstance()->getValue('title'));
			
			$parameters['menuItemLink'] = StringUtil::trim($this->parameters['data']['structure']['link']);
			$parameters['type'] = 'custom';
			
			$menuItem = MenuItemEditor::create($parameters);
			
			$menuItems[$menuItem->__get('menuItemID')] = $menuItem;
			$updateEntries = array();
			
			// save menu item name to database
			if (!I18nHandler::getInstance()->isPlainValue('title')) {
				I18nHandler::getInstance()->save('title', 'ultimate.menu.item.'.$menuItem->__get('menuItemID').'.menuItemName', 'ultimate.menu', PACKAGE_ID);
				$updateEntries['menuItemName'] = 'ultimate.menu.item.'.$menuItem->__get('menuItemID').'.menuItemName';
			}
			// replace empty menu item name with language variable
			if (count($updateEntries)) {
				$menuItemEditor = new MenuItemEditor($menuItem);
				$menuItemEditor->update($updateEntries);
				
				// get new menu item
				$menuItem = new MenuItem($menuItem->__get('menuItemID'));
				$menuItems[$menuItem->__get('menuItemID')] = $menuItem;
			}
		}
		else {
			WCF::getDB()->beginTransaction();
			
			foreach ($this->parameters['data']['structure'] as $parentElementID => $elementIDs) {
				foreach ($elementIDs as $elementID) {
					$element = null;
					
					switch ($this->parameters['data']['type']) {
						case 'category':
							$element = $this->categories[$elementID];
							$parameters['menuItemName'] = $element->__get('categoryTitle');
							$parameters['menuItemLink'] = $this->getCategoryLink($element, true);
							$parameters['type'] = 'category';
							break;
						case 'content':
							$element = $this->contents[$elementID];
							$parameters['menuItemName'] = $element->__get('contentTitle');
							/* @var $dateTimeObj \DateTime */
							$dateTimeObj = $element->__get('publishDateObject');
							if ($dateTimeObj->getTimestamp()) {
								$date = $dateTimeObj->format('Y/m/d');
								$parameters['menuItemLink'] = 'index.php/'.$date.'/'.$element->__get('contentSlug').'/';
							} else {
								$parameters['menuItemLink'] = '';
							}
							$parameters['type'] = 'content';
							break;
						case 'page':
							$element = $this->pages[$elementID];
							$parameters['menuItemName'] = $element->__get('pageTitle');
							$parameters['menuItemLink'] = $this->getPageLink($element, true);
							$parameters['type'] = 'page';
							break;
					}
					try {
						$menuItem = MenuItemEditor::create($parameters);
						$menuItems[$menuItem->__get('menuItemID')] = $menuItem;
					}
					catch (DatabaseException $e) {
						WCF::getDB()->rollbackTransaction();
						throw new SystemException('Couldn\'t create menu item.', $e->getCode(), 'You can\'t create menu items with the same name in the same menu.', $e);
					}
				}
			}
			WCF::getDB()->commitTransaction();
		}
		
		try {
		$menuItemsAJAX = array();
		foreach ($menuItems as $menuItemID => $menuItem) {
			$menuItemsAJAX[$menuItemID] = array(
				'menuID' => $menuItem->__get('menuID'),
				'menuItemName' => WCF::getLanguage()->get($menuItem->__get('menuItemName')),
				'menuItemNameRaw' => $menuItem->__get('menuItemName'),
				'menuItemParent' => $menuItem->__get('menuItemParent'),
				'menuItemLink' => $menuItem->__get('menuItemLink'),
				'showOrder' => $menuItem->__get('showOrder'),
				'type' => $menuItem->__get('type'),
				'isDisabled' => $menuItem->__get('isDisabled')
			);
		}
		
		return $menuItemsAJAX;
		}
		catch (Exception $e) {
			echo $e->getMessage();
			throw new SystemException($e->getMessage(), $e->getCode(), '', $e);
		}
	}
	
	/**
	 * Updates the position of menu items.
	 */
	public function updatePosition() {
		$showOrders = array();
		
		WCF::getDB()->beginTransaction();
		foreach ($this->parameters['data']['structure'] as $parentMenuItemID => $menuItemIDs) {
			if (!isset($showOrders[$parentMenuItemID])) {
				$showOrders[$parentMenuItemID] = 1;
			}
			
			foreach ($menuItemIDs as $menuItemID) {
				$this->objects[$menuItemID]->update(array(
					'menuItemParent' => $parentMenuItemID ? $this->objects[$parentMenuItemID]->__get('menuItemName') : '',
					'showOrder' => $showOrders[$parentMenuItemID]++
				));
			}
		}
		WCF::getDB()->commitTransaction();
	}
	
	/**
	 * Validates the 'createAJAX' action.
	 */
	public function validateCreateAJAX() {
		$this->validateCreate();
	}
	
	/**
	 * Validates the 'toggle' action.
	 */
	public function validateToggle() {
		$this->validateUpdate();
	}
	
	/**
	 * Validates the 'toggleContainer' action.
	 */
	public function validateToggleContainer() {
		$this->validateUpdate();
	}
	
	/**
	 * Validates the 'updatePosition' action.
	 */
	public function validateUpdatePosition() {
		// validate permissions
		if (count($this->permissionsUpdate)) {
			try {
				WCF::getSession()->checkPermissions($this->permissionsUpdate);
			}
			catch (PermissionDeniedException $e) {
				throw new ValidateActionException('Insufficient permissions');
			}
		}
		
		// validate 'structure' parameter
		if (!isset($this->parameters['data']['structure'])) {
			throw new ValidateActionException("Missing 'structure' parameter");
		}
		if (!is_array($this->parameters['data']['structure'])) {
			throw new ValidateActionException("'structure' parameter is no array");
		}
		
		// validate given menu item ids
		foreach ($this->parameters['data']['structure'] as $parentMenuItemID => $menuItemIDs) {
			if ($parentMenuItemID) {
				// validate menu item
				$menuItem = MenuItemHandler::getInstance()->getMenuItem($parentMenuItemID);
				if ($menuItem === null) {
					throw new ValidateActionException("Unknown menu item with id '".$parentMenuItemID."'");
				}
				
				$this->objects[$menuItem->__get('menuItemID')] = new $this->className($menuItem);
	
			}
			
			foreach ($menuItemIDs as $menuItemID) {
				// validate menu item
				$menuItem = MenuItemHandler::getInstance()->getMenuItem($menuItemID);
				if ($menuItem === null) {
					throw new ValidateActionException("Unknown menu item with id '".$menuItemID."'");
				}
				
				$this->objects[$menuItem->__get('menuItemID')] = new $this->className($menuItem);
			}
		}
	}
	
	/**
	 * Returns the ready-to-use category link.
	 * 
	 * @param	\ultimate\data\category\Category $category
	 * @param	boolean							 $implode
	 * @return	string[]|string
	 */
	protected function getCategoryLink(\ultimate\data\category\Category $category, $implode = false) {
		$slugs = array();
		$slugs[] = $category->__get('categorySlug');
		
		if ($id = $category->__get('categoryParent')) {
			$__category = $this->categories[$id];
			$slugs = array_merge_recursive($slugs, $this->getCategoryLink($__category));
		}
		if (!$implode) return $slugs;
		
		$slugs = array_reverse($slugs);
		return 'index.php/category/'.implode('/', $slugs).'/';
	}
	
	/**
	 * Returns the ready-to-use page link.
	 *
	 * @param	\ultimate\data\page\Page	$page
	 * @param	boolean						$implode
	 * @return	string[]|string
	 */
	protected function getPageLink(\ultimate\data\page\Page $page, $implode = false) {
		$slugs = array();
		$slugs[] = $page->__get('pageSlug');
		
		if ($id = $page->__get('pageParent')) {
			$__page = $this->pages[$id];
			$slugs = array_merge_recursive($slugs, $this->getPageLink($__page));
		}
		if (!$implode) return $slugs;
		
		$slugs = array_reverse($slugs);
		return 'index.php/'.implode('/', $slugs).'/';
	}
	
	/**
	 * Loads the cache.
	 */
	protected function loadCache() {
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
	}
}
