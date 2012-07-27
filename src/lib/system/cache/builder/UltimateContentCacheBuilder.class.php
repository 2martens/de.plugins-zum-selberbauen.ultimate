<?php
namespace ultimate\system\cache\builder;
use ultimate\data\content\ContentList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the contents.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.cache.builder
 * @category Ultimate CMS
 */
class UltimateContentCacheBuilder implements ICacheBuilder {
    
    /**
     * @see \wcf\system\cache\builder\ICacheBuilder::getData()
     */
    public function getData(array $cacheResource) {
        $data = array(
            'contents' => array(),
            'contentIDs' => array()
        );
        
        $contentList = new ContentList();
        // order by default
        $sortField = ULTIMATE_SORT_CONTENT_SORTFIELD;
        $sortOrder = ULTIMATE_SORT_CONTENT_SORTORDER;
        $sqlOrderBy = $sortField." ".$sortOrder;
        $contentList->sqlOrderBy = $sqlOrderBy;
        
        $contentList->readObjectIDs();
        $contentList->readObjects();
        $contentIDs = $contentList->getObjectIDs();
        $contents = $contentList->getObjects();
        if (!count($contentIDs) || !count($contents)) return $data;
        
        foreach ($contents as &$content) {
            $content->categories = $content->getCategories();
        }
        
        $data['contents'] = array_combine($contentIDs, $contents);
        $data['contentIDs'] = $contentIDs;
        
        return $data;
    }
}
