<?php
namespace ultimate\data\content;
use ultimate\system\UltimateCore;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit content.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.content
 * @category Ultimate CMS
 */
class ContentEditor extends DatabaseObjectEditor {
    /**
     * @see \wcf\data\DatabaseObjectDecorator::$baseClass
     */
    protected static $baseClass = '\ultimate\data\content\Content';
    
    /**
     * Contains the table which links contents with categories.
     * @var string
     */
    protected static $contentCategoryTable = 'content_to_category';
    
    /**
     * Deletes all connections of given contents with categories.
     * @see \wcf\data\DatabaseObjectEditor::deleteAll()
     */
    public static function deleteAll(array $objectIDs = array()) {
        //TODO: How to ensure that no contents are still in use?
        /*$sql = 'DELETE FROM ultimate'.ULTIMATE_N.'_content_to_links
        		WHERE '.static::getDatabaseTableIndexName().' = ?';
        $statement = UltimateCore::getDB()->prepareStatement($sql);

        UltimateCore::getDB()->beginTransaction();
        foreach ($objectIDs as $objectID) {
			$statement->executeUnbuffered(array($objectID));
		}
		UltimateCore::getDB()->commitTransaction();*/
		
		$affectedCount = DatabaseObjectEditor::deleteAll($objectIDs);
		
		$sql = 'DELETE FROM ultimate'.ULTIMATE_N.'_'.self::$contentCategoryTable.'
				WHERE contentID = ?';
		$statement = UltimateCore::getDB()->prepareStatement($sql);
		UltimateCore::getDB()->beginTransaction();
		foreach ($objectIDs as $objectID) {
		    $statement->executeUnbuffered(array($objectID));
		}
		UltimateCore::getDB()->commitTransaction();
		return $affectedCount;
    }
    
    /**
     * @see \wcf\data\IEditableObject::create()
     */
    public static function create(array $parameters = array()) {
        $content = parent::create($parameters);
        $sql = 'INSERT INTO ultimate'.ULTIMATE_N.'_'.self::$contentCategoryTable.'
        		(contentID, categoryID)
        			VALUES
        		(?, ?)';
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array(
            $content->contentID,
            $parameters['categoryID']
        ));
    }
    
    /**
     * @see \wcf\data\IEditableObject::update()
     */
    public function update(array $parameters = array()) {
        if (!count($parameters)) return;
        $categoryID = 0;
        if (isset($parameters['categoryID'])) $categoryID = $parameters['categoryID'];
        if ($categoryID) {
            unset($parameters['categoryID']);
            $sql = 'UPDATE ultimate'.ULTIMATE_N.'_'.self::$contentCategoryTable.'
            		SET categoryID = ?
            		WHERE contentID = ?';
            $statement = UltimateCore::getDB()->prepareStatement($sql);
            $statement->execute(array(
                $categoryID,
                $this->__get(static::getDatabaseTableIndexName())
            ));
        }
        parent::update($parameters);
    }
}
