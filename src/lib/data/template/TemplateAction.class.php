<?php
namespace ultimate\data\template;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes template-related actions.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimateCore
 * @subpackage data.ultimate.template
 * @category Ultimate CMS
 */
class TemplateAction extends AbstractDatabaseObjectAction {
    /**
     * @see \wcf\data\AbstractDatabaseObjectAction::$className
     */
    public $className = '\ultimate\data\template\TemplateEditor';
}
