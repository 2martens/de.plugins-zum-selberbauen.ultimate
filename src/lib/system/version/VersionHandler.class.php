<?php
/**
 * Contains the VersionHandler class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.version
 * @category	Ultimate CMS
 */
namespace ultimate\system\version;
use ultimate\system\cache\builder\VersionCacheBuilder;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\SingletonFactory;

/**
 * Handles versions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.version
 * @category	Ultimate CMS
 */
class VersionHandler extends SingletonFactory {
	/**
	 * cached versions
	 * @var	\wcf\data\VersionableDatabaseObject[][][]
	 */
	protected $versions = array();
	
	/**
	 * list of all version ids grouped after objectTypeID and objectID
	 * @var	integer[][][]
	*/
	protected $versionIDs = array();
	
	/**
	 * maps objectType ids and version ids to object ids.
	 * @var integer[][]
	 */
	protected $versionIDsToObjectID = array();
	
	/**
	 * list of object type ids
	 * @var	integer[]
	*/
	protected $objectTypeIDs = array();
	
	/**
	 * list of version object types
	 * @var	\wcf\data\object\type\ObjectType[]
	*/
	protected $objectTypes = array();
	
	/**
	 * Returns all versions of the object with the given object type id and object id or an empty array if no such version exists.
	 *
	 * @param	integer	$objectTypeID
	 * @param	integer	$objectID
	 * @return	\wcf\data\VersionableDatabaseObject[]|array
	*/
	public function getVersions($objectTypeID, $objectID) {
		if (isset($this->versions[$objectTypeID][$objectID])) {
			return $this->versions[$objectTypeID][$objectID];
		}
		
		return array();
	}
	
	/**
	 * Returns the database object with the given version id and object id or null if no such object exists.
	 *
	 * @param	integer		$objectTypeID
	 * @param	integer		$objectID
	 * @param	integer		$versionID
	 * @return	\wcf\data\VersionableDatabaseObject|null
	 */
	public function getVersionByID($objectTypeID, $objectID, $versionID) {
		if (isset($this->versions[$objectTypeID][$objectID][$versionID])) {
			return $this->versions[$objectTypeID][$objectID][$versionID];
		}
	
		return null;
	}
	
	/**
	 * Returns the object type with the given id or null if no such object type exists.
	 *
	 * @param	integer	$objectTypeID
	 * @return	\wcf\data\object\type\ObjectType|null
	 */
	public function getObjectType($objectTypeID) {
		if (isset($this->objectTypeIDs[$objectTypeID])) {
			return $this->getObjectTypeByName($this->objectTypeIDs[$objectTypeID]);
		}
	
		return null;
	}
	
	/**
	 * Returns the object type with the given name.
	 *
	 * @param	string		$objectTypeName
	 * @return	\wcf\data\object\type\ObjectType|null
	 */
	public function getObjectTypeByName($objectTypeName) {
		if (isset($this->objectTypes[$objectTypeName])) {
			return $this->objectTypes[$objectTypeName];
		}
	
		return null;
	}
	
	/**
	 * Returns a list of object types.
	 *
	 * @return	\wcf\data\object\type\ObjectType[]
	 */
	public function getObjectTypes() {
		return $this->objectTypes;
	}
	
	/**
	 * Reloads the version cache.
	 */
	public function reloadCache() {
		VersionCacheBuilder::getInstance()->reset();
	
		$this->init();
	}
	
	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('com.woltlab.wcf.versionableObject');
		
		foreach ($this->objectTypes as $objectType) {
			$this->objectTypeIDs[$objectType->objectTypeID] = $objectType->objectType;
		}
		
		$this->versions = VersionCacheBuilder::getInstance()->getData(array(), 'versions');
		$this->versionIDs = VersionCacheBuilder::getInstance()->getData(array(), 'versionIDs');
		$this->versionIDsToObjectID = VersionCacheBuilder::getInstance()->getData(array(), 'versionIDsToObjectID');
	}
}
