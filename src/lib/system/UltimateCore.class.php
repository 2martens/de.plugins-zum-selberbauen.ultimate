<?php
namespace ultimate\system;
use wcf\system\style\StyleHandler;

use wcf\system\application\AbstractApplication;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\menu\page\PageMenu;
use wcf\system\package\PackageDependencyHandler;
use wcf\system\request\LinkHandler;
use wcf\system\request\Route;
use wcf\system\request\RouteHandler;
use wcf\system\WCF;

//defines global version
define('ULTIMATE_VERSION', '1.0.0 Alpha 1 (Indigo)');

/**
 * The core class of the Ultimate CMS.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system
 * @category	Ultimate CMS
 */
class UltimateCore extends AbstractApplication {
	/**
	 * Calls all init functions of the Ultimate Core class.
	 */
	protected function init() {
		$this->initTPL();
		$this->initRoutes();
		$this->initStyle();
		PageMenu::getInstance()->setActiveMenuItem('ultimate.header.menu.index');
		WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('ultimate.header.menu.index'), LinkHandler::getInstance()->getLink('Index', array('application' => 'ultimate'))));
	}
	
	/**
	 * @see	\wcf\system\WCF::initTPL()
	 */
	protected function initTPL() {
		if (class_exists('wcf\system\WCFACP', false)) {
			WCF::getTPL()->addTemplatePath(PACKAGE_ID, ULTIMATE_DIR.'acp/templates/');
		} else {
			WCF::getTPL()->addTemplatePath(PACKAGE_ID, ULTIMATE_DIR.'templates/');
		}
		WCF::getTPL()->assign('__ultimate', $this);
	}
	
	/**
	 * Inits the custom routes.
	 */
	protected function initRoutes() {
		$pageRoute = new Route('pageRoute-'.PACKAGE_ID);
		$pageRoute->setSchema('/{pageSlug}/', 'Page');
		$pageRoute->setParameterOption('pageSlug', null, '[a-z]+(?:\-{1}[a-z]+)*(?:\/{1}[a-z]+(?:\-{1}[a-z]+)*)*');
		RouteHandler::getInstance()->addRoute($pageRoute);
		
		$categoryRoute = new Route('categoryRoute-'.PACKAGE_ID);
		$categoryRoute->setSchema('/category/{categorySlug}/', 'Category');
		$categoryRoute->setParameterOption('categorySlug', null, '[a-z]+(?:\-{1}[a-z]+)*(?:\/{1}[a-z]+(?:\-{1}[a-z]+)*)*');
		RouteHandler::getInstance()->addRoute($categoryRoute);
		
		$contentRoute = new Route('contentRoute-'.PACKAGE_ID);
		$contentRoute->setSchema('/{date}/{contentSlug}}/', 'Content');
		$contentRoute->setParameterOption('date', null, '2[0-9]{3}\/[0-9]{2}\/[0-9]{2}');
		$contentRoute->setParameterOption('contentSlug', null, '[a-z]+(\-{1}[a-z]+)*');
		RouteHandler::getInstance()->addRoute($contentRoute);
	}
	
	protected function initStyle() {
		
	}
}
