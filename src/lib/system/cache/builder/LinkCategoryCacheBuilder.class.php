<?php
/**
 * Contains the LinkCategoryCacheBuilder class.
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
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\link\CategorizedLink;
use wcf\system\cache\builder\AbstractCacheBuilder;
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
class LinkCategoryCacheBuilder implements AbstractCacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.AbstractCacheBuilder.html#rebuild
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'linksToCategoryID' => array(),
			'linksToCategoryName' => array()
		);
		
		$categories = CategoryHandler::getInstance()->getCategories('de.plugins-zum-selberbauen.ultimate.linkCategory');
		$categoryIDs = array_keys($categories);
		
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add('linkToCategory.categoryID IN (?)', array($categoryIDs));
		
		$sql = 'SELECT    link.*
		        FROM      ultimate'.WCF_N.'_link_to_category linkToCategory
		        LEFT JOIN ultimate'.WCF_N.'_link link
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
			if (!isset($data['linksToCategoryID'][$categoryID])) {
				$data['linksToCategoryID'][$categoryID] = array();
			}
			if (!isset($data['linksToCategoryName'][$category->__get('title')])) {
				$data['linksToCategoryName'][$category->__get('title')] = array();
			}
			foreach ($links as $linkID => $link) {
				if (!in_array($categoryID, array_keys($link->__get('categories')))) continue;
				
				$data['linksToCategoryID'][$categoryID][$linkID] = $link;
				$data['linksToCategoryName'][$category->__get('title')][$linkID] = $link;
			}
		}
		return $data;
	}
}
