<?php
namespace ultimate\data\content;
use ultimate\data\layout\LayoutAction;
use ultimate\system\layout\LayoutHandler;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\CacheHandler;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;

/**
 * Provides functions to edit content.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class ContentEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObjectDecorator.html#$baseClass
	 */
	protected static $baseClass = '\ultimate\data\content\Content';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableObject.html#create
	 */
	public static function create(array $parameters = array()) {
		$content = parent::create($parameters);
		$parameters = array(
			'data' => array(
				'layoutName' => $content->__get('contentTitle')
			)
		);
		$layoutAction = new LayoutAction(array(), 'create', $parameters);
		$layoutAction->executeAction();
		return $content;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableObject.html#deleteAll
	 */
	public static function deleteAll(array $objectIDs = array()) {
		// unmark contents
		ClipboardHandler::getInstance()->unmark($objectIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.content'));
		
		// delete language items
		$sql = 'DELETE FROM wcf'.WCF_N.'_language_item
		        WHERE       languageItem = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		
		WCF::getDB()->beginTransaction();
		foreach ($objectIDs as $objectID) {
			$statement->executeUnbuffered(array('ultimate.content.'.$objectID.'.%'));
			$taggedContent = new TaggedContent(new Content($objectID));
			$languageIDs = array_keys($taggedContent->tags);
			TagEngine::getInstance()->deleteObjectTags($taggedContent, $languageIDs);
		}
		WCF::getDB()->commitTransaction();
		return parent::deleteAll($objectIDs);
	}
	
	/**
	 * @see \wcf\data\IEditableObject::delete()
	 */
	public function delete() {
		/* @var $layout \ultimate\data\layout\Layout */
		$layout = LayoutHandler::getInstance()->getLayoutFromLayoutName($this->__get('contentTitle'));
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
			$sql = "DELETE FROM	ultimate".ULTIMATE_N."_content_to_category
			        WHERE       contentID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$this->__get('contentID')
			));
		}
		
		// insert new categories
		if (!empty($categoryIDs) > 0) {
			$sql = "INSERT INTO	ultimate".ULTIMATE_N."_content_to_category
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
		        FROM     ultimate".ULTIMATE_N."_content_to_category
		        WHERE    contentID  = ?
		        AND      categoryID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->contentID,
			$categoryID
		));
		$row = $statement->fetchArray();
		
		if (!$row['count']) {
			$sql = "INSERT INTO	ultimate".ULTIMATE_N."_content_to_category
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
		$sql = "DELETE FROM	ultimate".ULTIMATE_N."_content_to_category
		        WHERE       contentID  = ?
		        AND         categoryID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->object->__get('contentID'),
			$categoryID
		));
	}
	
	/**
	 * Removes the content from multiple categories.
	 * 
	 * @param	array	$categoryIDs
	 */
	public function removeFromCategories(array $categoryIDs) {
		$sql = "DELETE FROM	ultimate".ULTIMATE_N."_content_to_category
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
	* Adds new groups to this page.
	* 
	* @param	array	$groupIDs
	* @param	boolean	$replaceOldGroups
	*/
	public function addGroups(array $groupIDs, $deleteOldGroups = true) {
		if ($deleteOldGroups) {
			$sql = 'DELETE FROM ultimate'.ULTIMATE_N.'_user_group_to_content
			        WHERE       contentID = ?';
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$this->object->__get('contentID')
			));
		}
		$sql = 'INSERT INTO ultimate'.ULTIMATE_N.'_user_group_to_content
		               (groupID, contentID)
		        VALUES (?, ?)';
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($groupIDs as $groupID) {
			$statement->executeUnbuffered(array(
				$groupID,
				$this->object->__get('contentID')
			));
		}
		WCF::getDB()->commitTransaction();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableCachedObject.html#resetCache
	 */
	public static function resetCache() {
		CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.content.php');
		CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.content-to-category.php');
		CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.content-tag.php');
		CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.content-to-tag.php');
	}
}
