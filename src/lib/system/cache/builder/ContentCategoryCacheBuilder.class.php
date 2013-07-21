<?php
/**
 * Contains the ContentCategoryCacheBuilder class.
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
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\category\CategoryList;
use ultimate\data\content\TaggedContent;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the contents in relation with the categories.
 * 
 * Provides two variables:
 * * \ultimate\data\content\TaggedContent[][] contentsToCategoryID (categoryID => contents (contentID => content))
 * * \ultimate\data\content\TaggedContent[][] contentsToCategorytitle (categoryTitle => contents (contentID => content))
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class ContentCategoryCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.AbstractCacheBuilder.html#rebuild
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'contentsToCategoryID' => array(),
			'contentsToCategoryTitle' => array()
		);
		
		$categoryList = new CategoryList();
		$categoryList->readObjects();
		$categories = $categoryList->getObjects();
		
		foreach ($categories as $categoryID => $category) {
			/* @var $category \ultimate\data\category\Category */
			$contents = $category->contents;
			$categorizedContents = array();
			foreach ($contents as $contentID => $content) {
				$categorizedContent = new TaggedContent($content);
				$categorizedContents[$contentID] = $categorizedContent;
			}
			$data['contentsToCategoryID'][$categoryID] = $categorizedContents;
			$data['contentsToCategoryTitle'][$category->__get('categoryTitle')] = $categorizedContents;
		}
		
		return $data;
	}
}
