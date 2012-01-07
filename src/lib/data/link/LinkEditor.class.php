<?php
namespace ultimate\data\link;
use wcf\data\IEditableCachedObject;
use wcf\data\DatabaseObjectEditor;
use wcf\system\cache\CacheHandler;

/**
 * Provides functions to edit links.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.link
 * @category Ultimate CMS
 */
class LinkEditor extends DatabaseObjectEditor implements IEditableCachedObject {
    /**
     * @see \wcf\data\DatabaseObjectDecorator::$baseClass
     */
    protected static $baseClass = '\ultimate\data\link\Link';
    
	/**
     * @see \wcf\data\IEditableObject::update()
     */
    public function update(array $parameters = array()) {
        parent::update($parameters);
        self::resetCache();
    }
    
	/**
     * @see \wcf\data\IEditableObject::create()
     */
    public static function create(array $parameters = array()) {
        $result = parent::create($parameters);
        self::resetCache();
        return $result;
    }
    
    /**
     * @see \wcf\data\IEditableObject::deleteAll()
     */
    public static function deleteAll(array $objectIDs = array()) {
        $result = parent::deleteAll($objectIDs);
        self::resetCache();
        return $result;
    }
    
	/**
     * @see \wcf\data\IEditableCachedObject::resetCache()
     */
    public static function resetCache() {
        $cache = 'ultimate-links-'.PACKAGE_ID;
        try {
            CacheHandler::getInstance()->clearResource($cache);
        }
        catch (SystemException $e) {
            //does nothing, because cache resource doesn't exist
        }
    }
}
