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
            'categoryIDs' => array(),
            'categoriesToParent' => array()
        );
    
        $categoryList = new CategoryList();
        // order by default
        $sortField = ULTIMATE_SORT_CATEGORY_SORTFIELD;
        $sortOrder = ULTIMATE_SORT_CATEGORY_SORTORDER;
        $sqlOrderBy = $sortField." ".$sortOrder;
        $categoryList->sqlOrderBy = $sqlOrderBy;
        
        $categoryList->readObjectIDs();
        $categoryList->readObjects();
        $categories = $categoryList->getObjects();
        $categoryIDs = $categoryList->getObjectIDs();
        $data['categories'] = array_combine($categoryIDs, $categories);
        $data['categoryIDs'] = $categoryIDs;
        
        foreach ($data['categories'] as $id => $category) {
            $data['categoriesToParent'][$id] = $category->childCategories;
        }
        
        return $data;
    }
}
