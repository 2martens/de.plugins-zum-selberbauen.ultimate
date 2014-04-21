<?php
/**
 * Contains the CategoryLanguageEntryCacheBuilder class.
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
use wcf\system\cache\builder\AbstractLanguageEntryCacheBuilder;
use wcf\system\WCF;

/**
 * Caches the page language entries.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class CategoryLanguageEntryCacheBuilder extends AbstractLanguageEntryCacheBuilder {
	/**
	 * Name of the languageEntry class (FQCN).
	 * @var string
	 */
	protected static $languageEntryClass = '\ultimate\data\page\language\PageLanguageEntry';
	
	/**
	 * Builds language entries.
	 *
	 * @return	array	associative array (objectID => (languageID => languageEntry))
	 */
	protected function buildLanguageEntries() {
		$sql = 'SELECT *
		        FROM   '.static::getDatabaseTableName();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		$data = array();
		while ($row = $statement->fetchArray()) {
			$entry = new static::$languageEntryClass(null, null, null, $row);
			if (!isset($data[$row['pageID']])) {
				$data[$row['pageID']] = array();
			}
			$data[$row['pageID']][$row['languageID']] = $entry;
		}
		
		return $data;
	}
}
