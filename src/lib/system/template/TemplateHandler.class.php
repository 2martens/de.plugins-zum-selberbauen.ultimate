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
use ultimate\system\widgettype\WidgetTypeHandler;
use wcf\system\exception\NamedUserException;
use wcf\system\exception\SystemException;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Handles the templates.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.template
 * @category	Ultimate CMS
 */
class TemplateHandler extends SingletonFactory {
	/**
	 * Contains all templates.
	 * @var \ultimate\data\template\Template[]
	 */
	protected $templatesToLayoutID = array();
	
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
		// TODO: If a sub-type has no own template, look at the super type.
		else {
			throw new NamedUserException(WCF::getLanguage()->getDynamicVariable(
				'ultimate.error.missingTemplate', 
				array(
					'type' => $requestType
				)
			));
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
		
		// assigning template variables
		WCF::getTPL()->assign(array(
			'customArea' => $output,
			'title' => $layout->__get('layoutName')
		));
		
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
