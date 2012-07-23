<?php
namespace ultimate\data\content;
use ultimate\data\category\Category;
use ultimate\data\AbstractUltimateDatabaseObject;
use ultimate\system\UltimateCore;
use wcf\data\user\User;

/**
 * Represents a content entry.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.content
 * @category Ultimate CMS
 */
class Content extends AbstractUltimateDatabaseObject {
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableName
     */
    protected static $databaseTableName = 'content';
    
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableIndexIsIdentity
     */
    protected static $databaseTableIndexIsIdentity = true;
    
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableIndexName
     */
    protected static $databaseTableIndexName = 'contentID';
    
    /**
     * Contains the content to category database table name.
     * @var string
     */
    protected $contentCategoryTable = 'content_to_category';
    
    /**
     * Returns the categories associated with this content.
     *
     * @return array<ultimate\data\category\Category>
     */
    public function getCategories() {
        $sql = 'SELECT categoryID
        		FROM ultimate'.ULTIMATE_N.'_'.$this->contentCategoryTable.'
        		WHERE contentID = ?';
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array($this->contentID));
        $categories = array();
        while ($row = $statement->fetchArray()) {
            $categories[$row['categoryID']] = new Category($row['categoryID']);
        }
        return $categories;
    }
    
    /**
     * Returns the title of this content.
     *
     * @return string
     */
    public function __toString() {
        return $this->contentTitle;
    }
    
    /**
     * @see \wcf\data\DatabaseObject::handleData()
     */
    protected function handleData($data) {
        $authorID = intval($data['authorID']);
        $data['author'] = new User($authorID);
        parent::handleData($data);
    }
    
    
}
