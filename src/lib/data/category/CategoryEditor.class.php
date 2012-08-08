<?php
namespace ultimate\data\category;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\CacheHandler;
use wcf\system\clipboard\ClipboardHandler;

/**
 * Provides functions to edit categories.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.category
 * @category Ultimate CMS
 */
class CategoryEditor extends DatabaseObjectEditor implements IEditableCachedObject {
    /**
     * @see \wcf\data\DatabaseObjectDecorator::$baseClass
     */
    protected static $baseClass = '\ultimate\data\category\Category';
    
    /**
	 * @see	\wcf\data\IEditableObject::deleteAll()
	 */
	public static function deleteAll(array $objectIDs = array()) {
		// unmark contents
		ClipboardHandler::getInstance()->unmark($objectIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.category'));
	
		// delete language items
		$sql = 'DELETE FROM wcf'.WCF_N.'_language_item
		        WHERE       languageID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		
		WCF::getDB()->beginTransaction();
		foreach ($objectIDs as $objectID) {
		    $statement->executeUnbuffered(array('ultimate.category.'.$objectID.'.%'));
		}
		WCF::getDB()->commitTransaction();
		
		return parent::deleteAll($objectIDs);
	}
	
	/**
	 * @see \wcf\data\IEditableCachedObject::resetCache()
	 */
	public static function resetCache() {
	    CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.category.php');
	}
}
