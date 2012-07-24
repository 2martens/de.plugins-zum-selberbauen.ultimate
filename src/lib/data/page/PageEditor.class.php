<?php
namespace ultimate\data\page;
use ultimate\system\UltimateCore;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\CacheHandler;

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
     * Adds the specified content to this page.
     *
     * @param int     $contentID
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
