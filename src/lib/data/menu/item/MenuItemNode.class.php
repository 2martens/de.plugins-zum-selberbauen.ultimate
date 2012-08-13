<?php
namespace ultimate\data\menu\item;
use ultimate\system\menu\item\MenuItemHandler;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;

/**
 * Represents a menu item node.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.menu.item
 * @category	Ultimate CMS
 */
class MenuItemNode extends DatabaseObjectDecorator implements \RecursiveIterator, \Countable {
	/**
	 * Contains the current index.
	 * @var	integer
	 */
	protected $index = 0;
	
	/**
	 * Contains the child menu item nodes.
	 * @var	\ultimate\data\menu\item\MenuItemNode[]
	 */
	protected $childMenuItems = array();
	
	/**
	 * Indicates if disabled menu items are included.
	 * @var	boolean
	*/
	protected $includeDisabledMenuItems = false;
	
	/**
	 * Contains menu item IDs of excluded menu items.
	 * @var	integer[]
	 */
	protected $excludedMenuItemIDs = array();
	
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = '\ultimate\data\menu\item\MenuItem';
	
	/**
	 * Creates a new MenuItemNode object.
	 * 
	 * @param	\wcf\data\DatabaseObject $object
	 * @param	boolean					 $includeDisabledMenuItems
	 * @param	integer[]				 $excludedMenuItems
	 * @see		\wcf\data\DatabaseObjectDecorator::__construct()
	 */
	public function __construct(DatabaseObject $object, $includeDisabledMenuItems = false, array $excludedMenuItemIDs = array()) {
		parent::__construct($object);
		
		$this->includeDisabledMenuItems = $includeDisabledMenuItems;
		$this->excludedMenuItemIDs = $excludedMenuItemIDs;
		
		$className = get_called_class();
		/* @var $menuItem \ultimate\data\menu\item\MenuItem */
		foreach (MenuItemHandler::getInstance()->getChildMenuItems($this->getDecoratedObject()) as $menuItem) {
			if ($this->fulfillsConditions($menuItem)) {
				$this->childMenuItems[] = new $className($menuItem, $includeDisabledMenuItems, $excludedMenuItemIDs);
			}
		}
	}
	
	/**
	 * @see	\Countable::count()
	 */
	public function count() {
		return count($this->childMenuItems);
	}
	
	/**
	 * @see	\Iterator::current()
	 */
	public function current() {
		return $this->childMenuItems[$this->index];
	}
	
	/**
	 * Returns true if the given menu item fulfills all needed conditions to
	 * be included in the list.
	 * 
	 * @param	\ultimate\data\menu\item\MenuItem $menuItem
	 * @return	boolean
	 */
	public function fulfillsConditions(MenuItem $menuItem) {
		return !in_array($menuItem->__get('menuItemID'), $this->excludedMenuItemIDs) && ($this->includeDisabledMenuItems || !$menuItem->__get('isDisabled'));
	}
	
	/**
	 * @see	\RecursiveIterator::getChildren()
	 */
	public function getChildren() {
		return $this->childMenuItems[$this->index];
	}
	
	/**
	 * @see	\RecursiveIterator::getChildren()
	 */
	public function hasChildren() {
		return !empty($this->childMenuItems);
	}
	
	/**
	 * @see	\Iterator::key()
	 */
	public function key() {
		return $this->index;
	}
	
	/**
	 * @see	\Iterator::next()
	 */
	public function next() {
		$this->index++;
	}
	
	/**
	 * @see	\Iterator::rewind()
	 */
	public function rewind() {
		$this->index = 0;
	}
	
	/**
	 * @see	\Iterator::valid()
	 */
	public function valid() {
		return isset($this->childMenuItems[$this->index]);
	}
}
