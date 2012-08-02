<?php
namespace ultimate\data\page;
use ultimate\system\UltimateCore;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\CacheHandler;
use wcf\system\clipboard\ClipboardHandler;

/**
 * Provides functions to edit pages.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.page
 * @category Ultimate CMS
 */
class PageEditor extends DatabaseObjectEditor implements IEditableCachedObject {
    /**
     * @see \wcf\data\DatabaseObjectDecorator::$baseClass
     */
    protected static $baseClass = '\ultimate\data\page\Page';
    
    /**
     * @see	\wcf\data\IEditableObject::deleteAll()
     */
    public static function deleteAll(array $objectIDs = array()) {
        // unmark contents
        ClipboardHandler::getInstance()->unmark($objectIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.page'));
    
        return parent::deleteAll($objectIDs);
    }
    
    /**
     * Adds new groups to this page.
     * 
     * @param array   $groupIDs
     * @param boolean $replaceOldGroups
     */
    public function addGroups(array $groupIDs, $deleteOldGroups = true) {
        if ($deleteOldGroups) {
            $sql = 'DELETE FROM ultimate'.ULTIMATE_N.'_user_group_to_page
                    WHERE       pageID      = ?';
            /* @var $statement \wcf\system\database\statement\PreparedStatement */
            $statement = UltimateCore::getDB()->prepareStatement($sql);
            $statement->execute(array(
                    $this->object->__get('pageID')
            ));
        } 
        $sql = 'INSERT INTO ultimate'.ULTIMATE_N.'_user_group_to_page
                (userGroupID, pageID)
                VALUES
                (?, ?)';
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        UltimateCore::getDB()->beginTransaction();
        foreach ($groupIDs as $groupID) {
            $statement->executeUnbuffered(array(
                $groupID, 
                $this->object->__get('pageID')
            ));
        }
        UltimateCore::getDB()->commitTransaction();
    }
    
    /**
     * Adds the specified content to this page.
     *
     * @param integer $contentID
     * @param boolean $replaceOldContent
     */
    public function addContent($contentID, $replaceOldContent = true) {
        if ($replaceOldContent) {
            $sql = 'UPDATE ultimate'.ULTIMATE_N.'_content_to_page
                    SET    contentID = ?
                    WHERE  pageID    = ?';
            $statement = UltimateCore::getDB()->prepareStatement($sql);
            $statement->execute(array(
                $contentID,
                $this->pageID
            ));
        }
        else {
            $sql = 'INSERT INTO ultimate'.ULTIMATE_N.'_content_to_page
                    (contentID, pageID)
                    VALUES
                    (?, ?)';
            $statement = UltimateCore::getDB()->prepareStatement($sql);
            $statement->execute(array(
                $contentID,
                $this->pageID
            ));
        }
    }
    
	/**
     * @see \wcf\data\IEditableCachedObject::resetCache()
     */
    public static function resetCache() {
        CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.page.php');
        CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.content-to-page.php');
    }
}
