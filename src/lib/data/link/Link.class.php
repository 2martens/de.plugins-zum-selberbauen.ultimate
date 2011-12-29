<?php
namespace ultimate\data\link;
use ultimate\data\AbstractUltimateDatabaseObject;

/**
 * Represents a link.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.link
 * @category Ultimate CMS
 */
class Link extends AbstractUltimateDatabaseObject {
    /**
     * @see wcf\data\DatabaseObject::$databaseTableName
     */
    protected static $databaseTableName = 'link';
    
    /**
     * @see wcf\data\DatabaseObject::$databaseTableIndexIsIdentity
     */
    protected static $databaseTableIndexIsIdentity = false;
    
    /**
     * @see wcf\data\DatabaseObject::$databaseTableIndexName
     */
    protected static $databaseTableIndexName = 'linkID';
    
}
