<?php
namespace ultimate\system\event\listener;
use wcf\system\event\IEventListener;

/**
 * Manages the creation of a custom query.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
class BlockActionContentBlockTypeListener implements IEventListener {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.event.IEventListener.html#execute
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
		        FROM     ultimate'.ULTIMATE_N.'_content
		        ORDER BY ? ?';
		$parameters = array(
			$sortField,
			$sortOrder
		);
		$eventObj->parametersAction['data']['query'] = $sql;
		$eventObj->parametersAction['data']['parameters'] = serialize($parameters);
	}
}
