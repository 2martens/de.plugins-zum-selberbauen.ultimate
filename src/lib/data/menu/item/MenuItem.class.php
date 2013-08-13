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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
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
use wcf\system\exception\SystemException;
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
 * 
 * @property-read	integer		$menuItemID
 * @property-read	integer		$menuID
 * @property-read	string		$menuItemName
 * @property-read	string		$menuItemParent
 * @property-read	string|NULL	$menuItemController
 * @property-read	string		$menuItemLink
 * @property-read	integer		$showOrder
 * @property-read	string		$permissions
 * @property-read	string		$options
 * @property-read	string		$type ('category', 'content', 'custom', 'page')
 * @property-read	boolean		$isDisabled
 * @property-read	string		$className
 * @property-read	boolean		$isLandingPage
 */
class MenuItem extends AbstractUltimateProcessibleDatabaseObject implements ITreeMenuItem {
	/**
	 * The database table name.
	 * @var	string
	 */
	protected static $databaseTableName = 'menu_item';
	
	/**
	 * If true, the database table index is used as identity.
	 * @var	boolean
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * The database table index name.
	 * @var	string
	 */
	protected static $databaseTableIndexName = 'menuItemID';
	
	/**
	 * The processor interface.
	 * @var string
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
	 * Returns the processor.
	 * 
	 * @return	\ultimate\system\menu\custom\DefaultCustomMenuItemProvider
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
	 * Returns the link of this menu item.
	 * 
	 * @return	string
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
		} else if (strpos($menuItemLink, 'http') === 0) {
			return $menuItemLink;
		}
		throw new SystemException('Illegal link saved.');
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
	 * Handles data.
	 * 
	 * @param	array	$data
	 */
	protected function handleData($data) {
		$data['menuItemID'] = intval($data['menuItemID']);
		$data['menuID'] = intval($data['menuID']);
		$data['showOrder'] = intval($data['showOrder']);
		$data['isDisabled'] = (boolean) intval($data['isDisabled']);
		$data['isLandingPage'] = (boolean) intval($data['isLandingPage']);
		parent::handleData($data);
		$this->data['childItems'] = $this->getChildItems();
	}
}
