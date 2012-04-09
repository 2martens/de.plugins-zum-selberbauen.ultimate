<?php
namespace ultimate\page;
use ultimate\data\config\Config;
use ultimate\page\GenericCMSPage;
use ultimate\system\UltimateCore;
use wcf\page\AbstractPage;
use wcf\system\cache\CacheHandler;

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
class IndexPage extends GenericCMSPage {
    /**
     * Creates a new IndexPage object.
     */
    public function __construct() {
        $configID = ULTIMATE_GENERAL_INDEX_CONFIG;
        $config = new Config($configID);
        $this->configTitle = $config->configTitle;
        $this->metaDescription = $config->metaDescription;
        $this->metaKeywords = $config->metaKeywords;
        $this->templateName = $config->templateName;
        AbstractPage::__construct();
    }
}
