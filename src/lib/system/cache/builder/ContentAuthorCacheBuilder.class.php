<?php
/**
 * Contains the ContentAuthorCacheBuilder class.
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
use ultimate\data\content\ContentList;
use ultimate\data\content\TaggedContent;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the contents in relation with the authors.
 *
 * Provides two variables:
 * * \ultimate\data\content\TaggedContent[][] contentsToAuthorID (authorID => contents (contentID => content))
 *
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class ContentAuthorCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 *
	 * @param	array	$parameters
	 *
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'contentsToAuthorID' => array()
		);

		$contentList = new ContentList();
		$sortField = ULTIMATE_SORT_CONTENT_SORTFIELD;
		$sortOrder = ULTIMATE_SORT_CONTENT_SORTORDER;
		$sqlOrderBy = $sortField.' '.$sortOrder;
		$contentList->sqlOrderBy = $sqlOrderBy;
		$contentList->sqlJoins .= 'LEFT JOIN ultimate'.WCF_N.'_content_version contentVersion ON (content.contentID = contentVersion.contentID)';
		$contentList->readObjects();
		$contents = $contentList->getObjects();

		foreach ($contents as $contentID => $content) {
			/* @var $content \ultimate\data\content\Content */
			$authorID = $content->authorID;
			if (!isset($data['contentsToAuthorID'][$authorID])) {
				$data['contentsToAuthorID'][$authorID] = array();
			}
			$data['contentsToAuthorID'][$authorID][$contentID] = new TaggedContent($content);
		}

		return $data;
	}
}
