<?php
namespace ultimate\system\cache\builder;
use ultimate\data\content\ContentList;
use ultimate\data\content\TaggableContent;
use ultimate\data\content\TaggedContent;
use wcf\system\cache\builder\ICacheBuilder;

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
class ContentCacheBuilder implements ICacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.ICacheBuilder.html#getData
	 */
	public function getData(array $cacheResource) {
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
			if (!empty($taggedContent->__get('tags'))) {
				$data['contents'][$contentID] = $taggedContent;
			}
		}
		
		return $data;
	}
}
