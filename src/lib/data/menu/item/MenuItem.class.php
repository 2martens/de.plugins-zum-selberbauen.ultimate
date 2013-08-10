<?php
/**
 * Contains the menuItem data model class.
 * 
 * LICENSE:
 * This file is part of the Ultimate CMS.
 *
 * The Ultimate CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * The Ultimate CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.menu.item
 * @category	Ultimate CMS
 */
namespace ultimate\data\menu\item;
use ultimate\data\AbstractUltimateProcessibleDatabaseObject;
use ultimate\system\menu\custom\DefaultCustomMenuItemProvider;
use wcf\system\menu\ITreeMenuItem;
use wcf\system\request\LinkHandler;
use wcf\system\request\UltimateLinkHandler;
use wcf\system\Regex;
use wcf\system\WCF;

/**
 * Represents a menu item entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
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
	 * application abbreviation
	 * @var	string
	 */
	protected $application = '';
	
	/**
	 * menu item controller
	 * @var	string
	 */
	protected $controller = null;
	
	/**
	 * @return	\ultimate\system\menu\custom\DefaultCustomMenuItemProvider
	 * @see		\wcf\data\ProcessibleDatabaseObject::getProcessor()
	 */
	public function getProcessor() {
		if ($this->processor === null) {
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
		return WCF::getLanguage()->getDynamicVariable($this->menuItemName);
	}
	
	/**
	 * Returns true if current menu item may be set as landing page.
	 *
	 * @return	boolean
	 */
	public function isValidLandingPage() {
		// item must be a top header menu item without parents
		if ($this->parentMenuItem) {
			return false;
		}
	
		// external links are not valid
		if (strpos($menuItemLink, 'http') !== false) {
			return false;
		}
	
		// already is landing page
		if ($this->isLandingPage) {
			return false;
		}
	
		// disabled items cannot be a landing page
		if ($this->isDisabled) {
			return false;
		}
	
		return true;
	}
	
	/**
	 * Returns true if this item can be deleted.
	 *
	 * @return	boolean
	 */
	public function canDelete() {
		$deletable = true;
		$deletable = ($this->isLandingPage ? false : true);
		if ($deletable) {
			$deletable = ($this->menuItemController !== null ? false : true);
		}
		
		return $deletable;
	}
	
	/**
	 * Returns true if this item can be disabled.
	 *
	 * @return	boolean
	 */
	public function canDisable() {
		return ($this->isLandingPage ? false : true);
	}
	
	/**
	 * Returns application abbreviation.
	 *
	 * @return	string
	 */
	public function getApplication() {
		$this->parseController();
	
		return $this->application;
	}
	
	/**
	 * Returns controller name.
	 *
	 * @return	string
	 */
	public function getController() {
		$this->parseController();
	
		return $this->controller;
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
		$parameters['application'] = 'ultimate';
		$parameters['isRaw'] = true;
		$menuItemLink = $this->menuItemLink;
		if (strpos($menuItemLink, 'http') === false) {
			if ($this->menuItemController === null) {
				$menuItemLink = 'index.php/'.$menuItemLink;
				return UltimateLinkHandler::getInstance()->getLink(null, $parameters, $menuItemLink);
			} else {
				// external link
				if (!$this->menuItemController) {
					return WCF::getLanguage()->get($this->menuItemLink);
				}
				$this->parseController();
				return LinkHandler::getInstance()->getLink(
					$this->controller, 
					array(
						'application' => $this->application, 
						'forceFrontend' => true
					), 
					WCF::getLanguage()->get($this->menuItemLink));
			}
		} elseif (strpos($menuItemLink, 'http') === 0) {
			return $menuItemLink;
		}
	}
	
	/**
	 * Parses controller name.
	 */
	protected function parseController() {
		if ($this->controller === null) {
			$this->controller = '';
				
			// resolve application and controller
			if ($this->menuItemController) {
				$parts = explode('\\', $this->menuItemController);
				$this->application = array_shift($parts);
				$menuItemController = array_pop($parts);
	
				// drop controller suffix
				$this->controller = Regex::compile('(Action|Form|Page)$')->replace($menuItemController, '');
			}
		}
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
