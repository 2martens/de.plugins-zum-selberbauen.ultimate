<?php
/**
 * The UltimateLayoutManager form.
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
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\data\layout\LayoutEditor;
use ultimate\system\cache\builder\CategoryCacheBuilder;
use ultimate\system\cache\builder\ContentCacheBuilder;
use ultimate\system\cache\builder\LayoutCacheBuilder;
use ultimate\system\cache\builder\PageCacheBuilder;
use ultimate\system\cache\builder\TemplateCacheBuilder;
use ultimate\system\layout\LayoutHandler;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows the UltimateLayoutManager form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateLayoutManagerForm extends AbstractForm {
	/**
	 * The active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance.layoutManager';
	
	/**
	 * The template name.
	 * @var	string
	 */
	public $templateName = 'ultimateLayoutManager';
	
	/**
	 * Array of needed permissions.
	 * @var	string[]
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canManageLayouts'
	);
	
	/**
	 * All existing layouts.
	 * @var \ultimate\data\layout\Layout[]
	 */
	public $layouts = array();
	
	/**
	 * All category layouts.
	 * @var \ultimate\data\layout\Layout[]
	 */
	public $categoryLayouts = array();
	
	/**
	 * All category layouts.
	 * @var \ultimate\data\layout\Layout[]
	 */
	public $contentLayouts = array();
	
	/**
	 * All category layouts.
	 * @var \ultimate\data\layout\Layout[]
	 */
	public $pageLayouts = array();
	
	/**
	 * All existing templates.
	 * @var \ultimate\data\template\Template[]
	 */
	public $templates = array();
	
	/**
	 * All template to layout relations.
	 * @var integer[]
	 */
	public $templateToLayout = array();
	
	/**
	 * Reads data.
	 */
	public function readData() {
		// read templates
		$this->templates = TemplateCacheBuilder::getInstance()->getData(array(), 'templates');
		
		// read layouts
		$this->layouts = LayoutCacheBuilder::getInstance()->getData(array(), 'layouts');
		
		parent::readData();
		
		// read templates, ugly but necessary to get the newest changes
		$this->templates = TemplateCacheBuilder::getInstance()->getData(array(), 'templates');
		
		// read layouts
		$this->layouts = LayoutCacheBuilder::getInstance()->getData(array(), 'layouts');
		
		$categories = CategoryCacheBuilder::getInstance()->getData(array(), 'categories');
		
		/* @var $category \ultimate\data\category\Category */
		foreach ($categories as $categoryID => $category) {
			$layout = LayoutHandler::getInstance()->getLayoutFromObjectData($categoryID, 'category');
			$this->categoryLayouts[$layout->__get('layoutID')] = $category->getLangTitle();
		}
		
		$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		
		/* @var $content \ultimate\data\content\Content */
		foreach ($contents as $contentID => $content) {
			$layout = LayoutHandler::getInstance()->getLayoutFromObjectData($contentID, 'content');
			$this->contentLayouts[$layout->__get('layoutID')] = $content->getTitle();
		}
		
		$pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
		
		/* @var $page \ultimate\data\page\Page */
		foreach ($pages as $pageID => $page) {
			$layout = LayoutHandler::getInstance()->getLayoutFromObjectData($pageID, 'page');
			$this->pageLayouts[$layout->__get('layoutID')] = $page->getTitle();
		}
	}
	
	/**
	 * Reads form input.
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		$layoutIDs = array_keys($this->layouts);
		foreach ($layoutIDs as $layoutID) {
			if (isset($_POST['layout'.(string) $layoutID])) {
				$this->templateToLayout[$layoutID] = intval($_POST['layout'.(string) $layoutID]);
			}
		}
	}
	
	/**
	 * Validates the form input.
	 */
	public function validate() {
		parent::validate();
		
		$templateIDs = array_keys($this->templates);
		
		// checks for valid template ids
		foreach ($this->templateToLayout as $layoutID => $templateID) {
			// if selected template is a real template everything's fine
			if (in_array($templateID, $templateIDs)) continue;
			// the four basic layouts need to have a template
			if (in_array($layoutID, array(1,2,3,4))) {
				if ($templateID == 0) {
					throw new UserInputException('layout'.(string) $layoutID, 'notSelected');
				}
				
				throw new UserInputException('layout'.(string) $layoutID, 'notValid');
			}
			
			if (isset($this->categoryLayouts[$layoutID]) && $templateID != 0) {
				throw new UserInputException('layout2-child-template', 'notValid');
			}
			
			if (isset($this->contentLayouts[$layoutID]) && $templateID != 0) {
				throw new UserInputException('layout3-child-template', 'notValid');
			}
			
			if (isset($this->pageLayouts[$layoutID]) && $templateID != 0) {
				throw new UserInputException('layout4-child-template', 'notValid');
			}
		}
		
		// checks for actual changes
		foreach ($this->templateToLayout as $layoutID => $templateID) {
			/* @var $layout \ultimate\data\layout\Layout */
			$layout = $this->layouts[$layoutID];
			/* @var $template \ultimate\data\template\Template|null */
			$template = $layout->__get('template');
			if ($template !== null && $template->__get('templateID') == $templateID) {
				unset($this->templateToLayout[$layoutID]);
				continue;
			}
		}
	}
	
	/**
	 * Saves the form input.
	 */
	public function save() {
		parent::save();
		
		// assign templates
		foreach ($this->templateToLayout as $layoutID => $templateID) {
			$layoutEditor = new LayoutEditor($this->layouts[$layoutID]);
			if ($templateID == 0) {
				$layoutEditor->removeTemplate();
			}
			else {
				$layoutEditor->assignTemplate($templateID);
			}
		}
		LayoutEditor::resetCache();
		
		$this->saved();
		
		$url = LinkHandler::getInstance()->getLink('UltimateLayoutManager', array(
			'application' => 'ultimate'
		), 'success');
		HeaderUtil::redirect($url);
		// after initiating the redirect, no other code should be executed as the request for the original resource has ended
		exit;
	}
	
	/**
	 * Assigns template variables.
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'layouts' => $this->layouts,
			'templates' => $this->templates,
			'categoryLayouts' => $this->categoryLayouts,
			'contentLayouts' => $this->contentLayouts,
			'pageLayouts' => $this->pageLayouts
		));
		if (isset($_REQUEST['success'])) {
			WCF::getTPL()->assign('success', true);
		}
	}
}
