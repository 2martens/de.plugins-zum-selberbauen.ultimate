<?php
/**
 * Contains the FeedContentList class.
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
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use ultimate\system\cache\builder\ContentPageCacheBuilder;
use wcf\system\WCF;

/**
 * Represents a list of feed contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class FeedContentList extends ContentList {
	/**
	 * The sql snippet that will be used at the order position.
	 * @var string
	 */
	public $sqlOrderBy = 'contentVersion.publishDate DESC';
	
	/**
	 * The class name of the decorator.
	 * @var string
	 */
	public $decoratorClassName = 'ultimate\data\content\FeedContent';
	
	/**
	 * The ids of the categories the contents should be in.
	 * @var integer[]
	 */
	protected $categoryIDs = array();
	
	/**
	 * Initializes a new FeedContentList object.
	 *
	 * @param	integer[]	$categoryIDs
	 */
	public function __construct(array $categoryIDs) {
		parent::__construct();
		$pageContentIDs = array_values(ContentPageCacheBuilder::getInstance()->getData(array(), 'contentIDToPageID'));
		$this->sqlJoins .= ' LEFT JOIN ultimate'.WCF_N.'_content_version contentVersion ON (contentVersion.contentID = content.contentID)';
		$this->sqlConditionJoins .= ' LEFT JOIN ultimate'.WCF_N.'_content_version contentVersion ON (contentVersion.contentID = content.contentID)';
		$this->getConditionBuilder()->add('contentVersion.status = ?', array(3));
		$this->getConditionBuilder()->add('content.contentID NOT IN (?)', array($pageContentIDs));
		$this->categoryIDs = $categoryIDs;
	}
	
	/**
	 * Reads the objects for this list.
	 */
	public function readObjects() {
		if (empty($this->objectIDs)) $this->readObjectIDs();
		
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
		
		if (!empty($this->categoryIDs)) {
			$remainingContents = array();
			$indexToObject = array();
			foreach ($this->objects as $contentID => $content) {
				$categoryIDs = array_keys($content->getCategories());
				$intersection = array_intersect($categoryIDs, $this->categoryIDs);
				if (!empty($intersection)) {
					$remainingContents[$contentID] = $content;
					$indexToObject[] = $contentID;
				}
			}
			$this->objectIDs = $indexToObject;
			$this->indexToObject = $indexToObject;
			$this->objects = $remainingContents;
		}
	}
}
