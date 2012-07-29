<?php
namespace ultimate\system\cache\builder;
use ultimate\data\page\PageList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the pages.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.cache.builder
 * @category Ultimate CMS
 */
class UltimatePageCacheBuilder implements ICacheBuilder {
    
    /**
     * @see \wcf\system\cache\builder\ICacheBuilder::getData()
     */
    public function getData(array $cacheResource) {
        $data = array(
        	'pages' => array(),
            'pageIDs' => array(),
            'pagesToParent' => array(),
            'pagesToSlug' => array()
        );
        
        $pageList = new PageList();
        // order by default
        $sortField = ULTIMATE_SORT_PAGE_SORTFIELD;
        $sortOrder = ULTIMATE_SORT_PAGE_SORTORDER;
        $sqlOrderBy = $sortField." ".$sortOrder;
        $pageList->sqlOrderBy = $sqlOrderBy;
        
        $pageList->readObjectIDs();
        $pageList->readObjects();
        $pages = $pageList->getObjects();
        $pageIDs = $pageList->getObjectIDs();
        if (!count($pages) || !count($pageIDs)) return $data;
        
        $data['pages'] = array_combine($pageIDs, $pages);
        $data['pageIDs'] = $pageIDs;
        
        foreach ($data['pages'] as $id => $page) {
            /* @var $page \ultimate\data\page\Page */
            $data['pagesToParent'][$id] = $page->__get('childPages');
            $data['pagesToSlug'][$page->__get('pageSlug')] = $page;
        }
        
        return $data;
    }
}
