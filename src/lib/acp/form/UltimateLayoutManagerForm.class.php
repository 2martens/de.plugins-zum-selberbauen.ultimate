<?php
/**
 * Contains the UltimateLayoutManager form.
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
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use wcf\system\exception\NamedUserException;

use ultimate\data\layout\LayoutEditor;
use ultimate\system\layout\LayoutHandler;
use wcf\acp\form\ACPForm;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\UserInputException;

/**
 * Shows the UltimateLayoutManager form.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateLayoutManagerForm extends ACPForm {
	/**
	 * @var	string
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.acp.form.ACPForm.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance.layoutManager';
	
	/**
	 * @var	string
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$templateName
	 */
	public $templateName = 'ultimateLayoutManager';
	
	/**
	 * @var	string[]
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canManageLayouts'
	);
	
	/**
	 * Contains all existing layouts.
	 * @var \ultimate\data\layout\Layout[]
	 */
	public $layouts = array();
	
	/**
	 * Contains all category layouts.
	 * @var \ultimate\data\layout\Layout[]
	 */
	public $categoryLayouts = array();
	
	/**
	 * Contains all category layouts.
	 * @var \ultimate\data\layout\Layout[]
	 */
	public $contentLayouts = array();
	
	/**
	 * Contains all category layouts.
	 * @var \ultimate\data\layout\Layout[]
	 */
	public $pageLayouts = array();
	
	/**
	 * Contains all existing templates.
	 * @var \ultimate\data\template\Template[]
	 */
	public $templates = array();
	
	/**
	 * Contains all template to layout relations.
	 * @var integer[]
	 */
	public $templateToLayout = array();
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		parent::readData();
		
		// read templates
		$cacheName = 'template';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\TemplateCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->templates = CacheHandler::getInstance()->get($cacheName, 'templates');
		
		// read layouts
		$cacheName = 'layout';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\LayoutCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->layouts = CacheHandler::getInstance()->get($cacheName, 'layouts');
		
		$cacheName = 'category';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\CategoryCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$pages = CacheHandler::getInstance()->get($cacheName, 'category');
		
		/* @var $category \ultimate\data\category\Category */
		foreach ($category as $categoryID => $category) {
			$layout = LayoutHandler::getInstance()->getLayoutFromObjectData($categoryID, 'category');
			$this->categoryLayouts[$layout->__get('layoutID')] = $layout;
		}
		
		$cacheName = 'content';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\CategoryCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$pages = CacheHandler::getInstance()->get($cacheName, 'content');
		
		/* @var $content \ultimate\data\content\Content */
		foreach ($content as $contentID => $content) {
			$layout = LayoutHandler::getInstance()->getLayoutFromObjectData($contentID, 'content');
			$this->contentLayouts[$layout->__get('layoutID')] = $layout;
		}
		
		$cacheName = 'page';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\PageCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$pages = CacheHandler::getInstance()->get($cacheName, 'pages');
		
		/* @var $page \ultimate\data\page\Page */
		foreach ($pages as $pageID => $page) {
			$layout = LayoutHandler::getInstance()->getLayoutFromObjectData($pageID, 'page');
			$this->pageLayouts[$layout->__get('layoutID')] = $layout;
		}
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#readFormParameters
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		// @todo Make sure this works.
		$layoutIDs = array_keys($this->layouts);
		foreach ($layoutIDs as $layoutID) {
			if (isset($_POST['layout'.(string) $layoutID])) {
				$this->templateToLayout[$layoutID] = intval($_POST['layout'.(string) $layoutID]);
			}
		}
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#validate
	 */
	public function validate() {
		parent::validate();
		
		$templateIDs = array_keys($this->templates);
		// checks for valid template ids
		foreach ($this->templateToLayout as $layoutID => $templateID) {
			if (in_array($templateID, $templateIDs)) continue;
			
			if (in_array($layoutID, array(1,2,3,4))) {
				if ($templateID == 0) {
					throw new UserInputException('layout'.(string) $layoutID, 'notSelected');
				}
				
				throw new UserInputException('layout'.(string) $layoutID, 'notValid');
			}
			
			if (isset($this->categoryLayouts[$layoutID])) {
				throw new UserInputException('layout2-child-template', 'notValid');
			}
			
			if (isset($this->contentLayouts[$layoutID])) {
				throw new UserInputException('layout3-child-template', 'notValid');
			}
			
			if (isset($this->pageLayouts[$layoutID])) {
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
			
			if ($templateID == 0 && !in_array($layoutID, array(1,2,3,4))) {
				unset($this->templateToLayout[$layoutID]);
				continue;
			}
		}
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		parent::save();
		
		// assign templates
		foreach ($this->templateToLayout as $layoutID => $templateID) {
			$layoutEditor = new LayoutEditor($this->layouts[$layoutID]);
			$layoutEditor->assignTemplate($templateID);
		}
		
		$this->saved();
		
		WCF::getTPL()->assign(
			'success', true
		);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
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
	}
}
