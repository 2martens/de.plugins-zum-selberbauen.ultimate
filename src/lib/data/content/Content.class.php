<?php
namespace ultimate\data\content;
use ultimate\data\AbstractUltimateDatabaseObject;

/**
 * Represents a content entry.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.cms
 * @subpackage data.content
 * @category Ultimate CMS
 */
class Content extends AbstractUltimateDatabaseObject {
    /**
     * @see wcf\data\DatabaseObject::$databaseTableName
     */
    protected static $databaseTableName = 'content';
    
    /**
     * @see wcf\data\DatabaseObject::$databaseTableIndexIsIdentity
     */
    protected static $databaseTableIndexIsIdentity = false;
    
    /**
     * @see wcf\data\DatabaseObject::$databaseTableIndexName
     */
    protected static $databaseTableIndexName = 'contentID';
    
    
}
