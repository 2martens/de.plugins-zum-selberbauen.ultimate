<?php
namespace ultimate\system\cache\builder;
use ultimate\data\category\CategoryList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the contents in relation with the categories.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.cache.builder
 * @category Ultimate CMS
 */
class UltimateContentCategoryCacheBuilder implements ICacheBuilder {
    
    /**
     * @see wcf\system\cache\builder\ICacheBuilder::getData()
     */
    public function getData(array $cacheResource) {
        $data = array(
            'contentsToCategoryID' => array(),
            'contentsToCategoryTitle' => array()
        );
        
        $categoryList = new CategoryList();
        $categoryList->readObjects();
        $categories = $categoryList->getObjects();
        
        foreach ($categories as $category) {
            $contents = $category->getContents();
            $data['contentsToCategoryID'][$category->categoryID] = $contents;
            $data['contentsToCategoryTitle'][$category->categoryTitle] = $contents;
        }
        
        return $data;
    }
}
