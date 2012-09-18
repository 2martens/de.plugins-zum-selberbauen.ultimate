<?php
namespace ultimate\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\system\language\I18nHandler;

/**
 * Adds i18n options.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
class BlockActionI18nListener implements IEventListener {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.event.IEventListener.html#execute
	 */
	public function execute($eventObj, $className, $eventName) {
		/* @var $eventObj \ultimate\action\BlockAction */
		// content block type
		$eventObj->i18nOptions['readMoreText'] = 'content';
		$eventObj->i18nOptions['metaAboveContent'] = 'content';
		$eventObj->i18nOptions['metaBelowContent'] = 'content';
		
		I18nHandler::getInstance()->register('readMoreText');
		I18nHandler::getInstance()->register('metaAboveContent');
		I18nHandler::getInstance()->register('metaBelowContent');
	}
}
