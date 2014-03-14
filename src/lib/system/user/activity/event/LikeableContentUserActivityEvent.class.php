<?php
/**
 * Contains the LikeableContentUserActivityEvent class.
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
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.activity.event
 * @category	Ultimate CMS
 */
namespace ultimate\system\user\activity\event;
use ultimate\system\cache\builder\ContentCacheBuilder;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * User activity event implementation for liked contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.activity.event
 * @category	Ultimate CMS
 */
class LikeableContentUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * Prepares a list of events for output.
	 * 
	 * @param \wcf\data\user\activity\event\ViewableUserActivityEvent[] events
	 * 
	 * @see	\wcf\system\user\activity\event\IUserActivityEvent::prepare()
	 */
	public function prepare(array $events) {
		$contentIDs = array();
		foreach ($events as $event) {
			$contentIDs[] = $event->objectID;
		}
	
		// fetch contents
		$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		$remainingContents = array();
		foreach ($contents as $contentID => $content) {
			if (in_array($contentID, $contentIDs)) {
				$remainingContents[$contentID] = $content;
			}
		}
		$contents = $remainingContents;
	
		// set message
		/* @var $event \wcf\data\user\activity\event\ViewableUserActivityEvent  */
		/* @var $content \ultimate\data\content\Content */
		foreach ($events as $event) {
			if (isset($contents[$event->objectID])) {
				$content = $contents[$event->objectID];
	
				// check permissions
				if (!$content->isVisible()) {
					continue;
				}
				$event->setIsAccessible();
	
				// short output
				$text = WCF::getLanguage()->getDynamicVariable('wcf.user.profile.recentActivity.likedContent', array(
					'content' => $content
				));
				$event->setTitle($text);
	
				// output
				$event->setDescription($content->getExcerpt());
			}
			else {
				$event->setIsOrphaned();
			}
		}
	}
}
