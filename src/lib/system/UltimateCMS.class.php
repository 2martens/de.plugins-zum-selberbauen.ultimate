<?php
namespace ultimate\system;
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
class UltimateCMS extends AbstractApplication {
    
    /**
     * Contains a dispatcher object.
     * @var Dispatcher
     */
    protected static $dispatcher = null;
    
    /**
     * Calls all init functions of the Ultimate CMS class.
     */
    public function __construct() {
        
        $this->initDispatcher();
    }
    
    /**
     * Inits the dispatcher.
     */
    protected function initDispatcher() {
        self::$dispatcher = new Dispatcher();
    }
    
    /**
     * Returns the dispatcher object.
     * @return Dispatcher
     */
    public static function getDispatcher() {
        return self::$dispatcher;
    }
}
