<?php
namespace ultimate\data\menu\item;
use ultimate\system\menu\item\MenuItemHandler;
use wcf\system\exception\SystemException;

/**
 * Represents a menu item node list.
 * 
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.menu.item
 * @category Ultimate CMS
 */
class MenuItemNodeList extends \RecursiveIteratorIterator implements \Countable {
    /**
     * Contains the number of (real) menu item nodes in this list.
     * @var	integer
     */
    protected $count = null;

    /**
     * Contains the name of the menuItem node class.
     * @var	string
     */
    protected $nodeClassName = '\ultimate\data\menu\item\MenuItemNode';

    /**
     * Contains the id of the parent menu item.
     * @var	integer
     */
    protected $parentMenuItemID = 0;
    
    /**
     * Contains the menu id.
     * @var integer
     */
    protected $menuID = 0;

    /**
     * Creates a new MenuItemNodeList object.
     *
     * @param    integer   $menuID
     * @param    integer   $parentMenuItemID
     * @param    boolean   $includeDisabledMenuItems
     * @param    integer[] $excludedMenuItemIDs
     */
    public function __construct($menuID = 0, $parentMenuItemID = 0, $includeDisabledMenuItems = false, array $excludedMenuItemIDs = array()) {
        $this->parentMenuItemID = intval($parentMenuItemID);
        $this->menuID = intval($menuID);
        $parentMenuItem = null;
        // get parent menu item
        if (!$this->parentMenuItemID) {
            // empty node
            $parentMenuItem = new MenuItem(null, array(
                'menuItemID' => 0,
                'menuID' => $this->menuID,
                'menuItemName' => '',
                'menuItemParent' => '',
                'showOrder' => 0,
                'isDisabled' => false             
            ));
        }
        else {
            $parentMenuItem = MenuItemHandler::getInstance()->getMenuItem($this->parentMenuItemID);
            if ($parentMenuItem === null) {
                throw new SystemException("There is no menu item with id '".$this->parentMenuItemID."'");
            }
        }

        parent::__construct(new $this->nodeClassName($parentMenuItem, $includeDisabledMenuItems, $excludedMenuItemIDs), \RecursiveIteratorIterator::SELF_FIRST);
    }

    /**
     * @see	\Countable::count()
     */
    public function count() {
        if ($this->count === null) {
            $this->count = 0;
            foreach ($this as $menuItemNode) {
                $this->count++;
            }
        }

        return $this->count;
    }
}
