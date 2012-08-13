<?php
namespace ultimate\data\menu;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes menu-related functions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.menu
 * @category	Ultimate CMS
 */
class MenuAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	public $className = '\ultimate\data\menu\MenuEditor';
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddMenu');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	*/
	protected $permissionsDelete = array('admin.content.ultimate.canDeleteMenu');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	*/
	protected $permissionsUpdate = array('admin.content.ultimate.canEditMenu');
}
