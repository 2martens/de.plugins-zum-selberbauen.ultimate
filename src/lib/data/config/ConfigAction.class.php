<?php
namespace ultimate\data\config;
use ultimate\data\link\LinkList;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes config-related actions.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.config
 * @category Ultimate CMS
 */
class ConfigAction extends AbstractDatabaseObjectAction {
    /**
     * @see wcf\data\AbstractDatabaseObjectAction::$className
     */
    public $className = 'ultimate\data\config\ConfigEditor';
    
	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddConfig');
	
	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canDeleteConfig');
	
	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canEditConfig');
	
	/**
	 * @see wcf\data\AbstractDatabaseObjectAction::validateDelete()
	 * @throws ValidateActionException
	 */
	public function validateDelete() {
	    parent::validateDelete();
	    
	    $linkList = new LinkList();
	    $objects = $linkList->getObjects();
	    foreach ($objects as $link) {
	        if (!in_array($link->configID, $this->objectIDs)) continue;
	        
	        throw new ValidateActionException('The config with the ID '.(string) $link->configID.' is still used with some links.');
	    }
	}
}
