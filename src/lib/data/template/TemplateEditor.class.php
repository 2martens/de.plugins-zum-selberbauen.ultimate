<?php
namespace ultimate\data\template;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit templates.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimateCore
 * @subpackage data.ultimate.template
 * @category Ultimate CMS
 */
class TemplateEditor extends DatabaseObjectEditor {
    /**
     * @see \wcf\data\DatabaseObjectDecorator::$baseClass
     */
    protected static $baseClass = '\ultimate\data\template\Template';
    
    /**
     * @see \wcf\data\IEditableCachedObject::resetCache()
     */
    public function resetCache() {
        CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.template.php');
    }
}
