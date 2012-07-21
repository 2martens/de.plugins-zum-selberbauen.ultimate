<?php
namespace ultimate\data\page;
use wcf\system\exception\ValidateActionException;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\database\util\PreparedStatementConditionBuilder;

/**
 * Executes page-related actions.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.page
 * @category Ultimate CMS
 */
class LinkAction extends AbstractDatabaseObjectAction {
    /**
     * @see \wcf\data\AbstractDatabaseObjectAction::$className
     */
    public $className = 'ultimate\data\page\LinkEditor';
    
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddLink');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canDeleteLink');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canEditLink');
}
