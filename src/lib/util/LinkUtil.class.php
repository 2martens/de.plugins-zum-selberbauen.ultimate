<?php
namespace ultimate\util;
use ultimate\system\UltimateCore;

/**
 * Provides some useful functions for Links.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage util
 * @category Ultimate CMS
 */
class LinkUtil {
    
    /**
     * Contains the database table.
     * @var string
     */
    protected static $databaseTable = 'link';
    
    /**
     * Checks whether the given slug is available or not.
     *
     * @param string $slug
     * @return boolean
     */
    public static function isAvailableSlug($slug) {
        
        $sql = 'SELECT COUNT(linkSlug) as count
        		FROM ultimate'.ULTIMATE_N.'_'.self::$databaseTable.'
        		WHERE linkSlug = ?';
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array($slug));
        
        $row = $statement->fetchArray();
        return ($row['count'] == 0);
    }
}
