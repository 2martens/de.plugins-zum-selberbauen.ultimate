<?php
namespace ultimate\data\page;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of pages.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.page
 * @category Ultimate CMS
 */
class PageList extends DatabaseObjectList {
    /**
     * @see \wcf\data\DatabaseObjectList::$className
     */
    public $className = '\ultimate\data\page\Page';
}
