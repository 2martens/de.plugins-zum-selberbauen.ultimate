<?php
namespace ultimate\data\link;
use wcf\system\exception\ValidateActionException;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\database\util\PreparedStatementConditionBuilder;

/**
 * Execute link-related actions.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.cms
 * @subpackage data.link
 * @category Ultimate CMS
 */
class LinkAction extends AbstractDatabaseObjectAction {
    /**
     * @see wcf\data\AbstractDatabaseObjectAction::$className
     */
    public $className = 'ultimate\data\link\LinkEditor';
    
	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddLink');
	
	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canDeleteLink');
	
	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canEditLink');
	
	/**
	 * @see wcf\data\AbstractDatabaseObjectAction::validateDelete()
	 * @throws ValidateActionException
	 */
	public function validateDelete() {
	    parent::validateDelete();
	    
	    $linkIDs = array();
	    foreach ($this->objects as $link) $linkIDs[] = $link->linkID;
	    
	    $conditions = new PreparedStatementConditionBuilder();
	    $conditions->add('linkID IN (?)', array($linkIDs));
	    
	    $sql = 'SELECT COUNT(linkID) AS count
	    		FROM ultimate'.ULTIMATE_N.'_config
	    		'.$conditions.'
	    		LIMIT 1';
	    $statement = UltimateCMS::getDB()->prepareStatement($sql);
	    $statement->execute($conditions->getParameters());
	    $row = $statement->fetchArray();
	    if (intval($row['count']) > 0) {
	        throw new ValidateActionException('Some of the links are still connected with configurations.');
	    }
	}
}
