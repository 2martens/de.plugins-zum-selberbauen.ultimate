<?php
namespace ultimate\data\category;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of categories.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.category
 * @category Ultimate CMS
 */
class CategoryList extends DatabaseObjectList {
    /**
     * @see \wcf\data\DatabaseObjectList::$className
     */
    public $className = '\ultimate\data\category\Category';
}
