<?php
namespace ultimate\data\config;
use wcf\data\DatabaseObjectEditor;

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
class ConfigEditor extends DatabaseObjectEditor {
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
}
