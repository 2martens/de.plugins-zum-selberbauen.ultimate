<?php
namespace ultimate\data\content;
use ultimate\system\UltimateCMS;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit content.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.content
 * @category Ultimate CMS
 */
class ContentEditor extends DatabaseObjectEditor {
    /**
     * @see wcf\data\DatabaseObjectDecorator::$baseClass
     */
    protected static $baseClass = 'ultimate\data\content\Content';
    
    /**
     * Deletes all connections of given contents with links.
     * @see wcf\data\DatabaseObjectEditor::deleteAll()
     */
    public static function deleteAll(array $objectIDs = array()) {
        $sql = 'DELETE FROM ultimate'.ULTIMATE_N.'_content_to_links
        		WHERE '.static::getDatabaseTableIndexName().' = ?';
        $statement = UltimateCMS::getDB()->prepareStatement($sql);

        UltimateCMS::getDB()->beginTransaction();
        foreach ($objectIDs as $objectID) {
			$statement->executeUnbuffered(array($objectID));
		}
		UltimateCMS::getDB()->commitTransaction();
		
		return DatabaseObjectEditor::deleteAll($objectIDs);
    }
}
