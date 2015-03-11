<?php
/**
 * Contains the CategoryPage class.
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
use ultimate\system\cache\builder\CategoryCacheBuilder;
use ultimate\system\layout\LayoutHandler;
use ultimate\system\template\TemplateHandler;
use ultimate\util\CategoryUtil;
use wcf\page\AbstractPage;
use wcf\page\MultipleLinkPage;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\RouteHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows a category.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
class CategoryPage extends MultipleLinkPage {
	/**
	 * If true, the template shall be used.
	 * @var	boolean
	 */
	public $useTemplate = false;
	
	/**
	 * The Category object.
	 * @var	\ultimate\data\category\Category
	 */
	public $category = null;
	
	/**
	 * Contains an array of the given category slugs.
	 * @var	string[]
	 */
	public $categorySlugs = array();
	
	/**
	 * The output.
	 * @var string
	 */
	public $output = '';
	
	/**
	 * The layout.
	 * @var \ultimate\data\layout\Layout
	 */
	public $layout = null;
	
	/**
	 * Reads the given parameters.
	 */
	public function readParameters() {
		parent::readParameters();
		/* @var $routeData string[] */
		$routeData = RouteHandler::getInstance()->getRouteData();
		$this->categorySlugs = explode('_', StringUtil::trim($routeData['categorySlug']));
	}
	
	/**
	 * Reads/Gets the data to be displayed on this page.
	 */
	public function readData() {
		AbstractPage::readData();
		$categoriesToSlug = $this->loadCache();
		
		if (isset($categoriesToSlug[$this->categorySlugs[0]])) {
			/* @var $category \ultimate\data\category\Category */
			$category = $categoriesToSlug[$this->categorySlugs[0]];
			if (count($this->categorySlugs) > 1) {
				$category = CategoryUtil::getRealCategory($category, 1, $this->categorySlugs);
			}
			$this->category = $category;
		}
		else {
			throw new IllegalLinkException();
		}
		
		$this->layout = LayoutHandler::getInstance()->getLayoutFromObjectData($this->category->__get('categoryID'), 'category');
	}
	
	/**
	 * Assigns variables to the template engine.
	 */
	public function assignVariables() {
		AbstractPage::assignVariables();
		// get output
		$this->output = TemplateHandler::getInstance()->getOutput('category', $this->layout, $this->category, $this);
	}
	
	/**
	 * Assigns the multiple link variables.
	 */
	public function assignMultipleLinkVariables() {
		// assign page parameters
		WCF::getTPL()->assign(array(
			'pageNo' => $this->pageNo,
			'amountOfPages' => $this->pages,
			'items' => $this->items,
			'itemsPerPage' => $this->itemsPerPage,
			'startIndex' => $this->startIndex,
			'endIndex' => $this->endIndex
		));
	}
	
	/**
	 * Shows the requested page.
	 */
	public function show() {
		parent::show();
		HeaderUtil::sendHeaders();
		echo $this->output;
	}
	
	/**
	 * Calculates the number of pages and
	 * handles the given page number parameter.
	 */
	public function calculateNumberOfPages() {
		// call calculateNumberOfPages event
		EventHandler::getInstance()->fireAction($this, 'calculateNumberOfPages');
		
		// calculate number of pages
		// the items need to be inserted via content block type
		$this->pages = intval(ceil($this->items / $this->itemsPerPage));
		
		// correct active page number
		if ($this->pageNo > $this->pages) $this->pageNo = $this->pages;
		if ($this->pageNo < 1) $this->pageNo = 1;
		
		// calculate start and end index
		$this->startIndex = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->endIndex = $this->startIndex + $this->itemsPerPage;
		$this->startIndex++;
		if ($this->endIndex > $this->items) $this->endIndex = $this->items;
	}
	
	/**
	 * Loads the cache.
	 */
	protected function loadCache() {
		return CategoryCacheBuilder::getInstance()->getData(array(), 'categoriesToSlug');
	}
}
