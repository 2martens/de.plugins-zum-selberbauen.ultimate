<?php
/**
 * Contains the page data model class.
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
 * @subpackage	data.page
 * @category	Ultimate CMS
 */
namespace ultimate\data\page;
use ultimate\data\content\Content;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\data\user\group\UserGroup;
use wcf\data\user\User;
use wcf\data\ITitledObject;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Represents a page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.page
 * @category	Ultimate CMS
 * 
 * @property-read	integer								$pageID
 * @property-read	integer								$authorID
 * @property-read	\wcf\data\user\User					$author
 * @property-read	integer								$pageParent
 * @property-read	string								$pageTitle
 * @property-read	string								$pageSlug
 * @property-read	integer								$publishDate
 * @property-read	\DateTime							$publishDateObject
 * @property-read	integer								$lastModified
 * @property-read	integer								$status	(0, 1, 2, 3)
 * @property-read	string								$visibility	('public', 'protected', 'private')
 * @property-read	\wcf\data\user\group\UserGroup[]	$groups	(groupID => group)
 * @property-read	\ultimate\data\page\Page[]			$childPages	(pageID => page)
 * @property-read	string[]							$metaData	('metaDescription' => metaDescription, 'metaKeywords' => metaKeywords)
 * @property-read	\ultimate\data\content\Content		$content
 */
class Page extends AbstractUltimateDatabaseObject implements ITitledObject {
	/**
	 * The database table name.
	 * @var	string
	 */
	protected static $databaseTableName = 'page';
	
	/**
	 * If true, the database table index is used as identity.
	 * @var	boolean
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * The database table index name.
	 * @var	string
	 */
	protected static $databaseTableIndexName = 'pageID';
	
	/**
	 * The content to page database table name.
	 * @var	string
	 */
	protected $contentPageTable = 'content_to_page';
	
	/**
	 * Returns the content of this page.
	 * 
	 * @return	\ultimate\data\content\Content|null
	 */
	public function getContent() {
		$sql = 'SELECT	  content.*
		        FROM      ultimate'.WCF_N.'_'.$this->contentPageTable.' contentToPage
		        LEFT JOIN ultimate'.WCF_N.'_content content
		        ON        (content.contentID = contentToPage.contentID)
		        WHERE     contentToPage.pageID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->pageID));
		
		$content = $statement->fetchObject('\ultimate\data\content\Content');
		return $content;
	}
	
	/**
	 * Returns the page title without language interpreting.
	 * 
	 * To use language interpreting, use getLangTitle method.
	 * 
	 * @return	string
	 */
	public function getTitle() {
		return $this->pageTitle;
	}
	
	/**
	 * Returns all child pages of this page.
	 * 
	 * @return	\ultimate\data\page\Page[]
	 */
	public function getChildPages() {
		$sql = 'SELECT	*
		        FROM    '.self::getDatabaseTableName().'
		        WHERE   pageParent = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->pageID));
		
		$childPages = array();
		while ($page = $statement->fetchObject(get_class($this))) {
			$childPages[$page->pageID] = $page;
		}
		return $childPages;
	}
	
	/**
	 * Returns all user groups associated with this page.
	 * 
	 * @return	\wcf\data\user\group\UserGroup[]
	 */
	public function getGroups() {
		$sql = 'SELECT	  groupTable.*
		        FROM      ultimate'.WCF_N.'_user_group_to_page groupToPage
		        LEFT JOIN wcf'.WCF_N.'_user_group groupTable
		        ON        (groupTable.groupID = groupToPage.groupID)
		        WHERE     groupToPage.pageID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->pageID));
		
		$groups = array();
		while ($group = $statement->fetchObject('\wcf\data\user\group\UserGroup')) {
			$groups[$group->__get('groupID')] = $group;
		}
		return $groups;
	}
	
	/**
	 * Returns the title of this page.
	 * 
	 * @return	string
	 */
	public function getLangTitle() {
		return WCF::getLanguage()->get($this->pageTitle);
	}
	
	/**
	 * Returns the title of this page.
	 *
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->get($this->pageTitle);
	}
	
	/**
	 * Checks if the current user can see this page.
	 *
	 * @return boolean
	 */
	public function isVisible() {
		$isVisible = false;
		if ($this->visibility == 'public') {
			$isVisible = true;
		}
		else if ($this->visibility == 'protected') {
			$groupIDs = WCF::getUser()->getGroupIDs();
			$pageGroupIDs = array_keys($this->groups);
			$result = array_intersect($groupIDs, $pageGroupIDs);
			if (!empty($result)) {
				$isVisible = true;
			}
		} else {
			$isVisible = (WCF::getUser()->__get('userID') == $this->authorID);
		}
	
		if ($isVisible) {
			$isVisible = ($this->status == 3);
		}
	
		return $isVisible;
	}
	
	/**
	 * Handles data.
	 * 
	 * @param	array	$data
	 */
	protected function handleData($data) {
		$data['pageID'] = intval($data['pageID']);
		$data['pageParent'] = intval($data['pageParent']);
		$data['authorID'] = intval($data['authorID']);
		$data['author'] = new User($data['authorID']);
		$data['publishDate'] = intval($data['publishDate']);
		$data['publishDateObject'] = DateUtil::getDateTimeByTimestamp($data['publishDate']);
		$data['lastModified'] = intval($data['lastModified']);
		$data['status'] = intval($data['status']);
		parent::handleData($data);
		$this->data['groups'] = $this->getGroups();
		$this->data['childPages'] = $this->getChildPages();
		$this->data['metaData'] = $this->getMetaData($this->pageID, 'page');
		$this->data['content'] = $this->getContent();
	}
}
