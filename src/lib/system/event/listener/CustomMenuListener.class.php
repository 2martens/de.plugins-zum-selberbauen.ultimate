<?php
/**
 * Contains the CustomMenuListener class.
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
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
namespace ultimate\system\event\listener;
use ultimate\system\menu\custom\CustomMenu;
use ultimate\system\template\TemplateHandler;
use wcf\system\event\IEventListener;
use wcf\system\menu\page\PageMenu;
use wcf\system\request\RequestHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Manages the display of the CustomMenu on WCF and other non-Ultimate sites.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
class CustomMenuListener implements IEventListener {
	/**
	 * Executes this listener.
	 * 
	 * @param	object	$eventObj
	 * @param	string	$className
	 * @param	string	$eventName
	 */
	public function execute($eventObj, $className, $eventName) {
		// rules out all ultimate classes and all ACP requests
		if (mb_strpos($className, 'ultimate') !== false) return;
		if (RequestHandler::getInstance()->isACPRequest()) return;
		
		$activeMenuItems = PageMenu::getInstance()->getActiveMenuItems();
		if (empty($activeMenuItems)) return;
		
		$activeMenuItem = $activeMenuItems[0];
		
		TemplateHandler::getInstance()->initiateCustomMenu();
		CustomMenu::getInstance()->setActiveMenuItem($activeMenuItem);
		
		$assignCustomMenu = true;
		
		if (CustomMenu::getInstance()->getActiveMenuItem() === null) {
			$assignCustomMenu = false;
		}
		
		if ($assignCustomMenu) {
			$customMenu = '<script data-relocate="true" type="text/javascript">
			//<![CDATA[
				'."var customMenu = '".StringUtil::encodeJS(WCF::getTPL()->fetch('__customMenu', 'ultimate'))."';
				var customMenuSubMenu = '".StringUtil::encodeJS(WCF::getTPL()->fetch('__customMenuSubMenu', 'ultimate'))."';".'
			//]]>
			</script>';
			
			WCF::getTPL()->assign(array(
				'customMenu' => $customMenu
			));
		}
	}
}
