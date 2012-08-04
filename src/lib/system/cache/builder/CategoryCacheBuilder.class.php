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
class CategoryCacheBuilder implements ICacheBuilder {

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
        
        $categoryList->readObjects();
        
        $categories = $categoryList->getObjects();
        if (!count($categories)) return $data;
        
        foreach ($categories as &$category) {
            /* @var $category \ultimate\data\category\Category */
            $category->contents = $category->getContents();
            $category->childCategories = $category->getChildCategories();
            $data['categories'][$category->__get('categoryID')] = $category;
            $data['categoryIDs'][] = $category->__get('categoryID');
            $data['categoriesToParent'][$category->__get('categoryID')] = $category->childCategories;
        }
        
        return $data;
    }
}
