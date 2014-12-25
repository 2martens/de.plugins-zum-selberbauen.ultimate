<?php
/**
 * Contains the layout data model editor class.
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
 * @subpackage	data.layout
 * @category	Ultimate CMS
 */
namespace ultimate\data\layout;
use ultimate\system\cache\builder\LayoutCacheBuilder;
use ultimate\system\cache\builder\TemplateCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\WCF;

/**
 * Provides functions to edit layouts.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.layout
 * @category	Ultimate CMS
 */
class LayoutEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * The base class.
	 * @var	string
	 */
	protected static $baseClass = '\ultimate\data\layout\Layout';
	
	/**
	 * Deletes all corresponding objects to the given object IDs.
	 * 
	 * @since	1.0.0
	 * 
	 * @param	integer[]	$objectIDs
	 * @return  integer
	 */
	public static function deleteAll(array $objectIDs = array()) {
		// delete language items
		$sql = 'DELETE FROM wcf'.WCF_N.'_language_item
		        WHERE       languageItem = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
	
		WCF::getDB()->beginTransaction();
		foreach ($objectIDs as $objectID) {
			$statement->execute(array('ultimate.layout.'.$objectID.'.%'));
		}
		WCF::getDB()->commitTransaction();
		return parent::deleteAll($objectIDs);
	}
	
	/**
	 * Assigns a new template.
	 * 
	 * @since		1.0.0
	 * @internal	Calls removeTemplate.
	 * 
	 * @param	integer	$templateID
	 */
	public function assignTemplate($templateID) {
		// makes sure that a new template can be assigned
		$this->removeTemplate();
		
		$sql = 'INSERT INTO ultimate'.WCF_N.'_template_to_layout
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
		$sql = 'DELETE FROM ultimate'.WCF_N.'_template_to_layout
		        WHERE       layoutID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->__get('layoutID')));
	}
	
	/**
	 * Resets the cache.
	 * 
	 * @since	1.0.0
	 */
	public static function resetCache() {
		LayoutCacheBuilder::getInstance()->reset();
		TemplateCacheBuilder::getInstance()->reset();
	}
}
