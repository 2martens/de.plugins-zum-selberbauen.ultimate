<?php
namespace ultimate\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

/**
 * Works like a PIP and adds block types to databse or removes them.
 * 
 * This PIP-like listener is called on each installation, update or uninstallation of a Ultimate CMS plugin.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
class PIPReplacementListener implements IEventListener {
	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		
		if ($className == 'BlockTypePackageInstallationPlugin') {
			$this->{$eventName}($eventObj);
		}
	}
	
	/**
	 * Checks for specific requirements and sets values.
	 * 
	 * @param	\wcf\system\package\plugin\BlockTypePackageInstallationPlugin	$eventObj
	 */
	protected function install(\wcf\system\package\plugin\BlockTypePackageInstallationPlugin $eventObj) {
		$archive = $eventObj->installation->getArchive();
		
		// checks plugin tag - if package is not plugin of Ultimate CMS -> throw exception
		if ($archive->getPackageInfo('plugin') != 'de.plugins-zum-selberbauen.ultimate') {
			throw new SystemException('You can\'t use this PIP if you\'re not a plugin of the Ultimate CMS.');
		}
		
		$eventObj->className = '\ultimate\data\blocktype\BlockTypeEditor';
	}
	
	/**
	 * Fills the prepared variable in the PIP the sql data.
	 * 
	 * @param \wcf\system\package\plugin\BlockTypePackageInstallationPlugin $eventObj
	 */
	protected function findExistingItem(\wcf\system\package\plugin\BlockTypePackageInstallationPlugin $eventObj) {
		$sqlData['sql'] = 'SELECT   blockTypeID, packageID, blockTypeName, blockTypeClassName
		                   FROM     ultimate'.ULTIMATE_N.'_blocktype
		                   WHERE    packageID     = ?
		                   AND      blockTypeName = ?';
		$sqlData['parameters'] = array(
			$eventObj->installation->getPackageID(), 
			$eventObj->findExistingItemData['blockTypeName']
		);
		$eventObj->findExistingItemSQLData = $sqlData;
	}
	
	/**
	 * Checks if uninstallation is possible. If true, the specific variable in the PIP is set appropriate.
	 * 
	 * @param	\wcf\system\package\plugin\BlockTypePackageInstallationPlugin	$eventObj
	 */
	protected function hasUninstall(\wcf\system\package\plugin\BlockTypePackageInstallationPlugin $eventObj) {
		$sql = 'SELECT  COUNT(*) AS count
		        FROM    ultimate'.ULTIMATE_N.'_blocktype
		        WHERE   packageID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($eventObj->installation->getPackageID()));
		$installationCount = $statement->fetchArray();
		$eventObj->hasUninstallReturn = ($installationCount['count'] > 0);
	}
	
	/**
	 * Uninstalls the fitting block types.
	 * 
	 * @param	\wcf\system\package\plugin\BlockTypePackageInstallationPlugin	$eventObj
	 */
	protected function uninstall(\wcf\system\package\plugin\BlockTypePackageInstallationPlugin $eventObj) {
		$sql = 'DELETE FROM	ultimate'.ULTIMATE_N.'_blocktype
		        WHERE       packageID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($eventObj->installation->getPackageID()));
	}
}
