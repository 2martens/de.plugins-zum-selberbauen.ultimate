<?php
/**
 * Contains the AbstractUltimateDatabaseObject class.
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
 * @subpackage	data
 * @category	Ultimate CMS
 */
namespace ultimate\data;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Every Ultimate data class should extend this class.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data
 * @category	Ultimate CMS
 */
abstract class AbstractUltimateDatabaseObject extends DatabaseObject implements IUltimateData {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IStorableObject.html#getDatabaseTableName
	 */
	public static function getDatabaseTableName() {
		return 'ultimate'.WCF_N.'_'.static::$databaseTableName;
	}
	
	/**
	 * Returns the meta data of this content.
	 * 
	 * @param	integer		$objectID
	 * @param	string		$objectType	(category, content, index or page)
	 * 
	 * @return	string[]
	 */
	protected function getMetaData($objectID, $objectType) {
		$sql = 'SELECT    metaTable.metaDescription, metaTable.metaKeywords
		        FROM      ultimate'.WCF_N.'_meta metaTable
		        WHERE     metaTable.objectID = ?
		        AND       metaTable.objectType = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($objectID, $objectType));
	
		$metaData = array();
		$metaData = $statement->fetchArray();
		return (($metaData !== false) ? $metaData: array());
	}
}
