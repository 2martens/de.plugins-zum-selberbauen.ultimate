<?php
namespace ultimate\system\cache\builder;
use ultimate\data\link\CategorizedLink;
use ultimate\data\link\LinkList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches links.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class LinkCacheBuilder implements ICacheBuilder {
	/**
	 * @see \wcf\system\cache\builder\ICacheBuilder::getData()
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'links' => array(),
			'linkIDs' => array()
		);
		
		$linkList = new LinkList();
		$linkList->readObjects();
		$links = $linkList->getObjects();
		$linkIDs = $linkList->getObjectIDs();
		
		if (empty($links)) return $data;
		
		// using categorized link
		foreach ($links as $linkID => $link) {
			$links[$linkID] = new CategorizedLink($link);
		}
		
		$data['links'] = $links;
		$data['linkIDs'] = (is_null($linkIDs) ? array() : $linkIDs);
		
		return $data;
	}
} 
