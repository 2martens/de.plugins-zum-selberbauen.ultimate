<?php
/**
 * Contains the ContentPage class.
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
namespace ultimate\page;
use ultimate\data\content\ContentEditor;
use ultimate\system\cache\builder\ContentCacheBuilder;
use ultimate\system\cache\builder\ContentPageCacheBuilder;
use ultimate\system\layout\LayoutHandler;
use ultimate\system\template\TemplateHandler;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\RouteHandler;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows a content.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
class ContentPage extends AbstractPage {
	/**
	 * If true, the template shall be used.
	 * @var	boolean
	 */
	public $useTemplate = false;
	
	/**
	 * The Content object.
	 * @var	\ultimate\data\content\CategorizedContent
	 */
	public $content = null;
	
	/**
	 * Contains an array of the given content slugs.
	 * @var string[]
	 */
	public $contentSlugs = array();
	
	/**
	 * The output.
	 * @var string
	 */
	public $output = '';
	
	/**
	 * The layout of this content.
	 * @var \ultimate\data\layout\Layout
	 */
	public $layout = null;
	
	/**
	 * Contains all contents associated with their slug.
	 * @var \ultimate\data\content\CategorizedContent[]
	 */
	protected $contentsToSlug = array();
	
	/**
	 * Contains all contents associated with a page.
	 * @var integer[]
	 */
	protected $contentIDsToPageID = array();
	
	/**
	 * Reads the given parameters.
	 */
	public function readParameters() {
		parent::readParameters();
		/* @var $routeData string[] */
		$routeData = RouteHandler::getInstance()->getRouteData();
		$this->contentSlugs = explode('/', StringUtil::trim($routeData['contentSlug']));
	}
	
	/**
	 * Reads/Gets the data to be displayed on this page.
	 */
	public function readData() {
		parent::readData();
		$this->loadCache();
		if (isset($this->contentsToSlug[$this->contentSlugs[0]])) {
			$this->content = $this->contentsToSlug[$this->contentSlugs[0]];
		}
		else {
			throw new IllegalLinkException();
		}
		
		$this->layout = LayoutHandler::getInstance()->getLayoutFromObjectData($this->content->__get('contentID'), 'content');
	}
	
	/**
	 * Assigns variables to the template engine.
	 */
	public function assignVariables() {
		parent::assignVariables();
		// get output
		$this->output = TemplateHandler::getInstance()->getOutput('content', $this->layout, $this->content, $this);
	}
	
	/**
	 * Shows the requested page.
	 */
	public function show() {
		parent::show();
		// check if the actual content is already used by a page, if so don't display it
		if (in_array($this->content->__get('contentID'), $this->contentIDsToPageID)) {
			throw new IllegalLinkException();
		}
		// check if the actual content is published, if not throw an exception
		if ($this->content->__get('status') != 3) {
			throw new IllegalLinkException();
		}
		
		// check for visibility
		if (!$this->content->isVisible()) {
			throw new IllegalLinkException();
		}
		
		// update view count
		$contentEditor = new ContentEditor($this->content->getDecoratedObject());
		$contentEditor->updateCounters(array(
			'views' => 1
		));
		
		// everything's fine
		HeaderUtil::sendHeaders();
		echo $this->output;
	}
	
	/**
	 * Loads the cache.
	 */
	protected function loadCache() {
		$this->contentsToSlug = ContentCacheBuilder::getInstance()->getData(array(), 'contentsToSlug');
		$this->contentIDsToPageID = ContentPageCacheBuilder::getInstance()->getData(array(), 'contentIDToPageID');
	}
}
