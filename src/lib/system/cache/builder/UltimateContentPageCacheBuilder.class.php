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
    public function getData(array $cacheResource) {
        $data = array(
            'contentsToPageID' => array(),
            'contentIDsToPageID' => array()
        );
        
        $pageList = new PageList();
        $pageList->readObjects();
        $pages = $pageList->getObjects();
        
        foreach ($pages as $page) {
            /* @var $page \ultimate\data\page\Page */
            $content = $page->getContent();
            $data['contentsToPageID'][$page->__get('pageID')] = $content;
            $data['contentIDsToPageID'][$page->__get('pageID')] = $content->__get('contentID')';
        }
        
        return $data;
    }
}
