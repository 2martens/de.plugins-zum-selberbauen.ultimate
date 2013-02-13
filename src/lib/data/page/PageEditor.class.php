<?php
/**
 * Contains the page data model editor class.
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
 * @subpackage	data.page
 * @category	Ultimate CMS
 */
namespace ultimate\data\page;
use ultimate\system\cache\builder\ContentPageCacheBuilder;
use ultimate\system\cache\builder\PageCacheBuilder;
use ultimate\data\layout\LayoutAction;
use ultimate\system\layout\LayoutHandler;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\WCF;

/**
 * Provides functions to edit pages.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.page
 * @category	Ultimate CMS
 */
class PageEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObjectDecorator.html#$baseClass
	 */
	protected static $baseClass = '\ultimate\data\page\Page';
	
	/**
	 * @see \wcf\data\IEditableObject::create()
	 */
	public static function create(array $parameters = array()) {
		$page = parent::create($parameters);
		$parameters = array(
			'data' => array(
				'objectID' => $page->__get('pageID'),
				'objectType' => 'page'
			)
		);
		$layoutAction = new LayoutAction(array(), 'create', $parameters);
		$layoutAction->executeAction();
		return $page;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableObject.html#deleteAll
	 */
	public static function deleteAll(array $objectIDs = array()) {
		// unmark contents
		ClipboardHandler::getInstance()->unmark($objectIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.page'));
		
		// delete language items
		$sql = 'DELETE FROM wcf'.WCF_N.'_language_item
		        WHERE       languageItem = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		
		WCF::getDB()->beginTransaction();
		foreach ($objectIDs as $objectID) {
			$statement->executeUnbuffered(array('ultimate.page.'.$objectID.'.%'));
		}
		WCF::getDB()->commitTransaction();
		
		return parent::deleteAll($objectIDs);
	}
	
	/**
	 * @see \wcf\data\IEditableObject::delete()
	 */
	public function delete() {
		/* @var $layout \ultimate\data\layout\Layout */
		$layout = LayoutHandler::getInstance()->getLayoutFromObjectData($this->__get('pageID'), $this->__get('pageTitle'));
		$layoutAction = new LayoutAction(array($layout->__get('layoutID')), 'delete', array());
		$layoutAction->executeAction();
		parent::delete();
	}
	
	/**
	 * Adds new groups to this page.
	 * 
	 * @param	array	$groupIDs
	 * @param	boolean	$replaceOldGroups
	 */
	public function addGroups(array $groupIDs, $deleteOldGroups = true) {
		if ($deleteOldGroups) {
			$sql = 'DELETE FROM ultimate'.WCF_N.'_user_group_to_page
			        WHERE       pageID = ?';
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
					$this->object->__get('pageID')
			));
		}
		$sql = 'INSERT INTO ultimate'.WCF_N.'_user_group_to_page
		               (groupID, pageID)
		        VALUES (?, ?)';
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($groupIDs as $groupID) {
			$statement->executeUnbuffered(array(
				$groupID,
				$this->object->__get('pageID')
			));
		}
		WCF::getDB()->commitTransaction();
	}
	
	/**
	 * Adds the specified content to this page.
	 * 
	 * @param	integer	$contentID
	 * @param	boolean	$replaceOldContent
	 */
	public function addContent($contentID, $replaceOldContent = true) {
		if ($replaceOldContent) {
			$sql = 'UPDATE ultimate'.WCF_N.'_content_to_page
			        SET    contentID = ?
			        WHERE  pageID    = ?';
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$contentID,
				$this->pageID
			));
		}
		else {
			$sql = 'INSERT INTO ultimate'.WCF_N.'_content_to_page
			               (contentID, pageID)
			        VALUES (?, ?)';
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$contentID,
				$this->pageID
			));
		}
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableCachedObject.html#resetCache
	 */
	public static function resetCache() {
		PageCacheBuilder::getInstance()->reset();
		ContentPageCacheBuilder::getInstance()->reset();
	}
}
