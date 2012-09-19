<?php
namespace ultimate\data\layout;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\CacheHandler;
use wcf\system\WCF;

/**
 * Provides functions to edit layouts.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.layout
 * @category	Ultimate CMS
 */
class LayoutEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObjectDecorator.html#$baseClass
	 */
	protected static $baseClass = '\ultimate\data\layout\Layout';
	
	/**
	 * @since	1.0.0
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableObject.html#deleteAll
	 */
	public static function deleteAll(array $objectIDs = array()) {
		// delete language items
		$sql = 'DELETE FROM wcf'.WCF_N.'_language_item
		        WHERE       languageItem = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
	
		WCF::getDB()->beginTransaction();
		foreach ($objectIDs as $objectID) {
			$statement->executeUnbuffered(array('ultimate.layout.'.$objectID.'.%'));
		}
		WCF::getDB()->commitTransaction();
		return parent::deleteAll($objectIDs);
	}
	
	/**
	 * Assigns a new template.
	 * 
	 * @since	1.0.0
	 * @internal	Calls removeTemplate.
	 * 
	 * @param	integer	$templateID
	 */
	public function assignTemplate($templateID) {
		// makes sure that a new template can be assigned
		$this->removeTemplate();
		
		$sql = 'INSERT INTO ultimate'.ULTIMATE_N.'_template_to_layout
		               (layoutID, templateID)
		        VALUES (?, ?)';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->__get('layoutID'), intval($templateID)));
	}
	
	/**
	 * Removes an assigned template.
	 * 
	 * @since	1.0.0
	 */
	public function removeTemplate() {
		$sql = 'DELETE FROM ultimate'.ULTIMATE_N.'_template_to_layout
		        WHERE       layoutID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->__get('layoutID')));
	}
	
	/**
	 * @since	1.0.0
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableCachedObject.html#resetCache
	 */
	public static function resetCache() {
		CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.layout.php');
		CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.template-to-layout.php');
	}
}
