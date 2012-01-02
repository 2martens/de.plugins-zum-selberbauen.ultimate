<?php
namespace ultimate\data\component;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of components.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.component
 * @category Ultimate CMS
 */
class ComponentList extends DatabaseObjectList {
    /**
     * @see \wcf\data\DatabaseObjectList::$className
     */
    public $className = '\ultimate\data\component\Component';
}
