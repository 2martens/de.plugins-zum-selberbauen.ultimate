<?php
namespace ultimate\data\category;
use ultimate\system\UltimateCore;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit categories.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.category
 * @category Ultimate CMS
 */
class CategoryEditor extends DatabaseObjectEditor {
    /**
     * @see \wcf\data\DatabaseObjectDecorator::$baseClass
     */
    protected static $baseClass = '\ultimate\data\category\Category';
    
    /**
     * Contains the table which links contents with categories.
     * @var string
     */
    protected static $contentCategoryTable = 'content_to_category';
    
    /**
     * Deletes all connections of given categories with contents.
     * @see \wcf\data\DatabaseObjectEditor::deleteAll()
     */
    public static function deleteAll(array $objectIDs = array()) {
        $affectedCount = DatabaseObjectEditor::deleteAll($objectIDs);
		
		$sql = 'DELETE FROM ultimate'.ULTIMATE_N.'_'.self::$contentCategoryTable.'
				WHERE categoryID = ?';
		$statement = UltimateCore::getDB()->prepareStatement($sql);
		UltimateCore::getDB()->beginTransaction();
		foreach ($objectIDs as $objectID) {
		    $statement->executeUnbuffered(array($objectID));
		}
		UltimateCore::getDB()->commitTransaction();
		return $affectedCount;
    }
}
