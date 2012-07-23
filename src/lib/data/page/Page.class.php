<?php
namespace ultimate\data\page;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\data\user\User;

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
class Link extends AbstractUltimateDatabaseObject {
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
     * Returns the title of this page.
     *
     * @return string
     */
    public function __toString() {
        return $this->pageTitle;
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
