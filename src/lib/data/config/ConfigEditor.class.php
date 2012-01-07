<?php
namespace ultimate\data\config;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\CacheHandler;

/**
 * Provides functions to edit configs.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.config
 * @category Ultimate CMS
 */
class ConfigEditor extends DatabaseObjectEditor implements IEditableCachedObject {
    /**
     * @see \wcf\data\DatabaseObjectDecorator::$baseClass
     */
    protected static $baseClass = '\ultimate\data\config\Config';
    
    /**
     * @see \wcf\data\DatabaseObjectEditor::delete()
     */
    public function delete() {
        @unlink(ULTIMATE_DIR.'templates/'.$this->__get('templateName').'.tpl');
        parent::delete();
    }
        
    /**
     * @see \wcf\data\IEditableCachedObject::resetCache()
     */
    public static function resetCache() {
        CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.ultimate-links-*');
    }
}
