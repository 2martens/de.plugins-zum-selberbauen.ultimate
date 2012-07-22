<?php
namespace ultimate\data\content;
use ultimate\data\config\ConfigList;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\cache\CacheHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\ValidateActionException;


/**
 * Executes content-related functions.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.content
 * @category Ultimate CMS
 */
class ContentAction extends AbstractDatabaseObjectAction {
    /**
     * @see \wcf\data\AbstractDatabaseObjectAction::$className
     */
    public $className = '\ultimate\data\content\ContentEditor';
    
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddContent');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canDeleteContent');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canEditContent');
			
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::validateDelete()
	 * @throws ValidateActionException
	 */
	public function validateDelete() {
	    parent::validateDelete();
	    
	    $configList = new ConfigList();
	    $configList->readObjects();
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
	
	/**
	 * Creates new content.
	 *
	 * @return	Content
	 */
	public function create() {
	    $content = parent::create();
	    $contentEditor = new ContentEditor($content);
	
	    // insert categories
	    $categoryIDs = (isset($this->parameters['categories'])) ? $this->parameters['categories'] : array();
	    $contentEditor->addToCategories($categoryIDs, false);
	
	    return $content;
	}
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::update()
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
	
	    $categoryIDs = (isset($this->parameters['categories'])) ? $this->parameters['categories'] : array();
	    
	    foreach ($this->objects as $contentEditor) {
	        if (!empty($categoryIDs)) {
	            $contentEditor->addToCategories($categoryIDs);
	        }
	        	
	        if (!empty($removeGroups)) {
	            $userEditor->removeFromGroups($removeGroups);
	        }
	    }
	}
	
}
