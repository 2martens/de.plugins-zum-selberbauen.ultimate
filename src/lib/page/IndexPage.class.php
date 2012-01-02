<?php
namespace ultimate\page;
use wcf\page\AbstractPage;

/**
 * Shows the index page.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage page
 * @category Ultimate CMS
 */
class IndexPage extends AbstractPage {
    /**
     * @see wcf\page\AbstractPage::$templateName
     */
    public $templateName = 'index';
    
    /**
     * @see wcf\page\AbstractPage::$neededModules
     */
    public $neededModules = array(
        'MODULE_ULTIMATEFRONTEND'
    );
    
    /**
     * @see wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'user.ultimate.canUseFrontend'
    );
}
