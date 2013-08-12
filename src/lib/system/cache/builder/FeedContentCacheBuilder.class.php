<?php
/**
 * Contains the FeedContentCacheBuilder class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\content\FeedContentList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the feed contents.
 * 
 * Provides one variable:
 * * \ultimate\data\content\FeedContent[][] feedContentsToCategoryID (categoryID => feedContents (contentID => content))
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class FeedContentCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 * 
	 * @param	array	$parameters
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'feedContentsToCategoryID' => array()
		);
		
		$categoryIDs = CategoryCacheBuilder::getInstance()->getData(array(), 'categoryIDs');
		
		foreach ($categoryIDs as $categoryID) {
			$data['feedContentsToCategoryID'][$categoryID] = array();
		} 
		
		$feedContentList = new FeedContentList();
		$feedContentList->readObjects();
		$contents = $feedContentList->getObjects();
		
		if (empty($contents)) return $data;
		
		/* @var $content \ultimate\data\content\FeedContent */
		foreach ($contents as $contentID => $content) {
			$_categoryIDs = array_keys($content->getCategories());
			foreach ($_categoryIDs as $_categoryID) {
				$data['feedContentsToCategoryID'][$_categoryID][$contentID] = $content;
			}
		}
		
		return $data;
	}
}
