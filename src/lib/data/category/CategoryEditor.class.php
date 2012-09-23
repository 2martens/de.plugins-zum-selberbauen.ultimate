<?php
namespace ultimate\data\category;
use ultimate\data\layout\LayoutAction;
use ultimate\system\layout\LayoutHandler;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\CacheHandler;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\WCF;

/**
 * Provides functions to edit categories.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.category
 * @category	Ultimate CMS
 */
class CategoryEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObjectDecorator.html#$baseClass
	 */
	protected static $baseClass = '\ultimate\data\category\Category';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableObject.html#create
	 */
	public static function create(array $parameters = array()) {
		$category = parent::create($parameters);
		$parameters = array(
			'data' => array(
				'layoutName' => $category->__get('categoryTitle')
			)
		);
		$layoutAction = new LayoutAction(array(), 'create', $parameters);
		$layoutAction->executeAction();
		return $category;
	}
	
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableObject.html#deleteAll
	 */
	public static function deleteAll(array $objectIDs = array()) {
		// unmark contents
		ClipboardHandler::getInstance()->unmark($objectIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.category'));
		
		// delete language items
		$sql = 'DELETE FROM wcf'.WCF_N.'_language_item
		        WHERE       languageItem = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		
		WCF::getDB()->beginTransaction();
		foreach ($objectIDs as $objectID) {
			$statement->executeUnbuffered(array('ultimate.category.'.$objectID.'.%'));
		}
		WCF::getDB()->commitTransaction();
		
		return parent::deleteAll($objectIDs);
	}
	
	/**
	 * @see \wcf\data\IEditableObject::delete()
	 */
	public function delete() {
		/* @var $layout \ultimate\data\layout\Layout */
		$layout = LayoutHandler::getInstance()->getLayoutFromLayoutName($this->object->getTitle());
		$layoutAction = new LayoutAction(array($layout->__get('layoutID')), 'delete', array());
		$layoutAction->executeAction();
		parent::delete();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableCachedObject.html#resetCache
	 */
	public static function resetCache() {
		CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.category.php');
	}
}
