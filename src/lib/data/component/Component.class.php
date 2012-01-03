<?php
namespace ultimate\data\component;
use ultimate\data\AbstractUltimateDatabaseObject;

/**
 * Represents a component entry.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.component
 * @category Ultimate CMS
 */
class Component extends AbstractUltimateDatabaseObject {
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableName
     */
    protected static $databaseTableName = 'component';
    
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableIndexIsIdentity
     */
    protected static $databaseTableIndexIsIdentity = false;
    
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableIndexName
     */
    protected static $databaseTableIndexName = 'componentID';
}
