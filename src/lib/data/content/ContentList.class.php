<?php
namespace ultimate\data\content;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of contents
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.cms
 * @subpackage data.content
 * @category Ultimate CMS
 */
class ContentList extends DatabaseObjectList {
    /**
     * @see wcf\data\DatabaseObjectList::$className
     */
    public $className = 'ultimate\data\content\Content';
    
}
