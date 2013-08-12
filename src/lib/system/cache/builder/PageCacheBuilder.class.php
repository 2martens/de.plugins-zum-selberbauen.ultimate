<?php
/**
 * Contains the PageCacheBuilder class.
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
use ultimate\data\page\PageList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the pages.
 * 
 * Provides five variables:
 * * \ultimate\data\page\Page[] pages (pageID => page)
 * * integer[] pageIDs
 * * \ultimate\data\page\Page[][] pagesToParent (pageID => \ultimate\data\page\Page[] (pageID => page))
 * * \ultimate\data\page\Page[] pagesToSlug (pageSlug => page)
 * * (\ultimate\data\page\Page|array)[] pagesNested (pageID => array (0 => page, 1 => pagesNested))
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class PageCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 * 
	 * @param	array	$parameters
	 * 
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
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
