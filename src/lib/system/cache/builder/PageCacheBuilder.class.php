<?php
namespace ultimate\system\cache\builder;
use ultimate\data\page\PageList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the pages.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class PageCacheBuilder implements ICacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.ICacheBuilder.html#getData
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'pages' => array(),
			'pageIDs' => array(),
			'pagesToParent' => array(),
			'pagesToSlug' => array(),
			'pagesNested' => array()
		);
		
		$pageList = new PageList();
		// order by default
		$sortField = ULTIMATE_SORT_PAGE_SORTFIELD;
		$sortOrder = ULTIMATE_SORT_PAGE_SORTORDER;
		$sqlOrderBy = $sortField." ".$sortOrder;
		$pageList->sqlOrderBy = $sqlOrderBy;
		
		$pageList->readObjects();
		$pages = $pageList->getObjects();
		if (empty($pages)) return $data;
		
		foreach ($pages as $pageID => $page) {
			/* @var $page \ultimate\data\page\Page */
			$data['pages'][$pageID] = $page;
			$data['pageIDs'][] = $pageID;
			$data['pagesToParent'][$pageID] = $page->__get('childPages');
			$data['pagesToSlug'][$page->__get('pageSlug')] = $page;
		}
		
		// add pages without parent to the pagesToParent cache index
		foreach ($data['pages'] as $pageID => $page) {
			if ($page->__get('pageParent')) continue;
			$data['pagesToParent'][0][$pageID] = $page;
		}
		
		foreach ($data['pagesToParent'][0] as $pageID => $page) {
			$data['pagesNested'][$pageID] = array(
				0 => $page,
				1 => $this->buildNestedPages($pageID, $page, true)
			);
		}
		
		return $data;
	}
	
	/**
	 * Builds nested page hierarchy.
	 * 
	 * @param	integer					 $pageID
	 * @param	\ultimate\data\page\Page $page
	 * @param	boolean					 $returnCompleteArray
	 * @return	(\ultimate\data\page\Page|array)[]
	 */
	protected function buildNestedPages($pageID, \ultimate\data\page\Page $page) {
		$childPages = array();
		if (!empty($page->childPages)) {
			foreach ($page->childPages as $__pageID => $__page) {
				$childPages[$__pageID] = array(
					0 => $__page,
					1 => $this->buildNestedPages($__pageID, $__page)
				);
			}
		}
		return $childPages;
	}
}
