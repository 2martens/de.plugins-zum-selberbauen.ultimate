<?php
namespace ultimate\data\content;
use wcf\system\exception\ValidateActionException;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\database\util\PreparedStatementConditionBuilder;

/**
 * Execute content-related functions.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.cms
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
	    
	    $contentIDs = array();
	    foreach ($this->objects as $content) $contentIDs[] = $content->contentID;
	    
	    $conditions = new PreparedStatementConditionBuilder();
	    $conditions->add('contentID IN (?)', array($contentIDs));
	    
	    $sql = 'SELECT COUNT(contentID) AS count
	    		FROM ultimate'.ULTIMATE_N.'_content_to_links
	    		'.$conditions.'
	    		LIMIT 1';
	    $statement = UltimateCMS::getDB()->prepareStatement($sql);
	    $statement->execute($conditions->getParameters());
	    $row = $statement->fetchArray();
	    if (intval($row['count']) > 0) {
	        throw new ValidateActionException('Some of the contents are still used.');
	    }
	}
}
