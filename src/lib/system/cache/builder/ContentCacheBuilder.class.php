<?php
/**
 * Contains the ContentCacheBuilder class.
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
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\content\ContentList;
use ultimate\data\content\TaggableContent;
use ultimate\data\content\TaggedContent;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class ContentCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.AbstractCacheBuilder.html#rebuild
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'contents' => array(),
			'contentIDs' => array(),
			'contentsToSlug' => array()
		);
		
		$contentList = new ContentList();
		// order by default
		$sortField = ULTIMATE_SORT_CONTENT_SORTFIELD;
		$sortOrder = ULTIMATE_SORT_CONTENT_SORTORDER;
		$sqlOrderBy = $sortField." ".$sortOrder;
		$contentList->sqlOrderBy = $sqlOrderBy;
		
		$contentList->readObjects();
		$contents = $contentList->getObjects();
		if (empty($contents)) return $data;
		
		foreach ($contents as $contentID => $content) {
			/* @var $content \ultimate\data\content\Content */
			$data['contents'][$contentID] = new TaggableContent($content);
			$data['contentIDs'][] = $contentID;
			$data['contentsToSlug'][$content->__get('contentSlug')] = $content;
			
			$taggedContent = new TaggedContent($content);
			$tags = $taggedContent->__get('tags');
			if (!empty($tags)) {
				$data['contents'][$contentID] = $taggedContent;
			}
		}
		
		return $data;
	}
}
