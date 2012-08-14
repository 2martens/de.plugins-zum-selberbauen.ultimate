<?php
namespace ultimate\system\cache\builder;
use ultimate\data\category\CategoryList;
use ultimate\data\content\TaggedContent;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the contents in relation with the categories.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class ContentCategoryCacheBuilder implements ICacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\ICacheBuilder::getData()
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'contentsToCategoryID' => array(),
			'contentsToCategoryTitle' => array()
		);
		
		$categoryList = new CategoryList();
		$categoryList->readObjects();
		$categories = $categoryList->getObjects();
		
		foreach ($categories as $categoryID => $category) {
			/* @var $category \ultimate\data\category\Category */
			$contents = $category->contents;
			$categorizedContents = array();
			foreach ($contents as $contentID => $content) {
				$categorizedContent = new TaggedContent($content);
				$categorizedContents[$contentID] = $categorizedContents;
			}
			$data['contentsToCategoryID'][$categoryID] = $categorizedContents;
			$data['contentsToCategoryTitle'][$category->__get('categoryTitle')] = $categorizedContents;
		}
		
		return $data;
	}
}
