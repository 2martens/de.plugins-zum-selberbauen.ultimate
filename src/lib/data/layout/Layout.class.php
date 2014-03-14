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
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\system\WCF;

/**
 * Represents a layout entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.layout
 * @category	Ultimate CMS
 * 
 * @property-read	integer									$layoutID
 * @property-read	integer									$objectID
 * @property-read	string									$objectType
 * @property-read	\ultimate\data\template\Template|NULL	$template
 */
class Layout extends AbstractUltimateDatabaseObject {
	/**
	 * The database table name.
	 * @var string
	 */
	protected static $databaseTableName = 'layout';
	
	/**
	 * If true, the database table index is used as identity.
	 * @var	boolean
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * The database table index name.
	 * @var	string
	 */
	protected static $databaseTableIndexName = 'layoutID';
	
	/**
	 * Returns the assigned template.
	 * 
	 * @return \wcf\data\DatabaseObject|NULL
	 */
	protected function getTemplate() {
		$sql = 'SELECT    template.*
		        FROM      ultimate'.WCF_N.'_template_to_layout templateToLayout
		        LEFT JOIN ultimate'.WCF_N.'_template template
		        ON        (templateToLayout.templateID = template.templateID)
		        WHERE     templateToLayout.layoutID    = ?
		        LIMIT     1';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->layoutID));
		return $statement->fetchObject('\ultimate\data\template\Template');
	}
	
	/**
	 * Handles data.
	 * 
	 * @param	array	$data
	 */
	protected function handleData($data) {
		$data['layoutID'] = intval($data['layoutID']);
		$data['objectID'] = intval($data['objectID']);
		parent::handleData($data);
		$this->data['template'] = $this->getTemplate();
	}
}
