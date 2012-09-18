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
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class ContentCategoryCacheBuilder implements ICacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.ICacheBuilder.html#getData
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
				$categorizedContents[$contentID] = $categorizedContent;
			}
			$data['contentsToCategoryID'][$categoryID] = $categorizedContents;
			$data['contentsToCategoryTitle'][$category->__get('categoryTitle')] = $categorizedContents;
		}
		
		return $data;
	}
}
