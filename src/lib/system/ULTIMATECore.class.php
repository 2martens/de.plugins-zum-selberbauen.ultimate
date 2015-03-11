<?php
/**
 * Contains the ULTIMATECore class.
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
 * @subpackage	system
 * @category	Ultimate CMS
 */
namespace ultimate\system;
use ultimate\system\request\Route;
use wcf\system\application\AbstractApplication;
use wcf\system\menu\page\PageMenu;
use wcf\system\request\RequestHandler;
use wcf\system\request\RouteHandler;

/**
 * The core class of the Ultimate CMS.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system
 * @category	Ultimate CMS
 */
class ULTIMATECore extends AbstractApplication {
	/**
	 * The abbreviation of this application.
	 * @var	string
	 */
	protected $abbreviation = 'ultimate';
	
	/**
	 * Calls all init functions of the Ultimate Core class.
	 */
	public function __run() {
		$this->initRoutes();
		PageMenu::getInstance()->setActiveMenuItem('ultimate.header.menu.index');
	}
	
	/**
	 * Inits the custom routes.
	 */
	protected function initRoutes() {
		$pageRoute = new Route('pageRoute-'.PACKAGE_ID);
		$pageRoute->setSchema('/{pageSlug}/', 'Page');
		$pageRoute->setParameterOption('pageSlug', null, '[a-z]+(?:\-{1}[a-zA-Z0-9]+)*(?:\_{1}[a-zA-Z]+(?:\-{1}[a-zA-Z0-9]+)*)*');
		RouteHandler::getInstance()->addRoute($pageRoute);
		
		$categoryRoute = new Route('categoryRoute-'.PACKAGE_ID);
		$categoryRoute->setSchema('/{category}/{categorySlug}/', 'Category');
		$categoryRoute->setParameterOption('category', 'category', 'category');
		$categoryRoute->setParameterOption('categorySlug', null, '[a-zA-Z]+(?:\-{1}[a-zA-Z0-9]+)*(?:\_{1}[a-zA-Z]+(?:\-{1}[a-zA-Z0-9]+)*)*');
		RouteHandler::getInstance()->addRoute($categoryRoute);
		
		$contentRoute = new Route('contentRoute-'.PACKAGE_ID);
		$contentRoute->setSchema('/{date}/{contentSlug}/', 'Content');
		$contentRoute->setParameterOption('date', null, '2[0-9]{3}\-[0-9]{2}\-[0-9]{2}');
		$contentRoute->setParameterOption('contentSlug', null, '[a-zA-Z]+(\-{1}[a-zA-Z0-9]+)*');
		RouteHandler::getInstance()->addRoute($contentRoute);
		
		$editSuiteRoute = new Route('editSuiteRoute-'.PACKAGE_ID);
		$editSuiteRoute->setSchema('/{parent}/{controller}/{id}/', null);
		$editSuiteRoute->setParameterOption('parent', null, '[a-zA-Z]+');
		$editSuiteRoute->setParameterOption('controller', null, '[a-zA-Z]+');
		$editSuiteRoute->setParameterOption('id', null, '\d+', true);
		RouteHandler::getInstance()->addRoute($editSuiteRoute);
	}
}
