<?php
/**
 * Contains the tagged content data model list class.
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
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use ultimate\system\cache\builder\ContentPageCacheBuilder;
use ultimate\system\cache\builder\PageCacheBuilder;
use wcf\data\tag\Tag;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;

/**
 * Represents a list of tagged contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class TaggedContentList extends ContentList {
	/**
	 * The class name of the decorator.
	 * @var string
	 */
	public $decoratorClassName = '\ultimate\data\content\TaggedContent';
	
	/**
	 * sql order by statement
	 * @var	string
	 */
	public $sqlOrderBy = 'contentVersion.publishDate DESC';
	
	/**
	 * Creates a new TaggedContentList object.
	 * 
	 * @param	\wcf\data\tag\Tag	$tag
	 */
	public function __construct(Tag $tag) {
		parent::__construct();
		
		$this->getConditionBuilder()->add('tag_to_object.objectTypeID = ? AND tag_to_object.languageID = ? AND tag_to_object.tagID = ?', array(TagEngine::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.content'), $tag->languageID, $tag->tagID));
		$this->getConditionBuilder()->add('content.contentID = tag_to_object.objectID');
		$this->getConditionBuilder()->add('(contentVersion.visibility = ? OR (contentVersion.visibility = ? AND contentVersion.authorID = ?) OR (contentVersion.visibility = ? AND groupToContent.groupID IN (?)))', array('public', 'private', WCF::getUser()->__get('userID'), 'protected', WCF::getUser()->getGroupIDs()));
		$this->getConditionBuilder()->add('contentVersion.status = ?', array(3)); // fixes #219
		$this->sqlJoins .= ' LEFT JOIN ultimate'.WCF_N.'_content_version contentVersion ON (contentVersion.contentID = content.contentID)';
		$this->sqlConditionJoins .= ' LEFT JOIN ultimate'.WCF_N.'_content_version contentVersion ON (contentVersion.contentID = content.contentID)';
		$this->sqlConditionJoins .= ' LEFT JOIN ultimate'.WCF_N.'_user_group_to_content_version groupToVersion ON (contentVersion.versionID = groupToVersion.versionID)';
	}
	
	/**
	 * Returns the amount of tagged contents.
	 * 
	 * @return	integer
	 */
	public function countObjects() {
		$sql = 'SELECT COUNT(*) AS count
		        FROM   wcf'.WCF_N.'_tag_to_object tag_to_object,
		               ultimate'.WCF_N.'_content content
		        '.$this->sqlConditionJoins.'
		        '.$this->getConditionBuilder();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($this->getConditionBuilder()->getParameters());
		$row = $statement->fetchArray();
		return $row['count'];
	}
	
	/**
	 * Reads the object IDs.
	 */
	public function readObjectIDs() {
		$this->objectIDs = array();
		$sql = 'SELECT tag_to_object.objectID
		        FROM   wcf'.WCF_N.'_tag_to_object tag_to_object,
		               ultimate'.WCF_N.'_content content
		        '.$this->sqlConditionJoins.'
		        '.$this->getConditionBuilder().'
		        '.(!empty($this->sqlOrderBy) ? 'ORDER BY '.$this->sqlOrderBy : '');
		$statement = WCF::getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
		$statement->execute($this->getConditionBuilder()->getParameters());
		while ($row = $statement->fetchArray()) {
			$this->objectIDs[] = $row['objectID'];
		}
	}
	
	/**
	 * Reads the objects.
	 */
	public function readObjects() {
		if ($this->objectIDs === null) $this->readObjectIDs();
		$this->conditionBuilder = new PreparedStatementConditionBuilder();
		
		// include code from DatabaseObjectList
		if ($this->objectIDs !== null) {
			if (empty($this->objectIDs)) {
				return;
			}
			$sql = 'SELECT '.(!empty($this->sqlSelects) ? $this->sqlSelects.($this->useQualifiedShorthand ? ',' : '') : '').'
			               '.($this->useQualifiedShorthand ? $this->getDatabaseTableAlias().'.*' : '').'
			        FROM   '.$this->getDatabaseTableName()." ".$this->getDatabaseTableAlias().'
			               '.$this->sqlJoins.'
			        WHERE  '.$this->getDatabaseTableAlias().'.'.$this->getDatabaseTableIndexName().' IN (?'.str_repeat(',?', count($this->objectIDs) - 1).')
			               '.(!empty($this->sqlOrderBy) ? 'ORDER BY '.$this->sqlOrderBy : '');
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute($this->objectIDs);
			$this->objects = $statement->fetchObjects(($this->objectClassName ?: $this->className));
		}
		else {
			$sql = 'SELECT  '.(!empty($this->sqlSelects) ? $this->sqlSelects.($this->useQualifiedShorthand ? ',' : '') : '').'
			                '.($this->useQualifiedShorthand ? $this->getDatabaseTableAlias().'.*' : '').'
			        FROM    '.$this->getDatabaseTableName().' '.$this->getDatabaseTableAlias().'
			                '.$this->sqlJoins.'
			                '.$this->getConditionBuilder().'
			                '.(!empty($this->sqlOrderBy) ? 'ORDER BY '.$this->sqlOrderBy : '');
			$statement = WCF::getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
			$statement->execute($this->getConditionBuilder()->getParameters());
			$this->objects = $statement->fetchObjects(($this->objectClassName ?: $this->className));
		}
		
		// decorate objects
		if (!empty($this->decoratorClassName)) {
			foreach ($this->objects as &$object) {
				$object = new $this->decoratorClassName($object);
			}
			unset($object);
		}
		
		// use table index as array index
		$objects = array();
		foreach ($this->objects as $object) {
			$objectID = $object->getObjectID();
			// the select process is not distinctive
			if (!isset($objects[$objectID])) {
				$objects[$objectID] = $object;
				$this->indexToObject[] = $objectID;
			}
		}
		$this->objectIDs = $this->indexToObject;
		$this->objects = $objects;
		
		// filter those objects out that are not visible for current user
// 		$conditionBuilder = new PreparedStatementConditionBuilder();
// 		$conditionBuilder->add('groupToVersion.groupID IN (?)', array(WCF::getUser()->getGroupIDs()));
// 		$conditionBuilder->add('contentVersion.versionID = ?');
// 		$conditionBuilder->add('contentVersion.contentID = ?');
// 		$sql = 'SELECT DISTINCT contentVersion.contentID
// 		        FROM   ultimate'.WCF_N.'_content_version contentVersion
// 		        LEFT JOIN ultimate'.WCF_N.'_user_group_to_content_version groupToVersion
// 		        ON        ((contentVersion.contentID = groupToVersion.contentID) AND (contentVersion.versionID = groupToVersion.versionID))
// 		        '.$conditionBuilder;
// 		$statement = WCF::getDB()->prepareStatement($sql);
		
// 		$objects = array();
		
// 		WCF::getDB()->beginTransaction();
// 		foreach ($this->objects as $objectID => $object) {
// 			$versionID = $object->getCurrentVersion()->getObjectID();
// 			$statement->executeUnbuffered(array_merge($conditionBuilder->getParameters(), array($versionID, $objectID)));
// 			$row = $statement->fetchArray();
			
// 			if ($row === null) continue;
			
// 			$objects[$objectID] = $object;
// 		}
// 		WCF::getDB()->commitTransaction();
		
// 		$this->objects = $objects;
// 		$this->objectIDs = $this->indexToObject = array_keys($objects);
		
		$pageIDs = array_flip(ContentPageCacheBuilder::getInstance()->getData(array(), 'contentIDToPageID'));
		$pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
		foreach ($this->objects as $objectID => &$object) {
			if (isset($pageIDs[$objectID])) {
				$object->page = $pages[$pageIDs[$objectID]];
			}
		}
	}
}
