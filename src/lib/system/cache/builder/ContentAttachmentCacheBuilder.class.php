<?php
/**
 * Contains the ContentAttachmentCacheBuilder class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\data\attachment\GroupedAttachmentList;

/**
 * Caches the content attachments.
 * 
 * getData returns the following \wcf\data\attachment\GroupedAttachmentList|null
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class ContentAttachmentCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 * 
	 * @param	array	$parameters
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
		$data = null;
		
		$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		$attachmentObjectIDs = array();
		
		foreach ($contents as $contentID => $content) {
			if ($content->__get('attachments')) {
				$attachmentObjectIDs[] = $contentID;
			}
		}
		
		if (MODULE_ATTACHMENT && !empty($attachmentObjectIDs)) {
			$attachmentList = new GroupedAttachmentList('de.plugins-zum-selberbauen.ultimate.content');
			$attachmentList->getConditionBuilder()->add('attachment.objectID IN (?)', array($attachmentObjectIDs));
			$attachmentList->readObjects();
			
			$data = $attachmentList;
		}
		return $data;
	}
}
