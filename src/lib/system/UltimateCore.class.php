<?php
namespace ultimate\system;
use wcf\system\package\PackageDependencyHandler;

use ultimate\system\Dispatcher;
use wcf\system\application\AbstractApplication;

//defines global version
define('ULTIMATE_VERSION', '1.0.0 Alpha 1 (Indigo)');

/**
 * The core class of the Ultimate CMS.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.cms
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
     * @see wcf\system\WCF::initTPL()
     */
    protected function initTPL() {
        self::getTPL()->addTemplatePath($this->packageID, ULTIMATE_DIR.'templates/');
        self::getTPL()->assign('__ultimate', $this);
    }
}
