<?php
namespace ultimate\data\menu\item;
use ultimate\data\AbstractUltimateProcessibleDatabaseObject;
use ultimate\system\menu\custom\DefaultCustomMenuItemProvider;
use wcf\system\menu\ITreeMenuItem;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Represents a menu item entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.menu.item
 * @category	Ultimate CMS
 */
class MenuItem extends AbstractUltimateProcessibleDatabaseObject implements ITreeMenuItem {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableName
	 */
	protected static $databaseTableName = 'menu_item';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'menuItemID';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.ProcessibleDatabaseObject.html#$processorInterface
	 */
	protected static $processorInterface = '\wcf\system\menu\page\IPageMenuItemProvider';
	
	/**
	 * @return	\ultimate\system\menu\custom\DefaultCustomMenuItemProvider
	 * @see		\wcf\data\ProcessibleDatabaseObject::getProcessor()
	 */
	public function getProcessor() {
		if (parent::getProcessor() === null) {
			$this->processor = new DefaultCustomMenuItemProvider($this);
		}
		
		return $this->processor;
	}
	
	/**
	 * Returns the name of this menu item.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->get($this->menuItemName);
	}
	
	/**
	 * Returns all child items.
	 * 
	 * @return	\ultimate\data\menu\item\MenuItem[]
	 */
	public function getChildItems() {
		$sql = 'SELECT	*
		        FROM    '.self::getDatabaseTableName().'
		        WHERE   menuItemParent = ?
		        AND     menuID         = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->menuItemName, $this->menuID));
		
		$childItems = array();
		while ($menuItem = $statement->fetchObject(get_class($this))) {
			$childItems[$menuItem->menuItemID] = $menuItem;
		}
		return $childItems;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.menu.ITreeMenuItem.html#getLink
	 */
	public function getLink() {
		$parameters = array();
		$parameters['abbreviation'] = 'ultimate';
		return LinkHandler::getInstance()->getLink(null, $parameters, $this->menuItemLink);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#handleData
	 */
	protected function handleData($data) {
		$data['menuItemID'] = intval($data['menuItemID']);
		$data['menuID'] = intval($data['menuID']);
		$data['showOrder'] = intval($data['showOrder']);
		$data['isDisabled'] = (boolean) intval($data['isDisabled']);
		parent::handleData($data);
		$this->data['childItems'] = $this->getChildItems();
	}
}
