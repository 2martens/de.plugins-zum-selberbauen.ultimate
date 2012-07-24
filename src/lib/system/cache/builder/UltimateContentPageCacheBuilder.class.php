<?php
namespace ultimate\system\cache\builder;
use ultimate\data\page\PageList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the contents in relation with the pages.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.cache.builder
 * @category Ultimate CMS
 */
class UltimateContentPageCacheBuilder implements ICacheBuilder {
    
    /**
     * @see \wcf\system\cache\builder\ICacheBuilder::getData()
     */
    public function getData($cacheResource) {
        $data = array(
            'contentsToPageID' => array(),
            'contentIDsToPageID' => array()
        );
        
        $pageList = new PageList();
        $pageList->readObjects();
        $pages = $pageList->getObjects();
        
        foreach ($pages as $page) {
            $content = $page->getContent();
            $data['contentsToPageID'][$page->pageID] = $content;
            $data['contentIDsToPageID'][$page->pageID] = $content->contentID;
        }
        
        return $data;
    }
}
