<?php
/**
 * Contains the ContentTagCacheBuilder class.
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
use ultimate\data\content\TaggedContent;
use ultimate\data\content\ContentList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\cache\CacheHandler;

/**
 * Caches the content to tag relation.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class ContentTagCacheBuilder implements AbstractCacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.AbstractCacheBuilder.html#rebuild
	 */
	protected  function rebuild(array $parameters) {
		$data = array(
			'contentsToTagID' => array()
		);
		
		// reading contents
		$contentList = new ContentList();
		$contentList->readObjects();
		/* @var $contents \ultimate\data\content\Content[] */
		$contents = $contentList->getObjects();
		
		// reading tags
		$cacheName = 'content-tag';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\ContentTagCloudCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		/* @var $tags \wcf\data\tag\TagCloudTag[] */
		$tags = CacheHandler::getInstance()->get($cacheName);
		
		// group by tag id
		foreach ($tags as $tagID => $tag) {
			$data['contentsToTagID'][$tagID] = array();
			foreach ($contents as $contentID => $content) {
				/* @var $content \ultimate\data\content\Content */
				/* @var $__tags \wcf\data\tag\Tag[] */
				$taggedContent = new TaggedContent($content);
				$__tags = $taggedContent->__get('tags');
				$__keys = array();
				foreach ($__tags as $languageID => $tags) {
					$__keys = array_merge($__keys, array_keys($tags));
				}
				if (!in_array($tagID, $__keys)) continue;
				$data['contentsToTagID'][$tagID][$contentID] = $taggedContent;
			}
		}
		return $data;
	}
}
