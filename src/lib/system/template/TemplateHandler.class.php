<?php
/**
 * Contains the TemplateHandler class.
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
 * @subpackage	system.template
 * @category	Ultimate CMS
 */
namespace ultimate\system\template;
use ultimate\data\layout\Layout;
use ultimate\data\template\Template;
use ultimate\data\IUltimateData;
use ultimate\system\blocktype\BlockTypeHandler;
use ultimate\system\cache\builder\CategoryCacheBuilder;
use ultimate\system\cache\builder\CurrentMenuCacheBuilder;
use ultimate\system\cache\builder\PageCacheBuilder;
use ultimate\system\cache\builder\TemplateCacheBuilder;
use ultimate\system\layout\LayoutHandler;
use ultimate\system\menu\custom\CustomMenu;
use ultimate\system\widget\WidgetHandler;
use wcf\data\user\group\UserGroup;
use wcf\page\IPage;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\exception\NamedUserException;
use wcf\system\exception\SystemException;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\MetaTagHandler;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Handles the templates.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.template
 * @category	Ultimate CMS
 */
class TemplateHandler extends SingletonFactory {
	/**
	 * The layout ID of the category layout.
	 * @var integer
	 */
	const CATEGORY_LAYOUT_ID = 4;
	
	/**
	 * The layout ID of the content layout.
	 * @var integer
	 */
	const CONTENT_LAYOUT_ID = 2;
	
	/**
	 * The layout ID of the page layout.
	 * @var integer
	 */
	const PAGE_LAYOUT_ID = 3;
	
	/**
	 * Contains all templates.
	 * @var \ultimate\data\template\Template[]
	 */
	protected $templatesToLayoutID = array();
	
	/**
	 * Contains all menu to template relations.	 * 
	 * @var	\ultimate\data\menu\Menu[]
	 */
	protected $menusToTemplateID = array();
	
	/**
	 * The template name.
	 * @var string
	 */
	protected $templateName = 'template';
	
	/**
	 * The template of this request.
	 * @var \ultimate\data\template\Template
	 */
	protected $template = null;
	
	/**
	 * Returns the output of the template associated with the given information.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string								$requestType	(category, content, index, page)
	 * @param	\ultimate\data\layout\Layout		$layout
	 * @param	\ultimate\data\IUltimateData|null	$requestObject	(null only if $requestType is index)
	 * @param	\wcf\page\IPage						$page
	 * @return	string
	 * @throws  SystemException
	 */
	public function getOutput($requestType, Layout $layout, $requestObject, IPage $page) {
		$requestType = mb_strtolower(StringUtil::trim($requestType));
		if ($requestType != 'index') {
			if (!($requestObject instanceof IUltimateData)) {
				throw new SystemException('The given request object is not an instance of \ultimate\data\IUltimateData.');
			}
		}
		
		// get template
		$template = $this->getTemplate($layout->__get('layoutID'));
		$this->template = $this->getRealTemplate($template, $requestType);
		
		// gathering output
		$blocks = $this->template->__get('blocks');
		$output = $this->getGeneratedOutput($this->template, $layout, $requestObject, $requestType, $blocks, $page);
		
		if ($this->template->__get('showWidgetArea')) {
			$this->initWidgetArea($this->template, $page);
		}
		
		// build menu
		$this->buildMenu($this->template, $requestObject, $requestType);
		
		// assigning template variables
		$blockIDs = array_keys($blocks);
		WCF::getTPL()->assign(array(
			'customArea' => $output,
			'blockIDs' => $blockIDs,
			'requestType' => $requestType
		));
		if ($requestObject !== null) {
			WCF::getTPL()->assign(array(
				'title' => $requestObject->getLangTitle(),
				'requestObject' => $requestObject
			));
		}
		
		// assign custom meta values (if existing)
		if ($requestObject !== null) {
			$this->assignMetaValues($requestObject);
		}
		
		// determines if spiders are allowed to index the requested site
		$this->assignSpiderSetting($requestObject, $requestType);
		
		return WCF::getTPL()->fetch($this->templateName, 'ultimate');
	}
	
	/**
	 * Returns the template attached to the given layoutID or null if there is no such template.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	integer	$layoutID
	 * @return	\ultimate\data\template\Template|null
	 */
	public function getTemplate($layoutID) {
		$layoutID = intval($layoutID);
		if (isset($this->templatesToLayoutID[$layoutID])) {
			return $this->templatesToLayoutID[$layoutID];
		}
		return null;
	}
	
	/**
	 * This method should be used if you just want to initiate the custom menu.
	 * 
	 * The method will initate the custom menu that is connected with the template of the IndexPage.
	 * This method does only work if there is a template attached to the index layout.
	 * If there is no such template, the method will return without initiating the custom menu.
	 * 
	 * @since	1.0.0
	 * @api
	 */
	public function initiateCustomMenu() {
		$layout = LayoutHandler::getInstance()->getLayoutFromObjectData(0, 'index');
		$template = $this->getTemplate($layout->__get('layoutID'));
		// if there is no template, we cannot procede further
		if ($template === null) return;
		$menu = $template->__get('menu');
		if ($menu !== null) {
			CustomMenu::getInstance()->buildMenu($menu);
			CurrentMenuCacheBuilder::getInstance()->reset();
			// rebuild menu cache
			CurrentMenuCacheBuilder::getInstance()->getData(array(), 'currentMenuItems');
		}
	}
	
	/**
	 * Initializes the template handler.
	 */
	protected function init() {
		$this->loadCache();
	}
	
	/**
	 * Loads the cache.
	 */
	protected function loadCache() {
		// templates
		$this->templatesToLayoutID = TemplateCacheBuilder::getInstance()->getData(array(), 'templatesToLayoutID');
	}
	
	/**
	 * Determines the real template.
	 * 
	 * @param	\ultimate\data\template\Template|null	$template	the template of the layout
	 * @param	string 									$requestType
	 * @return	\ultimate\data\template\Template
	 * 
	 * @throws	NamedUserException if no template is found
	 */
	protected function getRealTemplate($template, $requestType) {
		if ($template === null) {
			// check for super type
			switch ($requestType) {
				case 'category':
					$template = $this->getTemplate(self::CATEGORY_LAYOUT_ID);
					break;
				case 'content':
					$template = $this->getTemplate(self::CONTENT_LAYOUT_ID);
					break;
				case 'page':
					$template = $this->getTemplate(self::PAGE_LAYOUT_ID);
					break;
			}
				
			if ($template === null) {
				throw new NamedUserException(WCF::getLanguage()->getDynamicVariable(
					'ultimate.error.missingTemplate',
					array(
						'type' => $requestType
					)
				));
			}
		}
		return $template;
	}
	
	/**
	 * Initializes the widget area of the given template.
	 * 
	 * @param \ultimate\data\template\Template	$template
	 * @param \wcf\page\IPage					$page
	 */
	protected function initWidgetArea(Template $template, IPage $page) {
		/* @var $widgetArea \ultimate\data\widget\area\WidgetArea|null */
		$widgetArea = $template->__get('widgetArea');
		$useDefaultDashboardConfig = ($widgetArea === null ? true : false);
		$sidebarOrientation = $template->__get('widgetAreaSide');
		$sidebarCollapsed = ULTIMATE_GENERAL_TEMPLATE_COLLAPSIBLE_SIDEBARS;
			
		if ($sidebarCollapsed) {
			$sidebarCollapsed = UserCollapsibleContentHandler::getInstance()->isCollapsed(
				'com.woltlab.wcf.collapsibleSidebar',
				'de.plugins-zum-selberbauen.ultimate.template'
			);
		}
			
		WCF::getTPL()->assign(array(
			'sidebarOrientation' => $sidebarOrientation,
			'sidebarName' => 'de.plugins-zum-selberbauen.ultimate.template',
			'sidebarCollapsed' => $sidebarCollapsed
		));
			
		if ($useDefaultDashboardConfig) {
			DashboardHandler::getInstance()->loadBoxes('de.plugins-zum-selberbauen.ultimate.template', $page);
			WCF::getTPL()->assign(array(
				'useDefaultSidebar' => true
			));
		} else {
			WidgetHandler::getInstance()->loadBoxes($widgetArea, $page);
			// assign sidebar content
			WCF::getTPL()->assign(array(
				'useDefaultSidebar' => false
			));
		}
	}
	
	/**
	 * Builds the output and returns it.
	 * 
	 * @param	\ultimate\data\template\Template 	$template
	 * @param	\ultimate\data\layout\Layout 		$layout
	 * @param	\IUltimateData|null 				$requestObject
	 * @param	string 								$requestType
	 * @param   \ultimate\data\block\Block[]		$blocks
	 * @param	\wcf\page\IPage						$page
	 * @return 	string
	 */
	protected function getGeneratedOutput(Template $template, Layout $layout, $requestObject, $requestType, array $blocks, IPage $page) {
		$output = '';
		foreach ($blocks as $blockID => $block) {
			/* @var $blockTypeDatabase \ultimate\data\blocktype\BlockType */
			$blockTypeID = $block->__get('blockTypeID');
			/* @var $blockType \ultimate\system\blocktype\IBlockType */
			$blockType = BlockTypeHandler::getInstance()->getBlockType($blockTypeID);
			$blockType->init($requestType, $layout, $requestObject, $blockID, $page);
			$output .= $blockType->getHTML();
		}
		return $output;
	}
	
	/**
	 * Builds the menu (if existing).
	 * 
	 * @param	\ultimate\data\template\Template	$template
	 * @param	\ultimate\data\IUltimateData|null	$requestObject
	 * @param	string								$requestType
	 */
	protected function buildMenu(Template $template, $requestObject, $requestType) {
		$menu = $template->__get('menu');
		if ($menu !== null) {
			CustomMenu::getInstance()->buildMenu($menu);
			// reset menu cache
			CurrentMenuCacheBuilder::getInstance()->reset();
			if ($requestType != 'index') {
				$activeMenuItem = $requestObject->getTitle();
				CustomMenu::getInstance()->setActiveMenuItem($activeMenuItem);
				$result = CustomMenu::getInstance()->getActiveMenuItem(0);
				
				$menuItems = CustomMenu::getInstance()->getMenuItems();
				$parents = array();
				$startParentID = 0;
				switch ($requestType) {
					case 'category':
						$startParentID = $requestObject->__get('categoryParent');
						$categories = CategoryCacheBuilder::getInstance()->getData(array(), 'categories');
						$parents = $this->getParentCategories($startParentID, $categories);
						break;
					case 'page':
						$startParentID = $requestObject->__get('pageParent');
						$pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
						$parents = $this->getParentPages($startParentID, $pages);
						break;
					case 'content':
						$contentCategories = $requestObject->__get('categories');
						// getting the first category that is represented in the menu as active menu item
						// if none is available use index
						foreach ($contentCategories as $category) {
							if (isset($menuItems[$category->getTitle()])) {
								// determine breadcrumbs
								$startParentID = $category->__get('categoryID');
								$categories = CategoryCacheBuilder::getInstance()->getData(array(), 'categories');
								$parents = $this->getParentCategories($startParentID, $categories);
								break;
							}
						}
						break;
				}
				$this->addBreadcrumbs($parents, $menuItems);
				if ($result === null) {
					// determine lowest fitting menu item
					$activeMenuItem = $this->getActiveMenuItem($parents, $menuItems);
					if ($requestType == 'content' && !empty($parents)) {
						$parent = $parents[$startParentID];
						$activeMenuItem = $parent->getTitle();
					}
					
					CustomMenu::getInstance()->setActiveMenuItem($activeMenuItem);
				}
			} else {
				CustomMenu::getInstance()->setActiveMenuItem('ultimate.header.menu.index');
			}
			// rebuild menu cache
			CurrentMenuCacheBuilder::getInstance()->getData(array(), 'currentMenuItems');
		}
	}
	
	/**
	 * Adds the breadcrumbs.
	 *
	 * @param	\ultimate\data\IUltimateData[]		$parents
	 * @param	\ultimate\data\menu\item\MenuItem[]	$menuItems
	 */
	protected function addBreadcrumbs(array $parents, array $menuItems) {
		$parentsReversed = array_reverse($parents);
		foreach ($parentsReversed as $parent) {
			$title = $parent->getTitle();
			if (isset($menuItems[$title])) {
				foreach ($menuItems as $parentTitle => $subItems) {
					foreach ($subItems as $subItem) {
						$menuItemName = $subItem->__get('menuItemName');
						if ($menuItemName == $title) {
							WCF::getBreadcrumbs()->add(new Breadcrumb($parent->__toString(), $subItem->getLink()));
							break 2;
						}
					}
				}
			}
		}
	}
	
	/**
	 * Assigns the meta values.
	 * 
	 * @param	\ultimate\data\IUltimateData	$requestObject
	 */
	protected function assignMetaValues(IUltimateData $requestObject) {
		$metaData = $requestObject->__get('metaData');
		if (!empty($metaData)) {
			$metaDescription = (isset($metaData['metaDescription']) ? $metaData['metaDescription'] : '');
			$metaKeywords = (isset($metaData['metaKeywords']) ? $metaData['metaKeywords'] : '');
		}
		if (!empty($metaDescription)) {
			MetaTagHandler::getInstance()->removeTag('description');
			MetaTagHandler::getInstance()->addTag('description', 'description', $metaDescription);
		}
		if (!empty($metaKeywords)) {
			MetaTagHandler::getInstance()->removeTag('keywords');
			MetaTagHandler::getInstance()->addTag('keywords', 'keywords', $metaKeywords);
		}
	}
	
	/**
	 * Assigns the spider variable.
	 * 
	 * @param	\ultimate\data\IUltimateData|null	$requestObject
	 * @param	string								$requestType
	 */
	protected function assignSpiderSetting($requestObject, $requestType) {
		$allowSpidersToIndexThisPage = false;
		// only index is allowed to give a null reference
		// therefore we eliminate both index and category with this if clause
		if ($requestObject === null || $requestType == 'category') {
			$allowSpidersToIndexThisPage = true;
		}
		else {
			$visibility = $requestObject->__get('visibility');
			$allowSpidersToIndexThisPage = ($visibility == 'public');
			// If the visibility is private nobody but the creator can view the contents.
			// Therefore search engines shouldn't be able to index it.
			// Of course one could speculate that a search engine only 
			// gets access if it is publicly available but that's guessing.
			if (!$allowSpidersToIndexThisPage && $visibility == 'protected') {
				$groups = $requestObject->__get('groups');
				$allowSpidersToIndexThisPage = (isset($groups[UserGroup::EVERYONE]) || isset($groups[UserGroup::GUESTS]));
			}
		}
		WCF::getTPL()->assign('allowSpidersToIndexThisPage', $allowSpidersToIndexThisPage);
	}
	
	/**
	 * Returns the parent categories in reverse order (closest first).
	 * 
	 * @param	integer								$categoryParentID
	 * @param	\ultimate\data\category\Category[]	$categories
	 * @return	\ultimate\data\category\Category[]
	 */
	protected function getParentCategories($categoryParentID, array $categories, array $parentCategories = array()) {
		if ($categoryParentID) {
			$parent = $categories[$categoryParentID];
			$parentCategories[$categoryParentID] = $parent;
			$categoryParentID = $parent->__get('categoryParent');
			return $this->getParentCategories($categoryParentID, $categories, $parentCategories);
		} else {
			return $parentCategories;
		}
	}
	
	/**
	 * Returns the parent pages in reverse order (closest first).
	 *
	 * @param	integer						$pageParentID
	 * @param	\ultimate\data\page\Page[]	$pages
	 * @return	\ultimate\data\page\Page[]
	 */
	protected function getParentPages($pageParentID, array $pages, array $parentPages = array()) {
		if ($pageParentID) {
			$parent = $pages[$pageParentID];
			$parentPages[$pageParentID] = $parent;
			$pageParentID = $parent->__get('pageParent');
			return $this->getParentPages($pageParentID, $pages, $parentPages);
		} else {
			return $parentPages;
		}
	}
	
	/**
	 * Returns the active menu item.
	 * 
	 * @param	\ultimate\data\IUltimateData[]		$parents
	 * @param	\ultimate\data\menu\item\MenuItem[]	$menuItems
	 * @return	string
	 */
	protected function getActiveMenuItem(array $parents, array $menuItems) {
		foreach ($parents as $parent) {
			$parentTitle = $parent->getTitle();
			if (isset($menuItems[$parentTitle])) {
				return $parentTitle;
			}
		}
		return 'ultimate.header.menu.index';
	}
}
