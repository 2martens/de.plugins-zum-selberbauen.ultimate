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
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.page
 * @category	Ultimate CMS
 */
namespace ultimate\data\page;
use ultimate\data\AbstractUltimateDatabaseObject;
use ultimate\system\page\PagePermissionHandler;
use wcf\data\user\User;
use wcf\data\ITitledObject;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Represents a page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
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
 * @property-read	string[]							$metaData	('metaDescription' => metaDescription, 'metaKeywords' => metaKeywords)
 * @property-read	\ultimate\data\content\Content		$content
 * @property-read	\ultimate\data\page\Page[]			$childPages
 * @property-read	\wcf\data\user\group\UserGroup[]	$groups
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
	 * True, if the current content is visible for the current user.
	 * @var boolean
	 */
	private $isVisible = null;
	
	/**
	 * Returns the content of this page.
	 * 
	 * @return	\ultimate\data\content\Content|null
	 */
	public function getContent() {
		if (!isset($this->content)) {
			$sql = 'SELECT	  content.*
			        FROM      ultimate'.WCF_N.'_'.$this->contentPageTable.' contentToPage
			        LEFT JOIN ultimate'.WCF_N.'_content content
			        ON        (content.contentID = contentToPage.contentID)
			        WHERE     contentToPage.pageID = ?';
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->pageID));
			
			$this->data['content'] = $statement->fetchObject('\ultimate\data\content\Content');
		}
		return $this->content;
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
		if (!isset($this->childPages)) {
			$sql = 'SELECT	*
			        FROM    '.self::getDatabaseTableName().'
			        WHERE   pageParent = ?';
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->pageID));
			
			$childPages = array();
			while ($page = $statement->fetchObject(get_class($this))) {
				$childPages[$page->pageID] = $page;
			}
			$this->data['childPages'] = $childPages;
		}
		return $this->childPages;
	}
	
	/**
	 * Returns the title of this page.
	 * 
	 * @deprecated Use getTitle()
	 * @return	string
	 */
	public function getLangTitle() {
		return $this->getTitle();
	}
	
	/**
	 * Returns the title of this page.
	 *
	 * @return	string
	 */
	public function __toString() {
		return $this->getTitle();
	}
	
	/**
	 * Checks if the current user can see this page.
	 *
	 * @return boolean
	 */
	public function isVisible() {
		if ($this->isVisible === null) {
			$content = $this->getContent();
			$isVisible = $content->isVisible();
			$this->isVisible = $isVisible;
		}
		if ($this->isVisible) {
			$this->isVisible = PagePermissionHandler::getInstance()->getPermission($this->pageID, 'canSeePage');
		}
		
		return $this->isVisible;
	}
	
	/**
	 * @see \wcf\data\DatabaseObject::__get()
	 */
	public function __get($name) {
		$result = parent::__get($name);
		if ($result === null && in_array($name, array('content', 'childPages'))) {
			switch ($name) {
				case 'content':
					$result = $this->getContent();
					break;
				case 'childPages':
					$result = $this->getChildPages();
					break;
			}
		}
		
		return $result;
	}
	
	/**
	 * Handles data.
	 * 
	 * @param	array	$data
	 */
	protected function handleData($data) {
		if (!isset($data['pageID'])) {
			parent::handleData($data);
			return;
		}
		
		$data['pageID'] = intval($data['pageID']);
		$data['pageParent'] = intval($data['pageParent']);
		$data['authorID'] = intval($data['authorID']);
		$data['author'] = new User($data['authorID']);
		$data['publishDate'] = intval($data['publishDate']);
		$data['publishDateObject'] = DateUtil::getDateTimeByTimestamp($data['publishDate']);
		$data['lastModified'] = intval($data['lastModified']);
		$data['status'] = intval($data['status']);
		parent::handleData($data);
		$this->data['metaData'] = $this->getMetaData($this->pageID, 'page');
	}
}
