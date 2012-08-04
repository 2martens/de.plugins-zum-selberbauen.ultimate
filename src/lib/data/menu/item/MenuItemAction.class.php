<?php
namespace ultimate\data\menu\item;
use ultimate\system\menu\item\MenuItemHandler;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\ValidateActionException;
use wcf\system\WCF;

/**
 * Executes menu item-related functions.
 * 
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage data.menu.item
 * @category Ultimate CMS
 */
class MenuItemAction extends AbstractDatabaseObjectAction {
    /**
     * @see \wcf\data\AbstractDatabaseObjectAction::$className
     */
    public $className = '\ultimate\data\menu\item\MenuItemEditor';
    
    /**
     * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
     */
    protected $permissionsCreate = array('admin.content.ultimate.canAddMenuItem');
    
    /**
     * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
    */
    protected $permissionsDelete = array('admin.content.ultimate.canDeleteMenuItem');
    
    /**
     * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
    */
    protected $permissionsUpdate = array('admin.content.ultimate.canEditMenuItem');

    /**
     * Toggles the activity status of menu items.
     */
    public function toggle() {
        foreach ($this->objects as $menuItemEditor) {
            /* @var $menuItemEditor \ultimate\data\menu\item\MenuItemEditor */
            /* @var $menuItem \ultimate\data\menu\item\MenuItem */
            $menuItemEditor->update(array(
                'isDisabled' => 1 - $menuItemEditor->__get('isDisabled')
            ));
        }
    }
    
    /**
     * Updates the position of menu items.
     */
    public function updatePosition() {
        $showOrders = array();
    
        WCF::getDB()->beginTransaction();
        foreach ($this->parameters['data']['structure'] as $parentMenuItemID => $menuItemIDs) {
            if (!isset($showOrders[$parentMenuItemID])) {
                $showOrders[$parentMenuItemID] = 1;
            }
            	
            foreach ($menuItemIDs as $menuItemID) {
                $this->objects[$menuItemID]->update(array(
                    'menuItemParent' => $parentMenuItemID ? $this->objects[$parentMenuItemID]->__get('menuItemName') : '',
                    'showOrder' => $showOrders[$parentMenuItemID]++
                ));
            }
        }
        WCF::getDB()->commitTransaction();
    }
    
    /**
     * Validates the 'toggle' action.
     */
    public function validateToggle() {
        $this->validateUpdate();
    }
    
    /**
     * Validates the 'toggleContainer' action.
     */
    public function validateToggleContainer() {
        $this->validateUpdate();
    }
    
    /**
     * Validates the 'updatePosition' action.
     */
    public function validateUpdatePosition() {
        // validate permissions
        if (count($this->permissionsUpdate)) {
            try {
                WCF::getSession()->checkPermissions($this->permissionsUpdate);
            }
            catch (PermissionDeniedException $e) {
                throw new ValidateActionException('Insufficient permissions');
            }
        }
    
        // validate 'structure' parameter
        if (!isset($this->parameters['data']['structure'])) {
            throw new ValidateActionException("Missing 'structure' parameter");
        }
        if (!is_array($this->parameters['data']['structure'])) {
            throw new ValidateActionException("'structure' parameter is no array");
        }
    
        // validate given menu item ids
        foreach ($this->parameters['data']['structure'] as $parentMenuItemID => $menuItemIDs) {
            if ($parentMenuItemID) {
                // validate menu item
                $menuItem = MenuItemHandler::getInstance()->getMenuItem($parentMenuItemID);
                if ($menuItem === null) {
                    throw new ValidateActionException("Unknown menu item with id '".$parentMenuItemID."'");
                }
    
                $this->objects[$menuItem->__get('menuItemID')] = new $this->className($menuItem);
    
            }
            	
            foreach ($menuItemIDs as $menuItemID) {
                // validate menu item
                $menuItem = MenuItemHandler::getInstance()->getMenuItem($menuItemID);
                if ($menuItem === null) {
                    throw new ValidateActionException("Unknown menu item with id '".$menuItemID."'");
                }
    
                $this->objects[$menuItem->__get('menuItemID')] = new $this->className($menuItem);
    
            }
        }
    }
}
