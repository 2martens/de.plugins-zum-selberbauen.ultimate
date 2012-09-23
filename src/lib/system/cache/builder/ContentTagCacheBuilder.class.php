<?php
namespace ultimate\system\cache\builder;
use ultimate\data\content\TaggedContent;

use wcf\system\cache\CacheHandler;

use ultimate\data\content\ContentList;
use wcf\system\cache\builder\ICacheBuilder;

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
class ContentTagCacheBuilder implements ICacheBuilder {
	/**
	 * @see \wcf\system\cache\builder\ICacheBuilder::getData()
	 */
	public function getData(array $cacheResource) {
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
				$__tags = $taggedContent->tags;
				if (!isset($__tags[$tagID])) continue;
				$data['contentsToTagID'][$tagID][$contentID] = $taggedContent;
			}
		}
		return $data;
	}
}
