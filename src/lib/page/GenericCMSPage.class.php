<?php
namespace ultimate\page;
use ultimate\system\UltimateCore;
use wcf\page\AbstractPage;

/**
 * Shows a page with the specified template.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage page
 * @category Ultimate CMS
 */
class GenericCMSPage extends AbstractPage {
    
    /**
     * @see \wcf\page\AbstractPage::$neededModules
     */
    public $neededModules = array(
        'MODULE_ULTIMATEFRONTEND'
    );
    
    /**
     * @see \wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'user.ultimate.canUseFrontend'
    );
    
    /**
     * @see \wcf\page\AbstractPage::$templateName
     */
    public $templateName = '';
    
    /**
     * Contains the meta description.
     * @var string
     */
    protected $metaDescription = '';
    
    /**
     * Contains the meta keywords.
     * @var string
     */
    protected $metaKeywords = '';
    
    /**
     * Contains the config title.
     * @var string
     */
    protected $configTitle = '';
    
    /**
     * Creates a new GenericCMSPage object.
     *
     * @param array $callData
     */
    public function __construct(array $callData) {
        $this->templateName = $callData['templateName'];
        $this->configTitle = $callData['configTitle'];
        $this->metaDescription = $callData['metaDescription'];
        $this->metaKeywords = $callData['metaKeywords'];
        if (DEBUG) {
            $this->neededModules = array();
            $this->neededPermissions = array();
        }
        parent::__construct();
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        UltimateCore::getTPL()->assign(array(
            'configTitle' => $this->configTitle,
        	'metaDescription' => $this->metaDescription, //debug purposes
            'metaKeywords' => $this->metaKeywords
        ));
    }
}
