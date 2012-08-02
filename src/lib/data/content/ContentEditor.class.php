<?php
namespace ultimate\data\content;
use ultimate\system\UltimateCore;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\CacheHandler;
use wcf\system\clipboard\ClipboardHandler;

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
class ContentEditor extends DatabaseObjectEditor implements IEditableCachedObject {
    /**
     * @see \wcf\data\DatabaseObjectDecorator::$baseClass
     */
    protected static $baseClass = '\ultimate\data\content\Content';
    
    /**
	 * @see	\wcf\data\IEditableObject::deleteAll()
	 */
	public static function deleteAll(array $objectIDs = array()) {
		// unmark contents
		ClipboardHandler::getInstance()->unmark($objectIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.content'));
	
		return parent::deleteAll($objectIDs);
	}
	
	/**
	 * Adds new groups to this page.
	 *
	 * @param array   $groupIDs
	 * @param boolean $replaceOldGroups
	 */
	public function addGroups(array $groupIDs, $replaceOldGroups = true) {
	    if ($replaceOldGroups) {
	        $oldGroups = $this->object->__get('groups');
	        $deleteGroupIDs = array();
	
	        // delete groups which are not anymore needed
	        foreach ($oldGroups as $groupID => $group) {
	            if (in_array($groupID, $groupIDs)) continue;
	            $deleteGroupIDs[] = $groupID;
	            unset($oldGroups[$groupID]);
	        }
	
	        // remove already existing groups from groupIDs array
	        foreach ($groupIDs as $key => $groupID) {
	            if (array_key_exists($groupID, $oldGroups)) {
	                unset($groupIDs[$key]);
	            }
	        }
	
	        $sql = 'DELETE FROM ultimate'.ULTIMATE_N.'_user_group_to_content
                    WHERE       contentID   = ?
                    AND         userGroupID = ?';
	        /* @var $statement \wcf\system\database\statement\PreparedStatement */
	        $statement = UltimateCore::getDB()->prepareStatement($sql);
	        UltimateCore::getDB()->beginTransaction();
	        foreach ($deleteGroupIDs as $groupID) {
	            $statement->executeUnbuffered(array(
	                $this->object->__get('contentID'),
	                $groupID
	            ));
	        }
	        UltimateCore::getDB()->commitTransaction();
	    }
	    $sql = 'INSERT INTO ultimate'.ULTIMATE_N.'_user_group_to_content
                (userGroupID, pageID)
                VALUES
                (?, ?)';
	    $statement = UltimateCore::getDB()->prepareStatement($sql);
	    UltimateCore::getDB()->beginTransaction();
	    foreach ($groupIDs as $groupID) {
	        $statement->executeUnbuffered(array(
	            $groupID,
	            $this->object->__get('contentID')
	        ));
	    }
	    UltimateCore::getDB()->commitTransaction();
	}
    
    /**
     * Adds the content to the specified categories.
     *
     * @param array $categoryIDs
     * @param boolean $deleteOldCategories
     */
    public function addToCategories(array $categoryIDs, $deleteOldCategories = true) {
        // remove old categores
        if ($deleteOldCategories) {
            $sql = "DELETE FROM	ultimate".ULTIMATE_N."_content_to_category
                    WHERE       contentID = ?";
            $statement = UltimateCore::getDB()->prepareStatement($sql);
            $statement->execute(array(
                $this->object->__get('contentID')
            ));
        }
        
        // insert new categories
        if (count($categoryIDs) > 0) {
            $sql = "INSERT INTO	ultimate".ULTIMATE_N."_content_to_category
                    (contentID, categoryID)
                    VALUES      (?, ?)";
            $statement = UltimateCore::getDB()->prepareStatement($sql);
            UltimateCore::getDB()->beginTransaction();
            foreach ($categoryIDs as $categoryID) {
                $statement->executeUnbuffered(array(
                    $this->object->__get('contentID'), 
                    $categoryID
                ));
            }
            UltimateCore::getDB()->commitTransaction();
        }
    }
    
    /**
     * Adds the content to the specified category.
     *
     * @param int $categoryID
     */
    public function addToCategory($categoryID) {
        $sql = "SELECT   COUNT(*) AS count
                FROM     ultimate".ULTIMATE_N."_content_to_category
                WHERE    contentID = ?
				AND      categoryID = ?";
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array(
            $this->contentID,
            $categoryID
        ));
        $row = $statement->fetchArray();
    
        if (!$row['count']) {
            $sql = "INSERT INTO	ultimate".ULTIMATE_N."_content_to_category
                    (contentID, categoryID)
                    VALUES      (?, ?)";
            $statement = UltimateCore::getDB()->prepareStatement($sql);
            $statement->execute(array(
                $this->object->__get('contentID'), 
                $categoryID
            ));
        }
    }
    
    /**
     * Removes the content from the specified category.
     *
     * @param integer $categoryID
     */
    public function removeFromCategory($categoryID) {
        $sql = "DELETE FROM	ultimate".ULTIMATE_N."_content_to_category
                WHERE       contentID = ?
                AND         categoryID = ?";
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute(array(
            $this->object->__get('contentID'), 
            $categoryID
        ));
    }
    
    /**
     * Removes the content from multiple categories.
     *
     * @param array $categoryIDs
     */
    public function removeFromCategories(array $categoryIDs) {
        $sql = "DELETE FROM	ultimate".ULTIMATE_N."_content_to_category
                WHERE       contentID = ?
                AND         categoryID = ?";
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        UltimateCore::getDB()->beginTransaction();
        foreach ($categoryIDs as $categoryID) {
            $statement->executeUnbuffered(array(
                $this->object->__get('contentID'),
                $categoryID
            ));
        }
        UltimateCore::getDB()->commitTransaction();
    }
    
    /**
    * Adds new groups to this page.
    *
    * @param array   $groupIDs
    * @param boolean $replaceOldGroups
    */
    public function addGroups(array $groupIDs, $deleteOldGroups = true) {
        if ($deleteOldGroups) {
            $sql = 'DELETE FROM ultimate'.ULTIMATE_N.'_user_group_to_content
                    WHERE       contentID      = ?';
            $statement = UltimateCore::getDB()->prepareStatement($sql);
            $statement->execute(array(
                $this->object->__get('contentID')
            ));
        }
        $sql = 'INSERT INTO ultimate'.ULTIMATE_N.'_user_group_to_content
                (userGroupID, contentID)
                VALUES
                (?, ?)';
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        UltimateCore::getDB()->beginTransaction();
        foreach ($groupIDs as $groupID) {
            $statement->executeUnbuffered(array(
                $groupID,
                $this->object->__get('contentID')
            ));
        }
        UltimateCore::getDB()->commitTransaction();
    }
    
    /**
     * @see \wcf\data\IEditableCachedObject::resetCache()
     */
    public static function resetCache() {
        CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.content.php');
        CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.content-to-category.php');
    }
}
