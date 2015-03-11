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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.block
 * @category	Ultimate CMS
 */
namespace ultimate\data\block;
use ultimate\data\blocktype\BlockType;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a block entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.ultimate.block
 * @category	Ultimate CMS
 * 
 * @property-read	integer								$blockID
 * @property-read	integer								$blockTypeID
 * @property-read	\ultimate\data\blocktype\BlockType	$blockType
 * @property-read	string								$query
 * @property-read	array								$parameters	a numeric array of parameters (all primitive types are available for the parameters)
 * @property-read	integer								$showOrder
 * @property-read	array								$additionalData an associative array of additional data (what keys are available depends on the blockType, the values can be anything)	
 */
class Block extends AbstractUltimateDatabaseObject {
	/**
	 * The database table name.
	 * @var string
	 */
	protected static $databaseTableName = 'block';
	
	/**
	 * If true, the database table index is used as identity.
	 * @var boolean
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * The database table index name.
	 * @var string
	 */
	protected static $databaseTableIndexName = 'blockID';
	
	/**
	 * Creates a new instance of the DatabaseObject class.
	 *
	 * @param	mixed			$id
	 * @param	array			$row
	 * @param	DatabaseObject	$object
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
	 * Returns the value of a object data variable with the given name.
	 * 
	 * @param	string	$name
	 * @return	mixed
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
	 * Handles data.
	 * 
	 * @param	array	$data
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
				'blockTypeClassName' => ''		
			));
		}
		
		if (!empty($data['additionalData'])) {
			$data['additionalData'] = unserialize($data['additionalData']);
		} else {
			$data['additionalData'] = array();
		}
		$data['showOrder'] = intval($data['showOrder']);
		parent::handleData($data);
	}
}
