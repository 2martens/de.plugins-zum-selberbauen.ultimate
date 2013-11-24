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
 * @copyright	2011-2013 Jim Martens
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
 * @copyright	2011-2013 Jim Martens
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
	 * Creates a new TaggedContentList object.
	 * 
	 * @param	\wcf\data\tag\Tag	$tag
	 */
	public function __construct(Tag $tag) {
		parent::__construct();
		
		$this->getConditionBuilder()->add('tag_to_object.objectTypeID = ? AND tag_to_object.languageID = ? AND tag_to_object.tagID = ?', array(TagEngine::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.content'), $tag->languageID, $tag->tagID));
		$this->getConditionBuilder()->add('content.contentID = tag_to_object.objectID');
		$this->getConditionBuilder()->add('(content.visibility = ? OR (content.visibility = ? AND content.authorID = ?) OR (content.visibility = ? AND groupToContent.groupID IN (?)))', array('public', 'private', WCF::getUser()->__get('userID'), 'protected', WCF::getUser()->getGroupIDs()));
		$this->sqlConditionJoins .= 'LEFT JOIN ultimate1_user_group_to_content groupToContent ON (groupToContent.contentID = content.contentID)';
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
		parent::readObjects();
		$pageIDs = array_flip(ContentPageCacheBuilder::getInstance()->getData(array(), 'contentIDToPageID'));
		$pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
		foreach ($this->objects as $objectID => &$object) {
			if (isset($pageIDs[$objectID])) {
				$object->page = $pages[$pageIDs[$objectID]];
			}
		}
	}
}
