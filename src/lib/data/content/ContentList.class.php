<?php
/**
 * Contains the content data model list class.
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
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use wcf\data\DatabaseObjectList;
use wcf\system\like\LikeHandler;

/**
 * Represents a list of contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class ContentList extends DatabaseObjectList {
	/**
	 * The class name.
	 * @var	string
	 */
	public $className = '\ultimate\data\content\Content';
	
	/**
	 * Initializes a ContentList object.
	 */
	public function __construct() {
		parent::__construct();
		
		// get like status
		if (!empty($this->sqlSelects)) $this->sqlSelects .= ',';
		$this->sqlSelects .= "like_object.likes, like_object.dislikes";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_like_object like_object ON (like_object.objectTypeID = ".LikeHandler::getInstance()->getObjectType('de.plugins-zum-selberbauen.ultimate.likeableContent')->objectTypeID." AND like_object.objectID = content.contentID)";
	}
}
