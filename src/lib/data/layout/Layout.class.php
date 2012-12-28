<?php
/**
 * Contains the layout data model class.
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
 * @subpackage	data.layout
 * @category	Ultimate CMS
 */
namespace ultimate\data\layout;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\data\ITitledDatabaseObject;
use wcf\system\WCF;

/**
 * Represents a layout entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.layout
 * @category	Ultimate CMS
 */
class Layout extends AbstractUltimateDatabaseObject implements ITitledDatabaseObject {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableName
	 */
	protected static $databaseTableName = 'layout';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'layoutID';
	
	/**
	 * Returns the title of this layout.
	 * 
	 * @return	string
	 */
	public function getTitle() {
		return $this->__toString();
	}
	
	/**
	 * Returns the title of this layout.
	 *
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->get($this->layoutName);
	}
	
	/**
	 * Returns the assigned template.
	 * 
	 * @return \wcf\data\DatabaseObject|NULL
	 */
	protected function getTemplate() {
		$sql = 'SELECT    template.*
		        FROM      ultimate'.ULTIMATE_N.'_template_to_layout templateToLayout
		        LEFT JOIN ultimate'.ULTIMATE_N.'_template template
		        ON        (templateToLayout.templateID = template.templateID)
		        WHERE     templateToLayout.layoutID    = ?
		        LIMIT     1';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->layoutID));
		return $statement->fetchObject('\ultimate\data\template\Template');
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#handleData
	 */
	protected function handleData($data) {
		$data['layoutID'] = intval($data['layoutID']);
		parent::handleData($data);
		$this->data['template'] = $this->getTemplate();
	}
}
