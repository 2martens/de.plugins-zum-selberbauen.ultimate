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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
namespace ultimate\page;
use ultimate\system\template\TemplateHandler;
use ultimate\util\CategoryUtil;
use wcf\page\AbstractPage;
use wcf\system\cache\CacheHandler;
use wcf\system\request\RouteHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows a category.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
class CategoryPage extends AbstractPage {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$useTemplate
	 * @var	boolean
	 */
	public $useTemplate = false;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededModules
	 * @var	string[]
	 */
	public $neededModules = array(
		'ULTIMATE_MODULE_ULTIMATEFRONTEND'
	);
	
	/**
	 * Contains the Category object.
	 * @var	\ultimate\data\category\Category
	*/
	public $category = null;
	
	/**
	 * Contains an array of the given category slugs.
	 * @var	string[]
	 */
	public $categorySlugs = array();
	
	/**
	 * Contains the output.
	 * @var string
	*/
	public $output = '';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readParameters
	 */
	public function readParameters() {
		parent::readParameters();
		/* @var $routeData string[] */
		$routeData = RouteHandler::getInstance()->getRouteData();
		$this->categorySlugs = explode('/', StringUtil::trim($routeData['categorySlug']));
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		parent::readData();
		$categoriesToSlug = $this->loadCache();
		/* @var $page \ultimate\data\category\Category */
		$page = $categoriesToSlug[$this->categorySlugs[0]];
		if (count($this->categorySlugs) > 1) {
			$category = CategoryUtil::getRealCategory($category, 1, $this->categorySlugs);
		}
		$this->category = $category;
		$layout = LayoutHandler::getInstance()->getLayoutFromName($this->category->__get('categoryTitle'));
		// get output
		$this->output = TemplateHandler::getInstance()->getOutput('category', $layout, $category);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#show
	 */
	public function show() {
		parent::show();
		HeaderUtil::sendHeaders();
		echo $this->output;
		exit;
	}
	
	/**
	 * Loads the cache.
	 */
	protected function loadCache() {
		$cacheName = 'category';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\CategoryCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		return CacheHandler::getInstance()->get($cacheName, 'categoriesToSlug');
	}
}
