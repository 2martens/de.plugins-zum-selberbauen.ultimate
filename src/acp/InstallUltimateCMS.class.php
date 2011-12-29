<?php
namespace ultimate\acp;
use wcf\system\io\File;
use wcf\system\WCF;
use wcf\system\cache\CacheHandler;

/**
 * Does some important stuff.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
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
    
    /**
     * Creates a htaccess file.
     */
    protected function install() {
        $cache = 'domain-paths';
        $file = WCF_DIR.'cache/cache.'.$cache.'.php';
        $className = 'ultimate\system\cache\builder\ApplicationDomainPathCacheBuilder';
        CacheHandler::getInstance()->addResource($cache, $file, $className);
        
        require_once(dirname(dirname(__FILE__)).'/config.inc.php');
        $domainPaths = CacheHandler::getInstance()->get($cache, 'paths');
        $domainPath = $domainPaths[PACKAGE_ID];

        WCF::getTPL()->addTemplatePath($packageID, ULTIMATE_DIR.'acp/templates/');
        
        WCF::getTPL()->assign('relDir', $domainPath);
        $output = WCF::getTPL()->fetch('htaccess');
        $file = new File(ULTIMATE_DIR.'.htaccess');
        $file->write($output);
        $file->close();
    }
}
new InstallUltimateCMS();
