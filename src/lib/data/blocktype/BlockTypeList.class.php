<?php
namespace ultimate\data\blocktype;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of blockTypes.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimateCore
 * @subpackage data.ultimate.blockType
 * @category Ultimate CMS
 */
class BlockTypeList extends DatabaseObjectList {
    /**
     * @see \wcf\data\DatabaseObjectList::$className
     */
    public $className = '\ultimate\data\blocktype\BlockType';
}
