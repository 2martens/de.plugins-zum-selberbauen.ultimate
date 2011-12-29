<?php
namespace ultimate\data\content;
use ultimate\data\config\ConfigList;

use wcf\system\exception\ValidateActionException;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\database\util\PreparedStatementConditionBuilder;

/**
 * Executes content-related functions.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.content
 * @category Ultimate CMS
 */
class ContentAction extends AbstractDatabaseObjectAction {
    /**
     * @see wcf\data\AbstractDatabaseObjectAction::$className
     */
    public $className = 'ultimate\data\content\ContentEditor';
    
	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddContent');
	
	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canDeleteContent');
	
	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canEditContent');
	
	/**
	 * @see wcf\data\AbstractDatabaseObjectAction::validateDelete()
	 * @throws ValidateActionException
	 */
	public function validateDelete() {
	    parent::validateDelete();
	    
	    $configList = new ConfigList();
	    $objects = $configList->getObjects();
	    foreach ($objects as $config) {
	        $requiredContents = unserialize($config->requiredContents);
	        $flippedArray = array_flip($requiredContents);
	        foreach ($this->objectIDs as $objectID) {
	            if (!in_array($objectID, $flippedArray)) continue;
	            
	            throw new ValidateActionException('The content with the ID '.(string) $objectID.' is still needed by some configs.');
	        }
	    }
	}
}
