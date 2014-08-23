<?php
/**
 * Contains the content data model editor class.
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
use ultimate\data\content\language\ContentLanguageEntryCache;
use ultimate\data\content\language\ContentLanguageEntryEditor;
use ultimate\data\content\version\ContentVersionCache;
use ultimate\data\layout\LayoutAction;
use ultimate\data\page\PageAction;
use ultimate\system\cache\builder\ContentAttachmentCacheBuilder;
use ultimate\system\cache\builder\ContentCacheBuilder;
use ultimate\system\cache\builder\ContentCategoryCacheBuilder;
use ultimate\system\cache\builder\ContentPageCacheBuilder;
use ultimate\system\cache\builder\ContentTagCacheBuilder;
use ultimate\system\cache\builder\ContentTagCloudCacheBuilder;
use ultimate\system\cache\builder\LatestContentsCacheBuilder;
use ultimate\system\cache\builder\LayoutCacheBuilder;
use ultimate\system\layout\LayoutHandler;
use wcf\data\AbstractVersionableDatabaseObjectEditor;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\attachment\AttachmentHandler;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Provides functions to edit content.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class ContentEditor extends AbstractVersionableDatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * The base class.
	 * @var	string
	 */
	protected static $baseClass = '\ultimate\data\content\Content';
	
	/**
	 * Creates an object with the given parameters.
	 * 
	 * @param	array	$parameters
	 * @return  \ultimate\data\content\Content
	 */
	public static function create(array $parameters = array()) {
		$content = parent::create($parameters);
		$parameters = array(
			'data' => array(
				'objectID' => $content->__get('contentID'),
				'objectType' => 'content'
			)
		);
		$layoutAction = new LayoutAction(array(), 'create', $parameters);
		$layoutAction->executeAction();
		return $content;
	}
	
	/**
	 * Deletes all corresponding objects to the given object IDs.
	 * 
	 * @param	integer[]	$objectIDs	contentIDs
	 * @return  integer
	 */
	public static function deleteAll(array $objectIDs = array()) {
		// unmark contents
		ClipboardHandler::getInstance()->unmark($objectIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.content'));

		// delete attachments
		AttachmentHandler::removeAttachments('de.plugins-zum-selberbauen.ultimate.content', $objectIDs);
		
		// delete language items and tags
		foreach ($objectIDs as $objectID) {
			ContentLanguageEntryEditor::deleteEntries($objectID);
			TagEngine::getInstance()->deleteObjectTags('de.plugins-zum-selberbauen.ultimate.content', $objectID);
		}

		// delete meta data
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('objectID IN (?)', array($objectIDs));
		$conditionBuilder->add('objectType = ?', array('content'));
		$sql = 'DELETE FROM ultimate'.WCF_N.'_meta
			    '.$conditionBuilder->__toString();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		
		// delete associated pages
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('contentID IN (?)', array($objectIDs));
		$sql = 'SELECT pageID
		        FROM   ultimate'.WCF_N.'_content_to_page
		        '.$conditionBuilder->__toString();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		$pageIDs = array();
		while ($row = $statement->fetchArray()) {
			$pageIDs[] = intval($row['pageID']);
		}
		// checks if $pageIDs is filled, if not an exception would occur
		if (empty($pageIDs)) return parent::deleteAll($objectIDs);
		
		$pageAction = new PageAction($pageIDs, 'delete');
		$pageAction->executeAction();
		
		return parent::deleteAll($objectIDs);
	}
	
	/**
	 * Deletes this object.
	 */
	public function delete() {
		/* @var $layout \ultimate\data\layout\Layout */
		$layout = LayoutHandler::getInstance()->getLayoutFromObjectData($this->__get('contentID'), 'content');
		$layoutAction = new LayoutAction(array($layout->__get('layoutID')), 'delete', array());
		$layoutAction->executeAction();
		parent::delete();
	}
	
	/**
	 * Adds the content to the specified categories.
	 * 
	 * @param	array	$categoryIDs
	 * @param	boolean	$deleteOldCategories
	 */
	public function addToCategories(array $categoryIDs, $deleteOldCategories = true) {
		// remove old categores
		if ($deleteOldCategories) {
			$sql = "DELETE FROM	ultimate".WCF_N."_content_to_category
			        WHERE       contentID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$this->__get('contentID')
			));
		}
		
		// insert new categories
		if (!empty($categoryIDs)) {
			$sql = "INSERT INTO ultimate".WCF_N."_content_to_category
			               (contentID, categoryID)
			        VALUES (?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			WCF::getDB()->beginTransaction();
			foreach ($categoryIDs as $categoryID) {
				$statement->executeUnbuffered(array(
					$this->__get('contentID'),
					$categoryID
				));
			}
			WCF::getDB()->commitTransaction();
		}
	}
	
	/**
	 * Adds the content to the specified category.
	 * 
	 * @param	integer	$categoryID
	 */
	public function addToCategory($categoryID) {
		$sql = "SELECT   COUNT(*) AS count
		        FROM     ultimate".WCF_N."_content_to_category
		        WHERE    contentID  = ?
		        AND      categoryID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->__get('contentID'),
			$categoryID
		));
		$row = $statement->fetchArray();
		
		if (!$row['count']) {
			$sql = "INSERT INTO	ultimate".WCF_N."_content_to_category
			               (contentID, categoryID)
			        VALUES (?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$this->__get('contentID'),
				$categoryID
			));
		}
	}
	
	/**
	 * Removes the content from the specified category.
	 * 
	 * @param	integer	$categoryID
	 */
	public function removeFromCategory($categoryID) {
		$sql = "DELETE FROM	ultimate".WCF_N."_content_to_category
		        WHERE       contentID  = ?
		        AND         categoryID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->__get('contentID'),
			$categoryID
		));
	}
	
	/**
	 * Removes the content from multiple categories.
	 * 
	 * @param	array	$categoryIDs
	 */
	public function removeFromCategories(array $categoryIDs) {
		$sql = "DELETE FROM	ultimate".WCF_N."_content_to_category
		        WHERE       contentID  = ?
		        AND         categoryID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($categoryIDs as $categoryID) {
			$statement->executeUnbuffered(array(
				$this->object->__get('contentID'),
				$categoryID
			));
		}
		WCF::getDB()->commitTransaction();
	}
	
	/**
	 * Adds meta data for this content.
	 * 
	 * @param	string	$metaDescription
	 * @param	string	$metaKeywords
	 */
	public function addMetaData($metaDescription, $metaKeywords) {
		$metaDescription = StringUtil::trim($metaDescription);
		$metaKeywords = StringUtil::trim($metaKeywords);
		if (empty($metaDescription) && empty($metaKeywords)) {
			return;
		}
		$sql = 'REPLACE INTO ultimate'.WCF_N.'_meta
		               (objectID, objectType, metaDescription, metaKeywords)
		        VALUES (?, ?, ?, ?)';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->object->__get('contentID'),
			'content',
			$metaDescription,
			$metaKeywords
		));
	}
	
	/**
	 * Adds new groups to the given content version.
	 * 
	 * @param	integer	$versionID
	 * @param	array	$groupIDs
	 * @param	boolean	$deleteOldGroups
	 */
	public function addGroups($versionID, array $groupIDs, $deleteOldGroups = true) {
		if ($deleteOldGroups) {
			$sql = 'DELETE FROM ultimate'.WCF_N.'_user_group_to_content_version
			        WHERE       versionID = ?';
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$versionID
			));
		}
		$sql = 'INSERT INTO ultimate'.WCF_N.'_user_group_to_content_version
		               (groupID, versionID)
		        VALUES (?, ?, ?)';
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($groupIDs as $groupID) {
			$statement->executeUnbuffered(array(
				$groupID,
				$versionID
			));
		}
		WCF::getDB()->commitTransaction();
	}

	/**
	 * @see	\wcf\data\IStorableObject::getDatabaseTableName()
	 */
	public static function getDatabaseTableName() {
		return call_user_func(array(static::$baseClass, 'getDatabaseTableName'));
	}
	
	/**
	 * Resets the cache.
	 */
	public static function resetCache() {
		ContentAttachmentCacheBuilder::getInstance()->reset();
		ContentCacheBuilder::getInstance()->reset();
		ContentCategoryCacheBuilder::getInstance()->reset();
		ContentLanguageEntryCache::getInstance()->reloadCache();
		ContentPageCacheBuilder::getInstance()->reset();
		ContentTagCacheBuilder::getInstance()->reset();
		ContentTagCloudCacheBuilder::getInstance()->reset();
		ContentVersionCache::getInstance()->reloadCache();
		LayoutCacheBuilder::getInstance()->reset();
		LatestContentsCacheBuilder::getInstance()->reset();
	}
}
