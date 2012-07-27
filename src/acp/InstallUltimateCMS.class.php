<?php
namespace ultimate\acp;
use wcf\system\cache\CacheHandler;
use wcf\system\io\File;
use wcf\system\WCF;

/**
 * Does some important stuff.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp
 * @category Ultimate CMS
 */
class InstallUltimateCMS {
    
    /**
     * Creates a new InstallUltimateCMS object.
     */
    public function __construct() {
        $this->install();
    }
    // @todo Fixing!!!
    /**
     * Installs important things.
     */
    protected function install() {
        require_once(dirname(dirname(__FILE__)).'/config.inc.php');
        $this->createHtaccess();
    }
    
    /**
     * Creates a htaccess file.
     */
    protected function createHtaccess() {
        $cache = 'application-'.PACKAGE_ID;
        $file = WCF_DIR.'cache/cache.'.$cache.'.php';
        $className = '\wcf\system\cache\builder\ApplicationCacheBuilder';
        CacheHandler::getInstance()->addResource($cache, $file, $className);
        
        $applications = CacheHandler::getInstance()->get($cache, 'application');
        $ourApp = $applications[PACKAGE_ID];
        
        WCF::getTPL()->addTemplatePath(PACKAGE_ID, ULTIMATE_DIR.'acp/templates/');
        
        WCF::getTPL()->assign('relDir', $ourApp->domainPath."\n");
        $output = WCF::getTPL()->fetch('htaccess');
        $file = new File(ULTIMATE_DIR.'.htaccess');
        $file->write($output);
        $file->close();
    }
    
}
new InstallUltimateCMS();
