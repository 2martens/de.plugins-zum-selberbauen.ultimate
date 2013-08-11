<?php
/**
 * Contains the BlockType PIP.
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
 * @subpackage	system.package.plugin
 * @category	Ultimate CMS
 */
namespace wcf\system\package\plugin;
use ultimate\system\cache\builder\BlockTypeCacheBuilder;
use wcf\system\exception\SystemException;

/**
 * Provides the block type data for the event listeners.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.package.plugin
 * @category	Ultimate CMS
 */
class BlockTypePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	/**
	 * table application prefix
	 * @var	string
	 */
	public $application = 'ultimate';
	
	/**
	 * xml tag name
	 * @var	string
	 */
	public $tagName = 'blocktype';
	
	/**
	 * object editor class name
	 * @var string
	 */
	public $className = 'ultimate\data\blocktype\BlockTypeEditor';
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::prepareImport()
	 */
	protected function prepareImport(array $data) {
		$databaseData = array(
			'blockTypeName' => $data['elements']['blocktypename'],
			'blockTypeClassName' => $data['elements']['blocktypeclassname'],
			'fixedHeight' => (isset($data['elements']['fixedHeight']) ? $data['elements']['fixedHeight'] : 1)
		);
		return $databaseData;
	}
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::validateImport()
	 */
	protected function validateImport(array $data) {
		parent::validateImport($data);
		
		if (empty($data['blockTypeName'])) {
			throw new SystemException('Invalid blockTypeName', 0, 'The blockTypeName cannot be empty.');
		}
		$namespaces = explode('\\', $data['blockTypeClassName']);
		if (empty($namespaces)) {
			throw new SystemException('Invalid blockTypeClassName', 0, 'The blockTypeClassName has to contain namespaces.');
		}
		elseif (count($namespaces) > 1) {
			$applicationPrefix = array_shift($namespaces);
			if ($applicationPrefix != 'ultimate') {
				throw new SystemException('Invalid blockTypeClassName', 0, 'The blockTypeClassName has to contain the application prefix \'ultimate\'.');
			}
		}
		else {
			throw new SystemException('Invalid blockTypeClassName', 0, 'The blockTypeClassName has to contain more than the application prefix.');
		}
		if ($data['fixedHeight'] != 1 && $data['fixedHeight'] != 0) {
			throw new SystemException('Invalid fixedHeight', 0, 'The fixedHeight has to be either 0 or 1.');
		}
	}
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::findExistingItem()
	 */
	protected function findExistingItem(array $data) {
		$sqlData['sql'] = 'SELECT   blockTypeID, packageID, blockTypeName, blockTypeClassName, fixedHeight
		                   FROM     ultimate'.WCF_N.'_blocktype
		                   WHERE    packageID     = ?
		                   AND      blockTypeName = ?';
		$sqlData['parameters'] = array(
			$this->installation->getPackageID(),
			$data['blockTypeName']
		);
		return $sqlData;
	}
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::handleDelete()
	 */
	protected function handleDelete(array $items) {
		$sql = "DELETE FROM ultimate".WCF_N."_".$this->tableName."
		        WHERE       blockTypeName = ?
		        AND         packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		foreach ($items as $item) {
			$statement->execute(array(
				$item['elements']['blocktypename'],
				$this->installation->getPackageID()
			));
		}
	}
	
	/**
	 * @see	wcf\system\package\plugin\IPackageInstallationPlugin::uninstall()
	 */
	public function uninstall() {
		parent::uninstall();
	
		// clear cache immediately
		BlockTypeCacheBuilder::getInstance()->reset();
	}
}
