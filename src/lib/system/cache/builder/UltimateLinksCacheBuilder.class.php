<?php
namespace ultimate\system\cache\builder;
use ultimate\data\config\ConfigList;
use ultimate\data\link\LinkList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the ultimate links.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.cache.builder
 * @category Ultimate CMS
 */
class UltimateLinksCacheBuilder implements ICacheBuilder {
    
    /**
     * Contains the database table name of the link table.
     * @var string
     */
    protected $databaseTableLink = 'link';
    
    /**
     * Contains the database table name of the config table.
     * @var string
     */
    protected $databaseTableConfig = 'config';
    
    /**
     * @see wcf\system\cache\builder\ICacheBuilder::getData()
     */
    public function getData(array $cacheResource) {
        
        $data = array(
        	'links' => array(),
            'configs' => array()
        );
        
        //read links
        $linkList = new LinkList();
        $objects = $linkList->getObjects();
        $links = array();
        $configIDs = array();
        foreach ($objects as $link) {
            $links[$link->linkID] = $link->linkSlug;
            $configIDs[$link->linkSlug] = $link->configID;
        }
        $data['links'] = $links;
        
        //read configs
        $configList = new ConfigList();
        $objects = $configList->getObjects();
        $configs = array();
        foreach ($objects as $config) {
            if (!in_array($config->configID, $configIDs)) continue;
            $linkSlugs = array_flip($configIDs);
            $configs[$linkSlugs[$config->configID]] = array(
                'templateName' => $config->templateName,
                'content' => unserialize($config->requiredContents)
            );
        }
        $data['configs'] = $configs;
    }
}
