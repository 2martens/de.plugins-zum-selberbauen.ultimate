<?php
namespace ultimate\data\link;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit links.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.cms
 * @subpackage data.link
 * @category Ultimate CMS
 */
class LinkEditor extends DatabaseObjectEditor {
    /**
     * @see wcf\data\DatabaseObjectDecorator::$baseClass
     */
    protected static $baseClass = 'ultimate\data\link\Link';
}
