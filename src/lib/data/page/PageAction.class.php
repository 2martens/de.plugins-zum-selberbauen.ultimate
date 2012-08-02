<?php
namespace ultimate\data\page;
use wcf\util\ArrayUtil;

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
class PageAction extends AbstractDatabaseObjectAction {
    /**
     * @see \wcf\data\AbstractDatabaseObjectAction::$className
     */
    public $className = 'ultimate\data\page\PageEditor';
    
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddPage');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canDeletePage');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canEditPage');
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::create()
	 */
	public function create() {
	    $page = parent::create();
	    $pageEditor = new PageEditor($page);
	    
	    // connect with content
	    $contentID = (isset($this->parameters['contentID'])) ? intval($this->parameters['contentID']) : 0;
	    $pageEditor->addContent($contentID, false);
	    
	    // connect with userGroups
	    $groupIDs = (isset($this->parameters['userGroupIDs'])) ? ArrayUtil::toIntegerArray($this->parameters['userGroupIDs']) : array();
	    if (count($groupIDs)) {
	        $pageEditor->addGroups($groupIDs, false);
	    }
	    return $page;
	}
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::update()
	 */
	public function update() {
	    if (isset($this->parameters['data'])) {
	        parent::update();
	    }
	    else {
	        if (!count($this->objects)) {
	            $this->readObjects();
	        }
	    }
	    
	    $contentID = (isset($this->parameters['contentID'])) ? intval($this->parameters['contentID']) : 0;
	    $groupIDs = (isset($this->parameters['userGroupIDs'])) ? ArrayUtil::toIntegerArray($this->parameters['userGroupIDs']) : array();
	     
	    foreach ($this->objects as $pageEditor) {
	        /* @var $pageEditor \ultimate\data\page\PageEditor */
	        if ($contentID) {
	            $pageEditor->addContent($contentID);
	        }
	        
	        if (count($groupIDs)) {
	            $pageEditor->addGroups($groupIDs);
	        }
	    }
	}
}
