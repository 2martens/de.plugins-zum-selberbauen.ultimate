<?php
namespace ultimate\data\menu;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\system\WCF;

/**
 * Represents a menu entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.menu
 * @category	Ultimate CMS
 */
class Menu extends AbstractUltimateDatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'menu';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'menuID';
	
	/**
	 * Returns the name of this menu.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->get($this->menuName);
	}
	
	/**
	 * @see	\wcf\data\DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		$data['menuID'] = intval($data['menuID']);
		parent::handleData($data);
	}
}
