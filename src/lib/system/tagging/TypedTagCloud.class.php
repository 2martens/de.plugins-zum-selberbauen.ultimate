<?php
/**
 * Contains the TypedTagCloud class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.tagging
 * @category	Ultimate CMS
 */
namespace ultimate\system\tagging;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\tagging\TagCloud;
use wcf\system\WCF;

/**
 * Overwrites the original TypedTagCloud constructor.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.tagging
 * @category	Ultimate CMS
 */
class TypedTagCloud extends \wcf\system\tagging\TypedTagCloud {
	/**
	 * Contructs a new TypedTagCloud object.
	 *
	 * @param	string	$objectType
	 */
	public function __construct($objectType) {
		$objectTypeObj = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.tagging.taggableObject', $objectType);
		$this->objectTypeIDs[] = $objectTypeObj->objectTypeID;
		
		$languageIDs = array(WCF::getLanguage()->__get('languageID'));
		
		TagCloud::__construct($languageIDs);
	}
}
