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
        		FROM   ultimate'.ULTIMATE_N.'_'.$this->contentCategoryTable.'
        		WHERE  categoryID = ?';
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array($this->categoryID));
        $contents = array();
        while ($row = $statement->fetchArray()) {
            $contents[$row['contentID']] = new Content($row['contentID']);
        }
        return $contents;
    }
    
    /**
     * Returns all child categories of this category.
     *
     * @return array<ultimate\data\category\Category>
     */
    public function getChildCategories() {
        $sql = 'SELECT categoryID
                FROM   ultimate'.ULTIMATE_N.'_'.self::$databaseTableName.'
                WHERE  parentCategory = ?';
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array($this->categoryID));
        $categories = array();
        while ($row = $statement->fetchArray()) {
            $categories[$row['categoryID']] = new Category($row['categoryID']);
        }
        return $categories;
    }
    
    /**
     * Returns the title of this category.
     *
     * @return string
     */
    public function __toString() {
        return UltimateCore::getLanguage()->get($this->categoryTitle);
    }
    

    /**
     * @see \wcf\data\DatabaseObject::handleData()
     */
    protected function handleData($data) {
        $contents = $this->getContents();
        $childCategories = $this->getChildCategories();
        $data['contents'] = $contents;
        $data['childCategories'] = $childCategories;
        parent::handleData($data);
    }
}
