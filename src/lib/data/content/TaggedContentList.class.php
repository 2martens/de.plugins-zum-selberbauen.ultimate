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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use wcf\system\like\LikeHandler;

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
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObjectList.html#$decoratorClassName
	 */
	public $decoratorClassName = '\ultimate\data\content\TaggedContent';
	
	/**
	 * Creates a new TaggedThreadList object.
	 */
	public function __construct(Tag $tag) {
		parent::__construct();
		
		$this->getConditionBuilder()->add('tag_to_object.objectTypeID = ? AND tag_to_object.languageID = ? AND tag_to_object.tagID = ?', array(TagEngine::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.content'), $tag->languageID, $tag->tagID));
		$this->getConditionBuilder()->add('content.contentID = tag_to_object.objectID');
		
		// get like status
		if (!empty($this->sqlSelects)) $this->sqlSelects .= ',';
		$this->sqlSelects .= "like_object.likes, like_object.dislikes";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_like_object like_object ON (like_object.objectTypeID = ".LikeHandler::getInstance()->getObjectType('de.plugins-zum-selberbauen.ultimate.likeableContent')->objectTypeID." AND like_object.objectID = content.contentID)";
	}
	
	/**
	 * @see	\wcf\data\DatabaseObjectList::countObjects()
	 */
	public function countObjects() {
		$sql = 'SELECT COUNT(*) AS count
		        FROM   wcf'.WCF_N.'_tag_to_object tag_to_object,
		               ultimate'.WCF_N.'_content content,
		               ultimate'.WCF_N.'_content_to_page content_to_page
		        '.$this->sqlConditionJoins.'
		        '.$this->getConditionBuilder();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($this->getConditionBuilder()->getParameters());
		$row = $statement->fetchArray();
		return $row['count'];
	}
	
	/**
	 * @see	\wcf\data\DatabaseObjectList::readObjectIDs()
	 */
	public function readObjectIDs() {
		$this->objectIDs = array();
		$sql = 'SELECT tag_to_object.objectID
		        FROM   wcf'.WCF_N.'_tag_to_object tag_to_object,
		               ultimate'.WCF_N.'_content content,
		               ultimate'.WCF_N.'_content_to_page content_to_page
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
	 * @see	\wcf\data\DatabaseObjectList::readObjects()
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
