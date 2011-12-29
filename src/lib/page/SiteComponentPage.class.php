<?php
namespace ultimate\page;
use ultimate\page\AbstractComponentPage;

/**
 * Fetches a default site component.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage page
 * @category Ultimate CMS
 */
class SiteComponentPage extends AbstractComponentPage {
    /**
     * @see wcf\page\AbstractPage::$templateName
     */
    public $templateName = 'site';
    
}
