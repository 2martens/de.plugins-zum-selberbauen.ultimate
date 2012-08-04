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
class ContentCacheBuilder implements ICacheBuilder {
    
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
        
        $contentList->readObjects();
        $contents = $contentList->getObjects();
        if (!count($contents)) return $data;
        
        foreach ($contents as &$content) {
            /* @var $content \ultimate\data\content\Content */
            $content->categories = $content->getCategories();
            $data['contents'][$content->__get('contentID')] = $content;
            $data['contentIDs'][] = $content->__get('contentID');
        }
        
        return $data;
    }
}
