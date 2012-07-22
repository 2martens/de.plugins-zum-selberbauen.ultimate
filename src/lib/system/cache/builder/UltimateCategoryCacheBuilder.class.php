<?php
namespace ultimate\system\cache\builder;
use ultimate\data\category\CategoryList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the categories.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.cache.builder
 * @category Ultimate CMS
 */
class UltimateCategoryCacheBuilder implements ICacheBuilder {

    /**
     * @see \wcf\system\cache\builder\ICacheBuilder::getData()
     */
    public function getData(array $cacheResource) {
        $data = array(
            'categories' => array(),
            'categoryIDs' => array()
        );
    
        $categoryList = new CategoryList();
        $categoryList->readObjectIDs();
        $categoryList->readObjects();
        $categories = $categoryList->getObjects();
        $categoryIDs = $categoryList->getObjectIDs();
        $data['categories'] = array_combine($categoryIDs, $categories);
        $data['categoryIDs'] = $categoryIDs;
        
        return $data;
    }
}
