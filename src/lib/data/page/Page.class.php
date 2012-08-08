<?php
namespace ultimate\data\page;
use ultimate\data\content\Content;
use ultimate\data\AbstractUltimateDatabaseObject;
use ultimate\system\UltimateCore;
use wcf\data\user\group\UserGroup;
use wcf\data\user\User;
use wcf\data\ITitledDatabaseObject;
use wcf\util\DateUtil;

/**
 * Represents a page.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.page
 * @category Ultimate CMS
 */
class Page extends AbstractUltimateDatabaseObject implements ITitledDatabaseObject {
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableName
     */
    protected static $databaseTableName = 'page';
    
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableIndexIsIdentity
     */
    protected static $databaseTableIndexIsIdentity = true;
    
    /**
     * @see \wcf\data\DatabaseObject::$databaseTableIndexName
     */
    protected static $databaseTableIndexName = 'pageID';
    
    /**
     * Contains the content to page database table name.
     * @var string
     */
    protected $contentPageTable = 'content_to_page';
    
    /**
     * Returns the content of this page.
     *
     * @return \ultimate\data\content\Content
     */
    public function getContent() {
        $sql = 'SELECT    contentID
                FROM      ultimate'.ULTIMATE_N.'_'.$this->contentPageTable.'
                WHERE     pageID = ?';
        /* @var $statement \wcf\system\database\statement\PreparedStatement */
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array($this->pageID));
        
        $row = $statement->fetchArray();
        return new Content($row['contentID']);
    }
    
    /**
     * Returns the page title without language interpreting.
     * 
     * To use language interpreting, use magic toString method.
     * 
     * @return string
     */
    public function getTitle() {
        return $this->pageTitle;
    }
    
    /**
     * Returns all child pages of this page.
     *
     * @return \ultimate\data\page\Page[]
     */
    public function getChildPages() {
        $sql = 'SELECT    pageID
                FROM      '.self::getDatabaseTableName().'
                WHERE     pageParent = ?';
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array($this->pageID));
        
        $childPages = array();
        while ($row = $statement->fetchArray()) {
            $childPages[$row['pageID']] = new Page($row['pageID']);
        }
        return $childPages;
    }
    
    /**
     * Returns all user groups associated with this page.
     * 
     * @return \wcf\data\user\group\UserGroup[]
     */
    public function getGroups() {
        $sql = 'SELECT    groupID
                FROM      ultimate'.ULTIMATE_N.'_user_group_to_page
                WHERE     pageID = ?';
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array($this->pageID));
        
        $groups = array();
        while ($row = $statement->fetchArray()) {
            $groups[$row['groupID']] = new UserGroup($row['groupID']);
        }
        return $groups;
    }
    
    /**
     * Returns the title of this page.
     *
     * @return string
     */
    public function __toString() {
        return UltimateCore::getLanguage()->get($this->pageTitle);
    }
    
    /**
     * @see \wcf\data\DatabaseObject::handleData()
     */
    protected function handleData($data) {
        $data['pageID'] = intval($data['pageID']);
        $data['authorID'] = intval($data['authorID']);
        $data['author'] = new User($data['authorID']);
        $data['publishDate'] = intval($data['publishDate']);
        $data['publishDateObject'] = DateUtil::getDateTimeByTimestamp($data['publishDate']);
        $data['lastModified'] = intval($data['lastModified']);
        $data['status'] = intval($data['status']);
        parent::handleData($data);
        $this->data['groups'] = $this->getGroups();
        $this->data['childPages'] = $this->getChildPages();
    }
    
}
