<?php
/**
 * Contains the block data model class.
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
 * @subpackage	data.block
 * @category	Ultimate CMS
 */
namespace ultimate\data\block;
use wcf\system\WCF;

use ultimate\data\blocktype\BlockType;
use ultimate\data\AbstractUltimateDatabaseObject;

/**
 * Represents a block entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.ultimate.block
 * @category	Ultimate CMS
 */
class Block extends AbstractUltimateDatabaseObject {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableName
	 */
	protected static $databaseTableName = 'block';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'blockID';
	
	/**
	 * Creates a new instance of the DatabaseObject class.
	 *
	 * @param	mixed				$id
	 * @param	array				$row
	 * @param	wcf\data\DatabaseObject		$object
	 */
	public function __construct($id, array $row = null, DatabaseObject $object = null) {
		if ($id !== null) {
			$sql = "SELECT	*
				FROM	".static::getDatabaseTableName()."
				WHERE	".static::getDatabaseTableIndexName()." = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($id));
			$row = $statement->fetchArray();
			
			// enforce data type 'array'
			if ($row === false) $row = array();
		}
		else if ($object !== null) {
			$row = $object->data;
		}
	
		$this->handleData($row);
	}
	
	/**
	 * @see http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#__get
	 */
	public function __get($name) {
		$value = parent::__get($name);
		// makes additional data adressable like normal variables
		if ($value === null) {
			if (isset($this->data['additionalData'][$name])) {
				return $this->data['additionalData'][$name];
			}
		}
		return $value;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#handleData
	 */
	protected function handleData($data) {
		if (!empty($data['parameters'])) {
			$data['parameters'] = unserialize($data['parameters']);
		} else {
			$data['parameters'] = array();
		}
		
		if ($data['blockTypeID']) {
			$data['blockType'] = new BlockType($data['blockTypeID']);
		} else {
			$data['blockType'] = new BlockType(null, array(
				'blockTypeID' => 0,
				'packageID' => PACKAGE_ID,
				'blockTypeName' => '',
				'blockTypeClassName' => '',
				'fixedHeight' => 1					
			));
		}
		
		if (!empty($data['additionalData'])) {
			$data['additionalData'] = unserialize($data['additionalData']);
		} else {
			$data['additionalData'] = array();
		}
		parent::handleData($data);
	}
}
