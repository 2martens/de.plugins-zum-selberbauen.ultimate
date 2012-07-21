<?php
namespace ultimate\system\cache\builder;
use ultimate\data\config\ConfigList;
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
     * @see wcf\system\cache\builder\ICacheBuilder::getData()
     */
    public function getData(array $cacheResource) {
        
        $data = array(
        	'pages' => array(),
            'configs' => array()
        );
        
        //read pages
        $pageList = new PageList();
        $pageList->readObjects();
        $objects = $pageList->getObjects();
        $pages = array();
        $configIDs = array();
        foreach ($objects as $page) {
            $pages[$page->pageID] = $page->pageSlug;
            $configIDs[$page->pageSlug] = $page->configID;
        }
        $data['pages'] = $pages;
        
        //read configs
        $configList = new ConfigList();
        $configList->readObjects();
        $objects = $configList->getObjects();
        $configs = array();
        foreach ($objects as $config) {
            if (!in_array($config->configID, $configIDs)) continue;
            $pageSlugs = array_flip($configIDs);
            $configs[$pageSlugs[$config->configID]] = array(
                'configTitle' => $config->configTitle,
                'metaDescription' => $config->metaDescription,
                'metaKeywords' => $config->metaKeywords,
                'templateName' => $config->templateName,
                'storage' => $config->storage
            );
        }
        $data['configs'] = $configs;
        return $data;
    }
}
