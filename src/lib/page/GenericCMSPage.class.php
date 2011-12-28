<?php
namespace ultimate\page;
use ultimate\system\UltimateCMS;
use wcf\page\AbstractPage;

/**
 * Shows a page with the specified template.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.cms
 * @subpackage page
 * @category Ultimate CMS
 */
class GenericCMSPage extends AbstractPage {
    
    /**
     * @see wcf\page\AbstractPage::$neededModules
     */
    public $neededModules = array(
        'module_ultimateFrontend'
    );
    
    /**
     * @see wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'user.content.ultimate.canUseFrontend'
    );
    
    /**
     * @see wcf\page\AbstractPage::$templateName
     */
    public $templateName = '';
    
    /**
     * Contains an array with the outputs of the "sub-templates".
     * @var array
     */
    protected $content = array();
    
    /**
     * Creates a new GenericCMSPage object.
     * @param array $callData
     */
    public function __construct(array $callData) {
        $this->templateName = $callData['templateName'];
        $this->content = $callData['content'];
        parent::__construct();
    }
    
    /**
     * @see wcf\page\AbstractPage::assignVariables()
     */
    public function assignVariables() {
        foreach ($this->content as $id => $output) {
            UltimateCMS::getTPL()->assign($id, $output);
        }
    }
}
