<?php
namespace ultimate\data\link;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
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
     * @see \wcf\data\IEditableCachedObject::resetCache()
     */
    public static function resetCache() {
        CacheHandler::getInstance()->clearResource(ULTIMATE_DIR.'cache/', 'ultimate-links-*');
    }
}
