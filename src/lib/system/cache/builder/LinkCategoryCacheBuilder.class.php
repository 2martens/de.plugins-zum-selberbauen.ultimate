<?php
namespace ultimate\system\cache\builder;
use ultimate\data\link\CategorizedLink;
use wcf\system\cache\builder\ICacheBuilder;
use wcf\system\category\CategoryHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caches the link to category relation.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class LinkCategoryCacheBuilder implements ICacheBuilder {
	/**
	 * @see \wcf\system\cache\builder\ICacheBuilder::getData()
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'linksToCategoryID' => array(),
			'linksToCategoryName' => array()
		);
		
		$categories = CategoryHandler::getInstance()->getCategories('de.plugins-zum-selberbauen.ultimate.linkCategory');
		$categoryIDs = array_keys($categories);
		
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add('linkToCategory.categoryID IN (?)', $categoryIDs);
		
		$sql = 'SELECT    link.*
		        FROM      ultimate'.ULTIMATE_N.'_link_to_category linkToCategory
		        LEFT JOIN ultimate'.ULTIMATE_N.'_link link
		        ON        (link.linkID = linkToCategory.linkID)
		        '.$conditions;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());
		
		$links = array();
		while ($link = $statement->fetchObject('\ultimate\data\link\Link')) {
			$links[$link->__get('linkID')] = new CategorizedLink($link);
		}
		
		// group links by categories
		foreach ($categories as $categoryID => $category) {
			/* @var $category \wcf\data\category\Category */
			if (!isset($data['linksToCategoryIDs'][$categoryID])) {
				$data['linksToCategoryID'][$categoryID] = array();
			}
			if (!isset($data['linksToCategoryName'][$category->__get('title')])) {
				$data['linksToCategoryName'][$category->__get('title')] = array();
			}
			foreach ($links as $linkID => $link) {
				if (!in_array($categoryID, array_keys($link->__get('categories')))) continue;
				
				$data['linksToCategoryID'][$categoryID] = $link;
				$data['linksToCategoryName'][$category->__get('title')] = $link;
			}
		}
		return $data;
	}
}
