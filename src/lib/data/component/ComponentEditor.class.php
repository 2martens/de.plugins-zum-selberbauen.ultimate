<?php
namespace ultimate\data\component;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit components.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.component
 * @category Ultimate CMS
 */
class ComponentEditor extends DatabaseObjectEditor {
    /**
     * @see \wcf\data\DatabaseObjectDecorator::$baseClass
     */
    protected static $baseClass = '\ultimate\data\component\Component';
}
