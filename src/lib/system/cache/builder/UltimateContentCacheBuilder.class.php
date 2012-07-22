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
            'contents' => array()
        );
        
        $contentList = new ContentList();
        $contentList->readObjects();
        $data['contents'] = $contentList->getObjects();
        
        return $data;
    }
}
