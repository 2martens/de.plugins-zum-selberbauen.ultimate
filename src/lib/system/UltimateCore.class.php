<?php
namespace ultimate\system;
use wcf\system\WCF;
use wcf\system\menu\page\PageMenu;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\request\LinkHandler;
use wcf\system\package\PackageDependencyHandler;
use wcf\system\application\AbstractApplication;

//defines global version
define('ULTIMATE_VERSION', '1.0.0 Alpha 1 (Indigo)');

/**
 * The core class of the Ultimate CMS.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system
 * @category Ultimate CMS
 */
class UltimateCore extends AbstractApplication {
    
    /**
     * Contains the packageID of this package.
     * @var int
     */
    protected $packageID = 0;
    
    /**
     * Calls all init functions of the Ultimate Core class.
     */
    public function __construct() {
        $this->packageID = PackageDependencyHandler::getPackageID('de.plugins-zum-selberbauen.cms');
        
        $this->initTPL();
        PageMenu::getInstance()->setActiveMenuItem('ultimate.header.menu.index');
        WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('ultimate.header.menu.index'), LinkHandler::getInstance()->getLink('Index', array('application' => 'ultimate'))));
    }
    
    /**
     * @see \wcf\system\WCF::initTPL()
     */
    protected function initTPL() {
        if (class_exists('wcf\system\WCFACP')) {
            WCF::getTPL()->addTemplatePath(PACKAGE_ID, ULTIMATE_DIR.'acp/templates/');
        } else {
            WCF::getTPL()->addTemplatePath(PACKAGE_ID, ULTIMATE_DIR.'templates/');
        }
        WCF::getTPL()->assign('__ultimate', $this);
    }
}
