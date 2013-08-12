<?php
/**
 * Contains the CategoryCacheBuilder class.
 * 
 * LICENSE:
 * This file is part of the Ultimate CMS.
 *
 * The Ultimate CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * The Ultimate CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\category\CategoryList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the categories.
 * 
 * Provides five variables:
 * * \ultimate\data\category\Category[] categories (categoryID => category)
 * * integer[] categoryIDs
 * * \ultimate\data\category\Category[][] categoriesToParent (categoryID => \ultimate\data\category\Category[] (categoryID => category))
 * * \ultimate\data\category\Category[] categoriesToSlug (categorySlug => category)
 * * (\ultimate\data\category\Category|array)[] categoriesNested (categoryID => array (0 => category, 1 => categoriesNested))
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class CategoryCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 * 
	 * @param	array	$parameters
	 * 
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'categories' => array(),
			'categoryIDs' => array(),
			'categoriesToParent' => array(),
			'categoriesToSlug' => array(),
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
			$data['categories'][$categoryID] = $category;
			$data['categoryIDs'][] = $categoryID;
			$data['categoriesToParent'][$categoryID] = $category->childCategories;
			$data['categoriesToSlug'][$category->__get('categorySlug')] = $category;
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
