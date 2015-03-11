<?php
/**
 * Contains the PagePage class.
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
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
namespace ultimate\page;
use ultimate\data\content\ContentEditor;
use ultimate\system\cache\builder\PageCacheBuilder;
use ultimate\system\layout\LayoutHandler;
use ultimate\system\template\TemplateHandler;
use ultimate\util\PageUtil;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows a page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
class PagePage extends AbstractPage {
	/**
	 * If true, the template shall be used.
	 * @var	boolean
	 */
	public $useTemplate = false;
	
	/**
	 * The Page object.
	 * @var	\ultimate\data\page\Page
	 */
	public $page = null;
	
	/**
	 * Contains an array of the given page slugs.
	 * @var	string[]
	 */
	public $pageSlugs = array();
	
	/**
	 * The output.
	 * @var string
	 */
	public $output = '';
	
	/**
	 * The layout of this page.
	 * @var \ultimate\data\layout\Layout
	 */
	public $layout = null;
	
	/**
	 * Reads the given parameters.
	 */
	public function readParameters() {
		parent::readParameters();
		if (isset($_GET['pageSlug'])) $this->pageSlugs = explode('_', StringUtil::trim($_GET['pageSlug']));
	}
	
	/**
	 * Reads/Gets the data to be displayed on this page.
	 */
	public function readData() {
		parent::readData();
		$pagesToSlug = $this->loadCache();
		
		if (isset($pagesToSlug[$this->pageSlugs[0]])) {
			/* @var $page \ultimate\data\page\Page */
			$page = $pagesToSlug[$this->pageSlugs[0]];
			if (count($this->pageSlugs) > 1) {
				$page = PageUtil::getRealPage($page, 1, $this->pageSlugs);
			}
			$this->page = $page;
		}
		else {
			throw new IllegalLinkException();
		}
		
		$this->layout = LayoutHandler::getInstance()->getLayoutFromObjectData($this->page->__get('pageID'), 'page');
	}
	
	/**
	 * Assigns variables to the template engine.
	 */
	public function assignVariables() {
		parent::assignVariables();
		// get output
		$this->output = TemplateHandler::getInstance()->getOutput('page', $this->layout, $this->page, $this);
	}
	
	/**
	 * Shows the requested page.
	 */
	public function show() {
		parent::show();
		// check if the actual content is published, if not throw an exception
		if ($this->page->__get('status') != 3) {
			throw new IllegalLinkException();
		}
		
		// check visibility
		if (!$this->page->isVisible()) {
			throw new IllegalLinkException();
		}
		
		// update view count
		$contentEditor = new ContentEditor($this->page->__get('content'));
		$contentEditor->updateCounters(array(
			'views' => 1
		));
		
		HeaderUtil::sendHeaders();
		echo $this->output;
	}
	
	/**
	 * Loads the cache.
	 * 
	 * @internal
	 * 
	 * @return	\ultimate\data\page\Page[]
	 */
	protected function loadCache() {
		return PageCacheBuilder::getInstance()->getData(array(), 'pagesToSlug');
	}
}
