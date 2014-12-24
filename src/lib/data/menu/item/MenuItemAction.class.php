<?php
/**
 * Contains the menuItem data model action class.
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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.menu.item
 * @category	Ultimate CMS
 */
namespace ultimate\data\menu\item;
use ultimate\data\category\Category;
use ultimate\data\page\Page;
use ultimate\system\cache\builder\CategoryCacheBuilder;
use ultimate\system\cache\builder\ContentCacheBuilder;
use ultimate\system\cache\builder\PageCacheBuilder;
use ultimate\system\menu\item\MenuItemHandler;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISortableAction;
use wcf\data\IToggleAction;
use wcf\system\database\DatabaseException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\SystemException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Executes menu item-related functions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.menu.item
 * @category	Ultimate CMS
 */
class MenuItemAction extends AbstractDatabaseObjectAction implements ISortableAction, IToggleAction {
	/**
	 * The class name.
	 * @var	string
	 */
	public $className = '\ultimate\data\menu\item\MenuItemEditor';
	
	/**
	 * Array of permissions that are required for create action.
	 * @var	string[]
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canManageMenuItems');
	
	/**
	 * Array of permissions that are required for delete action.
	 * @var	string[]
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canManageMenuItems');
	
	/**
	 * Array of permissions that are required for update action.
	 * @var	string[]
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canManageMenuItems');
	
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
	 * page menu item editor
	 * @var	\ultimate\data\menu\item\MenuItemEditor
	 */
	public $menuItemEditor = null;
	
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
	 * Creates a menu item.
	 * 
	 * @return	MenuItem
	 */
	public function create() {
		// calculate show order
		$this->parameters['data']['showOrder'] = MenuItemEditor::getShowOrder(
			$this->parameters['data']['showOrder'], 
			$this->parameters['data']['menuID'], 
			$this->parameters['data']['menuItemParent']
		);
		
		$menuItem = parent::create();
		MenuItemEditor::updateLandingPage($menuItem->__get('menuID'));
		return $menuItem;
	}
	
	/**
	 * Deletes this object.
	 */
	public function delete() {
		$returnValues = parent::delete();
		$menuItem = array_shift($this->objects);
		MenuItemEditor::updateLandingPage($menuItem->__get('menuID'));
		return $returnValues;
	}
	
	/**
	 * Updates one or more objects.
	 */
	public function update() {
		parent::update();
		$menuItem = array_shift($this->objects);
		MenuItemEditor::updateLandingPage($menuItem->__get('menuID'));
	}
	
	/**
	 * Toggles the activity status of menu items.
	 */
	public function toggle() {
		$this->menuItemEditor->update(array(
			'isDisabled' => ($this->menuItemEditor->isDisabled ? 0 : 1)
		));
	}
	
	/**
	 * Creates a bunch of menu items.
	 * 
	 * @since	1.0.0
	 * 
	 * @return	array[]
	 * @throws  \wcf\system\exception\SystemException
	 */
	public function createAJAX() {
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
			
			$linkType = StringUtil::trim($this->parameters['data']['structure']['linkType']);
			if ($linkType == 'controller') {
				$parameters['menuItemController'] = StringUtil::trim($this->parameters['data']['structure']['controller']);
			} else if ($linkType == 'url') {
				$parameters['menuItemLink'] = StringUtil::trim($this->parameters['data']['structure']['url']);
			}
			
			$parameters['type'] = 'custom';
			$parameters['showOrder'] = MenuItemEditor::getShowOrder(
				$parameters['showOrder'],
				$parameters['menuID'],
				$parameters['menuItemParent']
			);
			$menuItem = MenuItemEditor::create($parameters);
			
			$menuItems[$menuItem->__get('menuItemID')] = $menuItem;
			$updateEntries = array();
			
			// save menu item name to database
			if (!I18nHandler::getInstance()->isPlainValue('title')) {
				I18nHandler::getInstance()->save('title', 'ultimate.menu.item.'.$menuItem->__get('menuItemID').'.menuItemName', 'ultimate.menu', PACKAGE_ID);
				$updateEntries['menuItemName'] = 'ultimate.menu.item.'.$menuItem->__get('menuItemID').'.menuItemName';
			}
			// replace empty menu item name with language variable
			if (!empty($updateEntries)) {
				$menuItemEditor = new MenuItemEditor($menuItem);
				$menuItemEditor->update($updateEntries);
				
				// get new menu item
				$menuItem = new MenuItem($menuItem->__get('menuItemID'));
				$menuItems[$menuItem->__get('menuItemID')] = $menuItem;
			}
			MenuItemEditor::resetCache();
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
								$date = $dateTimeObj->format('Y-m-d');
								$parameters['menuItemLink'] = $date.'/'.$element->__get('contentSlug').'/';
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
						$parameters['showOrder'] = MenuItemEditor::getShowOrder(
							$parameters['showOrder'],
							$parameters['menuID'],
							$parameters['menuItemParent']
						);
						$menuItem = MenuItemEditor::create($parameters);
						$menuItems[$menuItem->__get('menuItemID')] = new ViewableMenuItem($menuItem);
						MenuItemEditor::resetCache();
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
				'isDisabled' => $menuItem->__get('isDisabled'),
				'canDisable' => $menuItem->canDisable(),
				'canDelete' => $menuItem->canDelete(),
				'confirmMessage' => WCF::getLanguage()->getDynamicVariable(
					'wcf.acp.pageMenu.delete.sure', 
					array(
						'__menuItem' => $menuItem->__toString()
					)
				)
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
	 * 
	 * @since	1.0.0
	 */
	public function updatePosition() {
		WCF::getDB()->beginTransaction();
		$menuID = -1;
		foreach ($this->parameters['data']['structure'] as $parentMenuItemID => $menuItemIDs) {
			foreach ($menuItemIDs as $showOrder => $menuItemID) {
				if ($menuID == -1) $menuID = $this->objects[$menuItemID]->__get('menuID');
				$this->objects[$menuItemID]->update(array(
					'menuItemParent' => $parentMenuItemID ? $this->objects[$parentMenuItemID]->__get('menuItemName') : '',
					'showOrder' => $showOrder + 1
				));
			}
		}
		WCF::getDB()->commitTransaction();
		
		MenuItemEditor::updateLandingPage($menuID);
		
		MenuItemEditor::resetCache();
	}
	
	/**
	 * Validates the 'createAJAX' action.
	 * 
	 * @since	1.0.0
	 * @internal	Calls validateCreate.
	 */
	public function validateCreateAJAX() {
		$this->validateCreate();
	}
	
	/**
	 * Validates the 'toggle' action.
	 * 
	 * @since	1.0.0
	 * @internal	Calls validateUpdate.
	 */
	public function validateToggle() {
		$this->menuItemEditor = $this->getSingleObject();
		if ($this->menuItemEditor->isLandingPage) {
			throw new PermissionDeniedException();
		}
		
		WCF::getSession()->checkPermissions($this->permissionsUpdate);
	}
	
	/**
	 * Validates the 'updatePosition' action.
	 * 
	 * @since	1.0.0
	 */
	public function validateUpdatePosition() {
		// validate permissions
		if (!empty($this->permissionsUpdate)) {
			WCF::getSession()->checkPermissions($this->permissionsUpdate);
		}
		
		// validate 'structure' parameter
		if (!isset($this->parameters['data']['structure'])) {
			throw new SystemException("Missing 'structure' parameter.");
		}
		if (!is_array($this->parameters['data']['structure'])) {
			throw new SystemException("'structure' parameter is no array.");
		}
		
		// validate given menu item ids
		foreach ($this->parameters['data']['structure'] as $parentMenuItemID => $menuItemIDs) {
			if ($parentMenuItemID) {
				// validate menu item
				$menuItem = MenuItemHandler::getInstance()->getMenuItem($parentMenuItemID);
				if ($menuItem === null) {
					throw new SystemException("Unknown menu item with id '".$parentMenuItemID."'.");
				}
				
				$this->objects[$menuItem->__get('menuItemID')] = new $this->className($menuItem);
	
			}
			
			foreach ($menuItemIDs as $menuItemID) {
				// validate menu item
				$menuItem = MenuItemHandler::getInstance()->getMenuItem($menuItemID);
				if ($menuItem === null) {
					throw new SystemException("Unknown menu item with id '".$menuItemID."'.");
				}
				
				$this->objects[$menuItem->__get('menuItemID')] = new $this->className($menuItem);
			}
		}
	}
	
	/**
	 * Returns the ready-to-use category link.
	 * 
	 * @since	1.0.0
	 * 
	 * @param	\ultimate\data\category\Category $category
	 * @param	boolean							 $implode
	 * @return	string[]|string
	 */
	protected function getCategoryLink(Category $category, $implode = false) {
		$slugs = array();
		$slugs[] = $category->__get('categorySlug');
		
		if ($id = $category->__get('categoryParent')) {
			$__category = $this->categories[$id];
			$slugs = array_merge_recursive($slugs, $this->getCategoryLink($__category));
		}
		if (!$implode) return $slugs;
		
		$slugs = array_reverse($slugs);
		return 'category/'.implode('_', $slugs).'/';
	}
	
	/**
	 * Returns the ready-to-use page link.
	 * 
	 * @since	1.0.0
	 *
	 * @param	\ultimate\data\page\Page	$page
	 * @param	boolean						$implode
	 * @return	string[]|string
	 */
	protected function getPageLink(Page $page, $implode = false) {
		$slugs = array();
		$slugs[] = $page->__get('pageSlug');
		
		if ($id = $page->__get('pageParent')) {
			$__page = $this->pages[$id];
			$slugs = array_merge_recursive($slugs, $this->getPageLink($__page));
		}
		if (!$implode) return $slugs;
		
		$slugs = array_reverse($slugs);
		return implode('_', $slugs).'/';
	}
	
	/**
	 * Loads the cache.
	 * 
	 * @since	1.0.0
	 */
	protected function loadCache() {
		$this->categories = CategoryCacheBuilder::getInstance()->getData(array(), 'categories');
		$this->contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		$this->pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
	}
}
