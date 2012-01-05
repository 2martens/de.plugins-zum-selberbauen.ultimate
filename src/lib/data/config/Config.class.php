<?php
namespace ultimate\data\config;
use ultimate\data\AbstractUltimateDatabaseObject;

/**
 * Represents a link configuration.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.config
 * @category Ultimate CMS
 */
class Config extends AbstractUltimateDatabaseObject {
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableName
     */
    protected static $databaseTableName = 'config';
    
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableIndexIsIdentity
     */
    protected static $databaseTableIndexIsIdentity = true;
    
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableIndexName
     */
    protected static $databaseTableIndexName = 'configID';
}
