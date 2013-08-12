<?php
/**
 * Contains the BlockActionContentBlockTypeListener class.
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
namespace ultimate\system\event\listener;
use wcf\system\event\IEventListener;

/**
 * Manages the creation of a custom query.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
class BlockActionContentBlockTypeListener implements IEventListener {
	/**
	 * Executes this listener.
	 * 
	 * @param	object	$eventObj
	 * @param	string	$className
	 * @param	string	$eventName
	 */
	public function execute($eventObj, $className, $eventName) {
		// determine block type
		$blockType = $eventObj->parameters['blockOrigin']['blockType'];
		// we only need content blocks
		if (strtolower($blockType) != 'content') return; 
		
		// determine sort field
		$additionalData = unserialize($eventObj->parametersAction['data']['additionalData']);
		$sortField = $additionalData['sortField'];
		$sortOrder = $additionalData['sortOrder'];
		// if the sort field is identical, we don't have to change anything
		if ($sortField == ULTIMATE_SORT_CONTENT_SORTFIELD) return;
		// create custom query
		$sql = 'SELECT   *
		        FROM     ultimate'.WCF_N.'_content
		        ORDER BY ? ?';
		$parameters = array(
			$sortField,
			$sortOrder
		);
		$eventObj->parametersAction['data']['query'] = $sql;
		$eventObj->parametersAction['data']['parameters'] = serialize($parameters);
	}
}
