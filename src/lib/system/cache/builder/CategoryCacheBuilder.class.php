<?php
namespace ultimate\system\cache\builder;
use ultimate\data\category\CategoryList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the categories.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class CategoryCacheBuilder implements ICacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\ICacheBuilder::getData()
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'categories' => array(),
			'categoryIDs' => array(),
			'categoriesToParent' => array(),
			'categoriesNested' => array()
		);
		
		$categoryList = new CategoryList();
		// order by default
		$sortField = ULTIMATE_SORT_CATEGORY_SORTFIELD;
		$sortOrder = ULTIMATE_SORT_CATEGORY_SORTORDER;
		$sqlOrderBy = $sortField." ".$sortOrder;
		$categoryList->sqlOrderBy = $sqlOrderBy;
		
		$categoryList->readObjects();
		
		$categories = $categoryList->getObjects();
		if (empty($categories)) return $data;
		
		foreach ($categories as $categoryID => &$category) {
			/* @var $category \ultimate\data\category\Category */
			$category->contents = $category->getContents();
			$category->childCategories = $category->getChildCategories();
			$data['categories'][$categoryID] = $category;
			$data['categoryIDs'][] = $categoryID;
			$data['categoriesToParent'][$categoryID] = $category->childCategories;
		}
		
		// add categories without parent to the categoriesToParent cache index
		foreach ($data['categories'] as $categoryID => $category) {
			if ($category->__get('categoryParent')) continue;
			$data['categoriesToParent'][0][$categoryID] = $category;
		}
		
		foreach ($data['categoriesToParent'][0] as $categoryID => $category) {
			$data['categoriesNested'][$categoryID] = array(
				0 => $category,
				1 => $this->buildNestedCategories($categoryID, $category)
			);
		}
		
		return $data;
	}
	
	/**
	 * Builds nested category hierarchy.
	 * 
	 * @param	integer								$categoryID
	 * @param	\ultimate\data\category\Category	$category
	 * @return	(\ultimate\data\category\Category|array)[]
	 */
	protected function buildNestedCategories($categoryID, \ultimate\data\category\Category $category) {
		$childCategories = array();
		if (!empty($category->childCategories)) {
			foreach ($category->childCategories as $__categoryID => $__category) {
				$childCategories[$__categoryID] = array(
					0 => $__category,
					1 => $this->buildNestedCategories($__categoryID, $__category)
				);
			}
		}
		return $childCategories;
	}
}
