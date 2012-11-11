<?php
namespace ultimate\system\cache\builder;
use ultimate\data\content\TaggedContent;
use ultimate\data\page\PageList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the contents in relation with the pages.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class ContentPageCacheBuilder implements ICacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.ICacheBuilder.html#getData
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'contentsToPageID' => array(),
			'contentIDsToPageID' => array()
		);
		
		$pageList = new PageList();
		$pageList->readObjects();
		$pages = $pageList->getObjects();
		
		foreach ($pages as $pageID => $page) {
			/* @var $page \ultimate\data\page\Page */
			$content = $page->getContent();
			if ($content === null) continue;
			$data['contentsToPageID'][$pageID] = new TaggedContent($content);
			$data['contentIDsToPageID'][$pageID] = $content->__get('contentID');
		}
		
		return $data;
	}
}
