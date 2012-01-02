<?php
namespace ultimate\data\link;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of links.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.link
 * @category Ultimate CMS
 */
class LinkList extends DatabaseObjectList {
    /**
     * @see wcf\data\DatabaseObjectList::$className
     */
    public $className = 'ultimate\data\link\Link';
}
