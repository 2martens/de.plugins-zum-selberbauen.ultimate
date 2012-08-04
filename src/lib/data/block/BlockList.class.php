<?php
namespace ultimate\data\block;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of blocks.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimateCore
 * @subpackage data.ultimate.block
 * @category Ultimate CMS
 */
class BlockList extends DatabaseObjectList {
    /**
     * @see \wcf\data\DatabaseObjectList::$className
     */
    public $className = '\ultimate\data\block\Block';
}
