<?php
/**
 * Contains the PageAuthorCacheBuilder class.
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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
 *
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\page\PageList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the pages in relation with the authors.
 *
 * Provides two variables:
 * * \ultimate\data\page\Page[][] pagesToAuthorID (authorID => pages (pageID => page))
 *
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class PageAuthorCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 *
	 * @param	array	$parameters
	 *
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'pagesToAuthorID' => array()
		);

		$pageList = new PageList();
		$pageList->readObjects();
		$pages = $pageList->getObjects();

		foreach ($pages as $pageID => $page) {
			/* @var $page \ultimate\data\page\Page */
			$authorID = $page->authorID;
			if (!isset($data['pagesToAuthorID'][$authorID])) {
				$data['pagesToAuthorID'][$authorID] = array();
			}
			$data['pagesToAuthorID'][$authorID][$pageID] = $page;
		}

		return $data;
	}
}
