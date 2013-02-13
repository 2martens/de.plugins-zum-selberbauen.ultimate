<?php
/**
 * Contains the ContentTagCloudCacheBuilder class.
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
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\tag\Tag;
use wcf\data\tag\TagCloudTag;
use wcf\system\cache\builder\TagCloudCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Caches all content tags.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class ContentTagCloudCacheBuilder extends TagCloudCacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.AbstractCacheBuilder.html#rebuild
	 */
	protected function rebuild(array $parameters) {
		/* @var $objectType \wcf\data\object\type\ObjectType */
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.tagging.taggableObject', 'de.plugins-zum-selberbauen.ultimate.contentTaggable');
		$this->objectTypeIDs = array(
		    intval($objectType->__get('objectTypeID'))
		);
		// workaround
		$this->languageIDs = ArrayUtil::toIntegerArray(array_keys(WCF::getLanguage()->getLanguages()));
		$this->getTags();
		
		return $this->tags;
	}
	
	/**
	 * Gets the tags.
	 * 
	 * @see \wcf\system\cache\builder\TagCloudCacheBuilder::getTags()
	 */
	protected function getTags() {
		if (count($this->objectTypeIDs) > 0) {
			// get tag ids
			$tagIDs = array();
			$conditionBuilder = new PreparedStatementConditionBuilder();
			$conditionBuilder->add('object.objectTypeID IN (?)', array($this->objectTypeIDs));
			$conditionBuilder->add('object.languageID IN (?)', array($this->languageIDs));
			$sql = 'SELECT    COUNT(*) AS counter, object.tagID
			        FROM      wcf'.WCF_N.'_tag_to_object object
			        '.$conditionBuilder->__toString().'
			        GROUP BY  object.tagID
			        ORDER BY  counter DESC';
			$statement = WCF::getDB()->prepareStatement($sql, 500);
			$statement->execute($conditionBuilder->getParameters());
			while ($row = $statement->fetchArray()) {
				$tagIDs[$row['tagID']] = $row['counter'];
			}
			
			// get tags
			if (count($tagIDs)) {
				$conditionBuilder = new PreparedStatementConditionBuilder();
				$conditionBuilder->add('tagID IN (?)', array(array_keys($tagIDs)));
				$sql = 'SELECT	*
				        FROM    wcf'.WCF_N.'_tag
				        '.$conditionBuilder->__toString();
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute($conditionBuilder->getParameters());
				while ($row = $statement->fetchArray()) {
					$row['counter'] = $tagIDs[$row['tagID']];
					$this->tags[$row['tagID']] = new TagCloudTag(new Tag(null, $row));
				}
				
				// sort by counter
				uasort($this->tags, array('parent', 'compareTags'));
			}
		}
	}
}
