<?php
namespace ultimate\data;
use wcf\data\DatabaseObject;

/**
 * Every Ultimate data class should extend this class.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data
 * @category Ultimate CMS
 */
abstract class AbstractUltimateDatabaseObject extends DatabaseObject {
    
	/**
     * @see	wcf\data\IStorableObject::getDatabaseTableName()
     */
    public static function getDatabaseTableName() {
        return 'ultimate'.ULTIMATE_N.'_'.static::$databaseTableName;
    }
}
