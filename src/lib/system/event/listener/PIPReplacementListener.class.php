<?php
/**
 * Contains the PIP-Replacement listener.
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
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
namespace ultimate\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

/**
 * Works like a PIP and adds blockTypes/widgetTypes to database or removes them.
 * 
 * This PIP-like listener is called on each installation, update or uninstallation of a Ultimate CMS plugin.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
class PIPReplacementListener implements IEventListener {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.event.IEventListener.html#execute
	 */
	public function execute($eventObj, $className, $eventName) {
		
		if ($className == 'BlockTypePackageInstallationPlugin') {
			$this->{$eventName.'BlockType'}($eventObj);
		} elseif ($className == 'WidgetTypePackageInstallationPlugin') {
			$this->{$eventName.'WidgetType'}($eventObj);
		}
	}
	
	/**
	 * Checks for specific requirements and sets values for blockType installation.
	 * 
	 * @param	\wcf\system\package\plugin\BlockTypePackageInstallationPlugin	$eventObj
	 * @throws	\wcf\system\exception\SystemException	if the currently installed package is not a plugin of the Ultimate CMS
	 */
	protected function installBlockType(\wcf\system\package\plugin\BlockTypePackageInstallationPlugin $eventObj) {
		$archive = $eventObj->installation->getArchive();
		
		// checks plugin tag - if package is not plugin of Ultimate CMS -> throw exception
		if ($archive->getPackageInfo('plugin') != 'de.plugins-zum-selberbauen.ultimate') {
			throw new SystemException('You can\'t use this PIP if you\'re not a plugin of the Ultimate CMS.');
		}
		
		$eventObj->className = '\ultimate\data\blocktype\BlockTypeEditor';
	}
	
	/**
	 * Checks for specific requirements and sets values for widgetType installation.
	 * 
	 * @param	\wcf\system\package\plugin\WidgetTypePackageInstallationPlugin $eventObj
	 * @throws	\wcf\system\exception\SystemException	if the currently installed package is not a plugin of the Ultimate CMS
	 */
	protected function installWidgetType(\wcf\system\package\plugin\WidgetTypePackageInstallationPlugin $eventObj) {
		$archive = $eventObj->installation->getArchive();
		
		// checks plugin tag - if package is not plugin of Ultimate CMS -> throw exception
		if ($archive->getPackageInfo('plugin') != 'de.plugins-zum-selberbauen.ultimate') {
			throw new SystemException('You can\'t use this PIP if you\'re not a plugin of the Ultimate CMS.');
		}
		
		$eventObj->className = '\ultimate\data\widgettype\WidgetTypeEditor';
	}
	
	/**
	 * Fills the prepared variable in the PIP with the blockType sql data.
	 * 
	 * @param \wcf\system\package\plugin\BlockTypePackageInstallationPlugin $eventObj
	 */
	protected function findExistingItemBlockType(\wcf\system\package\plugin\BlockTypePackageInstallationPlugin $eventObj) {
		$sqlData['sql'] = 'SELECT   blockTypeID, packageID, blockTypeName, blockTypeClassName, fixedHeight
		                   FROM     ultimate'.WCF_N.'_blocktype
		                   WHERE    packageID     = ?
		                   AND      blockTypeName = ?';
		$sqlData['parameters'] = array(
			$eventObj->installation->getPackageID(), 
			$eventObj->findExistingItemData['blockTypeName']
		);
		$eventObj->findExistingItemSQLData = $sqlData;
	}
	
	/**
	 * Fills the prepared variable in the PIP with the widgetType sql data.
	 *
	 * @param \wcf\system\package\plugin\WidgetTypePackageInstallationPlugin $eventObj
	 */
	protected function findExistingItemWidgetType(\wcf\system\package\plugin\WidgetTypePackageInstallationPlugin $eventObj) {
		$sqlData['sql'] = 'SELECT   widgetTypeID, packageID, widgetTypeName, widgetTypeClassName
		                   FROM     ultimate'.WCF_N.'_widgettype
		                   WHERE    packageID     = ?
		                   AND      widgetTypeName = ?';
		$sqlData['parameters'] = array(
			$eventObj->installation->getPackageID(),
			$eventObj->findExistingItemData['widgetTypeName']
		);
		$eventObj->findExistingItemSQLData = $sqlData;
	}
	
	/**
	 * Checks if uninstallation of a blockType is possible. If true, the specific variable in the PIP is set appropriate.
	 * 
	 * @param	\wcf\system\package\plugin\BlockTypePackageInstallationPlugin	$eventObj
	 */
	protected function hasUninstallBlockType(\wcf\system\package\plugin\BlockTypePackageInstallationPlugin $eventObj) {
		$sql = 'SELECT  COUNT(*) AS count
		        FROM    ultimate'.WCF_N.'_blocktype
		        WHERE   packageID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($eventObj->installation->getPackageID()));
		$installationCount = $statement->fetchArray();
		$eventObj->hasUninstallReturn = ($installationCount['count'] > 0);
	}
	
	/**
	 * Checks if uninstallation of a widgetType is possible. If true, the specific variable in the PIP is set appropriate.
	 *
	 * @param	\wcf\system\package\plugin\WidgetTypePackageInstallationPlugin	$eventObj
	 */
	protected function hasUninstallWidgetType(\wcf\system\package\plugin\WidgetTypePackageInstallationPlugin $eventObj) {
		$sql = 'SELECT  COUNT(*) AS count
		        FROM    ultimate'.WCF_N.'_widgettype
		        WHERE   packageID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($eventObj->installation->getPackageID()));
		$installationCount = $statement->fetchArray();
		$eventObj->hasUninstallReturn = ($installationCount['count'] > 0);
	}
	
	/**
	 * Uninstalls the fitting blockTypes.
	 * 
	 * @param	\wcf\system\package\plugin\BlockTypePackageInstallationPlugin	$eventObj
	 */
	protected function uninstallBlockType(\wcf\system\package\plugin\BlockTypePackageInstallationPlugin $eventObj) {
		$sql = 'DELETE FROM	ultimate'.WCF_N.'_blocktype
		        WHERE       packageID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($eventObj->installation->getPackageID()));
	}
	
	/**
	 * Uninstalls the fitting widgetTypes.
	 *
	 * @param	\wcf\system\package\plugin\WidgetTypePackageInstallationPlugin	$eventObj
	 */
	protected function uninstallWidgetType(\wcf\system\package\plugin\WidgetTypePackageInstallationPlugin $eventObj) {
		$sql = 'DELETE FROM	ultimate'.WCF_N.'_widgettype
		        WHERE       packageID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($eventObj->installation->getPackageID()));
	}
}
