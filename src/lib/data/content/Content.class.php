<?php
namespace ultimate\data\content;
use ultimate\data\category\Category;
use ultimate\data\AbstractUltimateDatabaseObject;
use ultimate\system\UltimateCore;
use wcf\data\user\group\UserGroup;
use wcf\data\user\User;
use wcf\util\DateUtil;

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
     * Contains the categories to which this content belongs.
     * @var \ultimate\data\category\Category[]
     */
    public $categories = array();
    
    /**
     * Returns the categories associated with this content.
     *
     * @return \ultimate\data\category\Category[]
     */
    public function getCategories() {
        $sql = 'SELECT categoryID
        		FROM ultimate'.ULTIMATE_N.'_'.$this->contentCategoryTable.'
        		WHERE contentID = ?';
        /* @var $statement \wcf\system\database\statement\PreparedStatement */
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array($this->contentID));
        $categories = array();
        while ($row = $statement->fetchArray()) {
            $categories[$row['categoryID']] = new Category($row['categoryID']);
        }
        return $categories;
    }
    
    /**
     * Returns all user groups associated with this content.
     *
     * @return \wcf\data\user\group\UserGroup[]
     */
    public function getGroups() {
        $sql = 'SELECT    groupID
                FROM      ultimate'.ULTIMATE_N.'_user_group_to_content
                WHERE     contentID = ?';
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array($this->contentID));
    
        $groups = array();
        while ($row = $statement->fetchArray()) {
            $groups[$row['groupID']] = new UserGroup($row['groupID']);
        }
        return $groups;
    }
    
    /**
     * Returns the title of this content.
     *
     * @return string
     */
    public function __toString() {
        return UltimateCore::getLanguage()->get($this->contentTitle);
    }
    
    /**
     * @see \wcf\data\DatabaseObject::handleData()
     */
    protected function handleData($data) {
        $data['contentID'] = intval($data['contentID']);
        $data['authorID'] = intval($data['authorID']);
        $data['author'] = new User($data['authorID']);
        $data['enableSmilies'] = (boolean) intval($data['enableSmilies']);
        $data['enableHtml'] = (boolean) intval($data['enableHtml']);
        $data['enableBBCodes'] = (boolean) intval($data['enableBBCodes']);
        $data['publishDate'] = intval($data['publishDate']);
        $data['publishDateObject'] = DateUtil::getDateTimeByTimestamp($data['publishDate']);
        $data['lastModified'] = intval($data['lastModified']);
        parent::handleData($data);
        $this->data['groups'] = $this->getGroups();
    }
    
    
}
