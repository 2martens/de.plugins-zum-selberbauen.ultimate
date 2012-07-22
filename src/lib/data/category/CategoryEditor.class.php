<?php
namespace ultimate\data\category;
use ultimate\system\UltimateCore;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\CacheHandler;

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
	
		return parent::deleteAll($objectIDs);
	}
	
	/**
	 * @see \wcf\data\IEditableCachedObject::resetCache()
	 */
	public function resetCache() {
	    CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.category.php');
	}
}
