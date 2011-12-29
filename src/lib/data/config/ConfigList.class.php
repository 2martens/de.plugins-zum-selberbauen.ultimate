<?php
namespace ultimate\data\config;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of configs.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.config
 * @category Ultimate CMS
 */
class ConfigList extends DatabaseObjectList {
    /**
     * @see wcf\data\DatabaseObjectList::$className
     */
    public $className = 'ultimate\data\config\Config';
}
