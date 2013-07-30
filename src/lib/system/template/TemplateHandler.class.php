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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.template
 * @category	Ultimate CMS
 */
namespace ultimate\system\template;
use ultimate\data\layout\Layout;
use ultimate\data\template\Template;
use ultimate\data\widget\WidgetNodeList;
use ultimate\data\AbstractUltimateDatabaseObject;
use ultimate\data\IUltimateData;
use ultimate\system\blocktype\BlockTypeHandler;
use ultimate\system\cache\builder\CategoryCacheBuilder;
use ultimate\system\cache\builder\ContentCategoryCacheBuilder;
use ultimate\system\cache\builder\PageCacheBuilder;
use ultimate\system\cache\builder\TemplateCacheBuilder;
use ultimate\system\layout\LayoutHandler;
use ultimate\system\menu\custom\CustomMenu;
use ultimate\system\widgettype\WidgetTypeHandler;
use ultimate\system\widget\WidgetHandler;
use wcf\data\DatabaseObjectDecorator;
use wcf\page\IPage;
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.template
 * @category	Ultimate CMS
 */
class TemplateHandler extends SingletonFactory {
	/**
	 * Contains the layout ID of the category layout.
	 * @var integer
	 */
	const CATEGORY_LAYOUT_ID = 4;
	
	/**
	 * Contains the layout ID of the content layout.
	 * @var integer
	 */
	const CONTENT_LAYOUT_ID = 2;
	
	/**
	 * Contains the layout ID of the page layout.
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
	 * Contains the template name.
	 * @var string
	 */
	protected $templateName = 'template';
	
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
	 */
	public function getOutput($requestType, Layout $layout, $requestObject, IPage $page) {
		$requestType = strtolower(StringUtil::trim($requestType));
		if ($requestType != 'index') {
			if (!($requestObject instanceof IUltimateData)) {
				throw new SystemException('The given request object is not an instance of \ultimate\data\IUltimateData.');
			}
		}
		
		// get template
		$template = $this->getTemplate($layout->__get('layoutID'));
		$template = $this->getRealTemplate($template, $requestType);
		
		if ($template->__get('showWidgetArea')) {
			$this->initWidgetArea($template, $page);
		}
		
		// gathering output
		$blocks = $template->__get('blocks');
		$output = $this->getGeneratedOutput($template, $layout, $requestObject, $requestType, $blocks);
		
		// build menu
		$this->buildMenu($template, $requestObject, $requestType);
		
		// assigning template variables
		$blockIDs = array_keys($blocks);
		WCF::getTPL()->assign(array(
			'customArea' => $output,
			'blockIDs' => $blockIDs
		));
		if ($requestObject !== null) {
			WCF::getTPL()->assign('title', $requestObject->__toString());
		}
		
		// assign custom meta values (if existing)
		if ($requestObject !== null) {
			$this->assignMetaValues($requestObject);
		}
		
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
	 * The method will initate the custom menu that is connected with the template of the IndexPage.
	 * 
	 * @since	1.0.0
	 * @api
	 */
	public function initiateCustomMenu() {
		$layout = LayoutHandler::getInstance()->getLayoutFromObjectData(0, 'index');
		$template = $this->getTemplate($layout->__get('layoutID'));
		$menu = $template->__get('menu');
		if ($menu !== null) {
			CustomMenu::getInstance()->buildMenu($menu);
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
	 * @return 	string
	 */
	protected function getGeneratedOutput(Template $template, Layout $layout, $requestObject, $requestType, array $blocks) {
		$output = '';
		foreach ($blocks as $blockID => $block) {
			/* @var $blockTypeDatabase \ultimate\data\blocktype\BlockType */
			$blockTypeID = $block->__get('blockTypeID');
			/* @var $blockType \ultimate\system\blocktype\IBlockType */
			$blockType = BlockTypeHandler::getInstance()->getBlockType($blockTypeID);
			$blockType->init($requestType, $layout, $requestObject, $blockID);
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
			if ($requestType != 'index') {
				$activeMenuItem = $requestObject->getTitle();
				CustomMenu::getInstance()->setActiveMenuItem($activeMenuItem);
				$result = CustomMenu::getInstance()->getActiveMenuItem(0);
				if ($result === null) {
					// determine lowest fitting menu item
					$menuItems = CustomMenu::getInstance()->getMenuItems();
					$activeMenuItem = 'ultimate.header.menu.index';
					switch ($requestType) {
						case 'category':
							$categoryParentID = $requestObject->__get('categoryParent');
							$categories = CategoryCacheBuilder::getInstance()->getData(array(), 'categories');
							$activeMenuItem = $this->getActiveMenuItemCategory($categoryParentID, $categories, $menuItems);
							break;
						case 'page':
							$pageParentID = $requestObject->__get('pageParent');
							$pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
							$activeMenuItem = $this->getActiveMenuItemPage($pageParentID, $pages, $menuItems);
							break;
						case 'content':
							$contentCategories = $requestObject->__get('categories');
							// getting the first category that is represented in the menu as active menu item
							// if none is available use index
							foreach ($contentCategories as $category) {
								if (isset($menuItems[$category->getTitle()])) {
									$activeMenuItem = $category->getTitle();
									break;
								}
							}
							break;
					}
					CustomMenu::getInstance()->setActiveMenuItem($activeMenuItem);
				}
			} else {
				CustomMenu::getInstance()->setActiveMenuItem('ultimate.header.menu.index');
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
		$metaDescription = $metaData['metaDescription'];
		$metaKeywords = $metaData['metaKeywords'];
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
	 * Returns the active menu item for a category.
	 * 
	 * @param	integer 							$categoryParentID
	 * @param	\ultimate\data\category\Category[]	$categories
	 * @param	\ultimate\data\menu\item\MenuItem[]	$menuItems
	 * @return	string
	 */
	protected function getActiveMenuItemCategory($categoryParentID, array $categories, array $menuItems) {
		if ($categoryParentID) {
			$parent = $categories[$categoryParentID];
			$parentTitle = $parent->getTitle();
			if (isset($menuItems[$parentTitle])) {
				$activeMenuItem = $parentTitle;
				return $parentTitle;
			} else {
				$categoryParentID = $parent->__get('categoryParent');
				return $this->getActiveMenuItemCategory($categoryParentID, $categories);
			}
		} else {
			return 'ultimate.header.menu.index';
		}
	}
	
	/**
	 * Returns the active menu item for a page.
	 *
	 * @param	integer 							$pageParentID
	 * @param	\ultimate\data\page\Page[]			$pages
	 * @param	\ultimate\data\menu\item\MenuItem[]	$menuItems
	 * @return	string
	 */
	protected function getActiveMenuItemPage($pageParentID, array $pages, array $menuItems) {
		if ($pageParentID) {
			$parent = $pages[$pageParentID];
			$parentTitle = $parent->getTitle();
			if (isset($menuItems[$parentTitle])) {
				return $parentTitle;
			} else {
				$pageParentID = $parent->__get('pageParent');
				return $this->getActiveMenuItemPage($pageParentID, $pages);
			}
		} else {
			return 'ultimate.header.menu.index';
		}
	}
}
