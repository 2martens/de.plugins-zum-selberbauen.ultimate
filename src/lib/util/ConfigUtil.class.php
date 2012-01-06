<?php
namespace ultimate\util;
use ultimate\system\UltimateCore;

/**
 * Provides useful functions connected with Configs.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage util
 * @category Ultimate CMS
 */
class ConfigUtil {
    
    /**
     * Contains the database table.
     * @var string
     */
    protected static $databaseTable = 'config';
    
    /**
     * Checks whether the given configTitle is available or not.
     *
     * @param string $title
     * @return boolean
     */
    public static function isAvailableTitle($title) {
        $sql = 'SELECT COUNT(configTitle) AS count
        		FROM ultimate'.ULTIMATE_N.'_'.self::$databaseTable.'
        		WHERE configTitle = ?';
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array($title));
        $row = $statement->fetchArray();
        return ($row['count'] == 0);
    }
}
