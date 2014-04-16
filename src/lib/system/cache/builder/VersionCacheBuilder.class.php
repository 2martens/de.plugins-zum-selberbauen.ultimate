<?php
/**
 * Contains the VersionCacheBuilder class.
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
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches versionable objects.
 * 
 * Provides three variables:
 * * \wcf\data\VersionableDatabaseObject[][][] versions ($objectTypeID => ($objectID => ($versionID => versionedObject)))
 * * integer[][][] versionIDs ($objectTypeID => ($objectID => ($versionID)))
 * * integer[][] versionIDsToObjectID ($objectTypeID => ($versionID => $objectID))
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class VersionCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 * 
	 * @param	array	$parameters
	 * 
	 * @return	array
	 */
	public function rebuild(array $parameters) {
		// get object types
		// $objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('com.woltlab.wcf.versionableObject');
		// only cache Ultimate CMS object types
		$objectTypes = array(
			'content' => ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.versionableObject', 'de.plugins-zum-selberbauen.ultimate.content'),
			'page' => ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.versionableObject', 'de.plugins-zum-selberbauen.ultimate.page')
		);
		
		$data = array(
			'versions' => array(),
			'versionIDs' => array(),
			'versionIDsToObjectID' => array()
		);
		
		foreach ($objectTypes as $objectType => $objectTypeObj) {
			/* @var $objectTypeObj \wcf\data\object\type\ObjectType */
			$objectTypeID = $objectTypeObj->__get('objectTypeID');
			$tableName = call_user_func(array($objectType->__get('className'), 'getDatabaseTableName'));
			$versionTableName = call_user_func(array($objectType->__get('className'), 'getDatabaseVersionTableName'));
			$databaseIndexName = call_user_func(array($objectTypeObj->__get('className'), 'getDatabaseTableIndexName'));
			$databaseVersionTableIndexName = call_user_func(array($objectTypeObj->__get('className'), 'getDatabaseVersionTableIndexName'));
			
			// read content and page specific data
			$sqlSelect = '';
			$sqlJoin = false;
			$tableAlias = '';
			if ($objectType == 'content') {
				$tableAlias = 'content';
				$sqlSelect = 'content.contentSlug, content.lastModified, content.cumulativeLikes, content.views';
				$sqlJoin = true;
			}
			if ($objectType == 'page') {
				$tableAlias = 'page';
				$sqlSelect = 'page.pageSlug, page.lastModified';
				$sqlJoin = true;
			}
			
			$sql = 'SELECT version.*'.(!empty($sqlSelect) ? ', '.$sqlSelect : '').'
			        FROM   '.$versionTableName.' version
			        '.($sqlJoin ? 'LEFT JOIN '.$tableName.'
			        ON     (version.'.$databaseIndexName.' = '.$tableAlias.'.'.$databaseIndexName.')' : '');
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute();
			
			while ($row = $statement->fetchArray()) {
				$object = new $objectTypeObj->className(null, $row);
				$data['versions'][$objectTypeID][$object->$databaseIndexName][$object->$databaseVersionTableIndexName] = $object;
				$data['versionIDs'][$objectTypeID][$object->$databaseIndexName][] = $object->$databaseVersionTableIndexName;
				$data['versionIDsToObjectID'][$objectTypeID][$object->$databaseVersionTableIndexName] = $object->$databaseIndexName;
			}
		}
		
		return $data;
	}
}
