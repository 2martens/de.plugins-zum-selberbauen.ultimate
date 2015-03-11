<?php
/**
 * Contains the blockType data model class.
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
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.blocktype
 * @category	Ultimate CMS
 */
namespace ultimate\data\blocktype;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;

/**
 * Represents a blockType entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.ultimate.blockType
 * @category	Ultimate CMS
 * 
 * @property-read	integer	$blockTypeID
 * @property-read	integer	$packageID
 * @property-read	string	$blockTypeName
 * @property-read	string	$blockTypeClassName
 * @property-read	string	$cssIdentifier
 */
class BlockType extends AbstractUltimateDatabaseObject implements ITitledObject {
	/**
	 * The database table name.
	 * @var string
	 */
	protected static $databaseTableName = 'blocktype';
	
	/**
	 * If true, the database table index is used as identity.
	 * @var	boolean
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * The database table index name.
	 * @var	string
	 */
	protected static $databaseTableIndexName = 'blockTypeID';
	
	/**
	 * Returns the language interpreted name of this blockType.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->get($this->blockTypeName);
	}
	
	/**
	 * Returns the raw name of this block type.
	 * 
	 * For a language interpreted version, please refer to the magic toString method.
	 * 
	 * @return	string
	 * @see		\ultimate\data\blocktype\BlockType::__toString()
	 */
	public function getTitle() {
		return $this->blockTypeName;
	}
	
	/**
	 * Handles data.
	 * 
	 * @param	array	$data
	 */
	protected function handleData($data) {
		$data['cssIdentifier'] = mb_strtolower($data['blockTypeName']);
		parent::handleData($data);
	}
}
