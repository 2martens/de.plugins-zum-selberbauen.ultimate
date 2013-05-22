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
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.template
 * @category	Ultimate CMS
 */
namespace ultimate\system\template;
use ultimate\data\template\Template;
use ultimate\data\widget\WidgetNodeList;
use ultimate\system\blocktype\BlockTypeHandler;
use ultimate\system\cache\builder\TemplateCacheBuilder;
use ultimate\system\layout\LayoutHandler;
use ultimate\system\menu\custom\CustomMenu;
use ultimate\system\widgettype\WidgetTypeHandler;
use wcf\system\exception\NamedUserException;
use wcf\system\exception\SystemException;
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
	const CATEGORY_LAYOUT_ID = 1;
	
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
	 * @param	string											$requestType
	 * @param	\ultimate\data\layout\Layout					$layout
	 * @param	\ultimate\data\AbstractUltimateDatabaseObject	$requestObject
	 * @return	string
	 */
	public function getOutput($requestType, \ultimate\data\layout\Layout $layout, $requestObject) {
		$requestType = strtolower(StringUtil::trim($requestType));
		if ($requestType != 'index') {
			if (!($requestObject instanceof \ultimate\data\AbstractUltimateDatabaseObject)) {
				throw new SystemException('The given request object is not an instance of \ultimate\data\AbstractUltimateDatabaseObject.');
			}
		}
		
		$template = $this->getTemplate($layout->__get('layoutID'));
		
		// get sidebar content
		/* @var $widgetArea \ultimate\data\widget\area\WidgetArea|null */
		if ($template !== null) $widgetArea = $template->__get('widgetArea');
		else {
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
			
			if ($template !== null) {
				$widgetArea = $template->__get('widgetArea');
			} else {
				throw new NamedUserException(WCF::getLanguage()->getDynamicVariable(
					'ultimate.error.missingTemplate', 
					array(
						'type' => $requestType
					)
				));
			}
		}
		if ($widgetArea !== null && $template->__get('showWidgetArea')) {
			$sidebarOutput = '';
			$sidebarOrientation = $template->__get('widgetAreaSide');
			$widgetNodeList = new WidgetNodeList($widgetArea->__get('widgetAreaID'));
			foreach ($widgetNodeList as $widget) {
				$widgetTypeID = $widget->__get('widgetTypeID');
				/* @var $widgetType \ultimate\system\widgettype\IWidgetType */
				$widgetType = WidgetTypeHandler::getInstance()->getWidgetType($widgetTypeID);
				$widgetType->init($widget->__get('widgetID'));
				$sidebarOutput .= $widgetType->getHTML($requestType, $layout);
			}
			// assign sidebar content
			WCF::getTPL()->assign(array(
				'sidebar' => $sidebarOutput,
				'sidebarOrientation' => $sidebarOrientation
			));
		}
		
		// gathering output
		$output = '';
		$blocks = $template->__get('blocks');
		foreach ($blocks as $blockID => $block) {
			/* @var $blockTypeDatabase \ultimate\data\blocktype\BlockType */
			$blockTypeID = $block->__get('blockTypeID');
			/* @var $blockType \ultimate\system\blocktype\IBlockType */
			$blockType = BlockTypeHandler::getInstance()->getBlockType($blockTypeID);
			$blockType->init($requestType, $layout, $requestObject, $blockID);
			$output .= $blockType->getHTML();
		}
		
		// build menu
		$menu = $template->__get('menu');
		if ($menu !== null) {
			CustomMenu::getInstance()->buildMenu($menu);
		}
		$blockIDs = array_keys($blocks);
		
		
		// assigning template variables
		WCF::getTPL()->assign(array(
			'customArea' => $output,
			'blockIDs' => $blockIDs
		));
		if ($requestObject !== null) {
			WCF::getTPL()->assign('title', $requestObject->__toString());
		}
		
		// assign custom meta values (if existing)
		if ($requestObject !== null) {
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
}
