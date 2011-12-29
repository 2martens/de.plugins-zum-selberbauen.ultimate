<?php
namespace ultimate\system\cache\builder;
use ultimate\data\link\LinkList;

use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the ultimate links.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.cms
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
        foreach ($objects as $link) {
            $links[$link->linkID] = $link->linkSlug;
        }
        $data['links'] = $links;
        
        //reading data of the config table
        $sql = 'SELECT configID, linkSlug';
    }
}
