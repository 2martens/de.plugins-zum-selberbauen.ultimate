<?php
/**
 * Contains the LatestContentsCacheBuilder class.
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
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\content\CategorizedContent;
use ultimate\data\content\ContentList;
use ultimate\data\content\TaggableContent;
use ultimate\data\content\TaggedContent;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the latest contents.
 * 
 * Provides three variables:
 * * \ultimate\data\content\TaggableContent[] contents (contentID => content)
 * * integer[] contentIDs
 * * \ultimate\data\content\CategorizedContent[] contentsToSlug (contentSlug => content)
 * 
 * @author		Jim Martens
 * @copyright	2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class LatestContentsCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.AbstractCacheBuilder.html#rebuild
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'contents' => array(),
			'contentIDs' => array(),
			'contentsToSlug' => array()
		);
		
		$contentIDsToPage = ContentPageCacheBuilder::getInstance()->getData(array(), 'contentIDToPageID');
		
		$contentList = new ContentList();
		// order by default
		$sortField = 'publishDate';
		$sortOrder = 'DESC';
		$sqlOrderBy = $sortField." ".$sortOrder;
		$contentList->sqlOrderBy = $sqlOrderBy;
		$contentList->getConditionBuilder()->add('content.publishDate <> ?', array(''));
		// check if there are contentIDsToPage at all
		if (!empty($contentIDsToPage)) {
			$contentList->getConditionBuilder()->add('content.contentID NOT IN (?)', array($contentIDsToPage));
		}
		$contentList->sqlLimit = ULTIMATE_LATEST_CONTENTS_ITEMS * 10;
	
		$contentList->readObjects();
		$contents = $contentList->getObjects();
		if (empty($contents)) return $data;
	
		foreach ($contents as $contentID => $content) {
			/* @var $content \ultimate\data\content\Content */
			$data['contents'][$contentID] = new TaggableContent($content);
			$data['contentIDs'][] = $contentID;
			$data['contentsToSlug'][$content->__get('contentSlug')] = new CategorizedContent($content);
				
			$taggedContent = new TaggedContent($content);
			$tags = $taggedContent->__get('tags');
			if (!empty($tags)) {
				$data['contents'][$contentID] = $taggedContent;
			}
		}
	
		return $data;
	}
}
