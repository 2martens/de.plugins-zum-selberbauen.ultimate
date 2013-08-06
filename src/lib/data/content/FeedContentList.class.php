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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}.
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

/**
 * Represents a list of feed contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class FeedContentList extends ContentList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$sqlOrderBy
	 */
	public $sqlOrderBy = 'content.publishDate DESC';
	
	/**
	 * @see	\wcf\data\DatabaseObjectList::$decoratorClassName
	 */
	public $decoratorClassName = 'ultimate\data\content\FeedContent';
	
	/**
	 * Contains the ids of the categories the contents should be in.
	 * @var integer[]
	 */
	protected $categoryIDs = array();
	
	/**
	 * Initializes a new FeedContentList object.
	 *
	 * @param	integer[]	$boardIDs
	 */
	public function __construct(array $categoryIDs) {
		parent::__construct();
		$pageContentIDs = array_values(ContentPageCacheBuilder::getInstance()->getData(array(), 'contentIDToPageID'));
		$this->getConditionBuilder()->add('content.status = 3');
		$this->getConditionBuilder()->add('content.contentID NOT IN (?)', array($pageContentIDs));
		$this->categoryIDs = $categoryIDs;
	}
	
	/**
	 * @see \wcf\data\DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		if (empty($this->objectIDs)) $this->readObjectIDs();
		parent::readObjects();
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
