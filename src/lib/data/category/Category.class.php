<?php
namespace ultimate\data\category;
use ultimate\data\content\Content;
use ultimate\data\AbstractUltimateDatabaseObject;
use ultimate\system\UltimateCore;

/**
 * Represents a category entry.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.category
 * @category Ultimate CMS
 */
class Category extends AbstractUltimateDatabaseObject {
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableName
     */
    protected static $databaseTableName = 'category';
    
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableIndexIsIdentity
     */
    protected static $databaseTableIndexIsIdentity = true;
    
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableIndexName
     */
    protected static $databaseTableIndexName = 'categoryID';
    
    /**
     * Contains the content to category database table name.
     * @var string
     */
    protected $contentCategoryTable = 'content_to_category';
    
    /**
     * Returns all contents in this category.
     *
     * @return array<ultimate\data\content\Content>
     */
    public function getContents() {
        $sql = 'SELECT contentID
        		FROM ultimate'.ULTIMATE_N.'_'.$this->contentCategoryTable.'
        		WHERE categoryID = ?';
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array($this->categoryID));
        $contents = array();
        while ($row = $statement->fetchArray()) {
            $contents[$row['contentID']] = new Content($row['contentID']);
        }
        return $contents;
    }
}
