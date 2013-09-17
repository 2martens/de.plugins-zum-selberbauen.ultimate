<?php
/**
 * Contains the link data model class.
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.link
 * @category	Ultimate CMS
 */
namespace ultimate\data\link;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a link.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.link
 * @category	Ultimate CMS
 * 
 * @property-read	integer	$linkID
 * @property-read	string	$linkName
 * @property-read	string	$linkDescription
 * @property-read	string	$linkURL
 */
class Link extends AbstractUltimateDatabaseObject implements ITitledObject {
	/**
	 * The database table name.
	 * @var string
	 */
	protected static $databaseTableName = 'link';
	
	/**
	 * If true, the database table index is used as identity.
	 * @var	boolean
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * The database table index name.
	 * @var	string
	 */
	protected static $databaseTableIndexName = 'linkID';
	
	/**
	 * Returns the language interpreted name of this link.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->get($this->linkName);
	}
	
	/**
	 * Returns the raw version of the name of this link.
	 * 
	 * For a language interpreted version of the name, refer to the magic toString method.
	 * 
	 * @return	string
	 * @see		\ultimate\data\layout\Layout::__toString()
	 */
	public function getTitle() {
		return $this->linkName;
	}
	
	/**
	 * Returns the anchor tag for this link.
	 * 
	 * @return string
	 */
	public function getAnchorTag() {
		return StringUtil::getAnchorTag($this->linkURL, $this->__toString());
	}
	
	/**
	 * Handles data.
	 * 
	 * @param	array	$data
	 */
	protected function handleData($data) {
		$data['linkID'] = intval($data['linkID']);
		parent::handleData($data);
	}
}
