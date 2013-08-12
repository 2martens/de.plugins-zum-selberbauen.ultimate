<?php
/**
 * Contains the LinkCacheBuilder class.
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
use ultimate\data\link\CategorizedLink;
use ultimate\data\link\LinkList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches links.
 * 
 * Provides two variables:
 * * \ultimate\data\link\Link[] links (linkID => link)
 * * integer[] linkIDs
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class LinkCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 * 
	 * @param	array	$parameters
	 * 
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'links' => array(),
			'linkIDs' => array()
		);
		
		$linkList = new LinkList();
		$linkList->readObjects();
		$links = $linkList->getObjects();
		$linkIDs = $linkList->getObjectIDs();
		
		if (empty($links)) return $data;
		
		// using categorized link
		foreach ($links as $linkID => $link) {
			$links[$linkID] = new CategorizedLink($link);
		}
		
		$data['links'] = $links;
		$data['linkIDs'] = (is_null($linkIDs) ? array() : $linkIDs);
		
		return $data;
	}
} 
