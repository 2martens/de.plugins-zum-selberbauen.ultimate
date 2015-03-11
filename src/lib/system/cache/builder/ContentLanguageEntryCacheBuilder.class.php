<?php
/**
 * Contains ContentLanguageEntryCacheBuilder class.
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
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use wcf\system\cache\builder\AbstractLanguageEntryCacheBuilder;
use wcf\system\WCF;

/**
 * Caches the content language entries.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class ContentLanguageEntryCacheBuilder extends AbstractLanguageEntryCacheBuilder {
	/**
	 * Name of the languageEntry class (FQCN).
	 * @var string
	 */
	protected static $languageEntryClass = '\ultimate\data\content\language\ContentLanguageEntry';
	
	/**
	 * Builds language entries.
	 *
	 * @return	array	associative array (objectID => (languageID => languageEntry))
	 */
	protected function buildLanguageEntries() {
		$languageEntries = array();
		
		$sql = 'SELECT languageEntryID, contentVersionID, languageID, contentTitle, contentDescription
		        FROM   '.static::getDatabaseTableName();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		while ($row = $statement->fetchArray()) {
			$row['languageID'] = ($row['languageID'] === null ? 0 : $row['languageID']);
			$entry = new static::$languageEntryClass(null, null, null, $row);
			if (!isset($languageEntries[$row['contentVersionID']])) {
				$languageEntries[$row['contentVersionID']] = array();
			}
			$languageEntries[$row['contentVersionID']][$row['languageID']] = $entry;
		}
		return $languageEntries;
	}
}
