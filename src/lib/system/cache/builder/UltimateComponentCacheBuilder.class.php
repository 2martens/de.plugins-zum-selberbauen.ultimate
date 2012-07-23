<?php
namespace ultimate\system\cache\builder;
use ultimate\data\component\ComponentList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the components.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.cache.builder
 * @category Ultimate CMS
 */
class UltimateComponentCacheBuilder implements ICacheBuilder {
    
    /**
     * @see \wcf\system\cache\builder\ICacheBuilder::getData()
     */
    public function getData($cacheResource) {
        $data = array(
            'components' => array(),
            'componentIDs' => array()
        );
        
        $componentList = new ComponentList();
                
        $componentList->readObjectIDs();
        $componentList->readObjects();
        $data['componentIDs'] = $componentList->getObjectIDs();
        $objects = $componentList->getObjects();
        $data['components'] = array_combine($data['componentIDs'], $objects);
        
        return $data;
    }
}
