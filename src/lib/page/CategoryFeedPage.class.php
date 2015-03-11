<?php
/**
 * Contains the CategoryFeedPage class.
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
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
namespace ultimate\page;
use ultimate\data\content\FeedContentList;
use ultimate\system\cache\builder\CategoryCacheBuilder;
use wcf\page\AbstractFeedPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows contents for the specified categories in feed.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
class CategoryFeedPage extends AbstractFeedPage {
	/**
	 * application name
	 * @var	string
	 */
	public $application = 'ultimate';
	
	/**
	 * Reads the given parameters.
	 */
	public function readParameters() {
		parent::readParameters();
		
		$objectIDs = CategoryCacheBuilder::getInstance()->getData(array(), 'categoryIDs');
		if (empty($this->objectIDs)) {
			$this->objectIDs = $objectIDs;
		}
		else {
			// validate ids
			foreach ($this->objectIDs as $objectID) {
				// wrong id
				if (!in_array($objectID, $objectIDs)) {
					throw new IllegalLinkException();
				}
			}
		}
	}
	
	/**
	 * Reads/Gets the data to be displayed on this page.
	 */
	public function readData() {
		parent::readData();
		
		$items = new FeedContentList($this->objectIDs);
		$items->sqlLimit = 20;
		$items->readObjects();
		
		$this->items = $items;
		
		// set title
		if (count($this->objectIDs) === 1) {
			$categories = CategoryCacheBuilder::getInstance()->getData(array(), 'categories');
			$category = $categories[reset($this->objectIDs)];
			$this->title = $category->getTitle();
		}
		else {
			$this->title = WCF::getLanguage()->get('ultimate.header.menu.index');
		}
	}
}
