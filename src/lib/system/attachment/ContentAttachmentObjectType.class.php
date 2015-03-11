<?php
/**
 * Contains the ContentAttachmentObjectType class.
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
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.attachment
 * @category	Ultimate CMS
 */
namespace ultimate\system\attachment;
use ultimate\data\content\Content;
use ultimate\data\content\ContentList;
use wcf\system\attachment\AbstractAttachmentObjectType;
use wcf\system\WCF;

/**
 * Attachment object type implementation for contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.attachment
 * @category	Ultimate CMS
 */
class ContentAttachmentObjectType extends AbstractAttachmentObjectType {
	/**
	 * cached content objects
	 * @var \ultimate\data\content\Content[]
	 */
	protected $cachedObjects = array();
	
	/**
	 * Returns true if the active user has the permission to download attachments.
	 * 
	 * @param	integer	$objectID
	 * @return	boolean
	 */
	public function canDownload($objectID) {
		if ($objectID) {
			$content = new Content($objectID);
			if (!$content->isVisible()) return false;
				
			return WCF::getSession()->getPermission('user.ultimate.content.canDownloadAttachment');
		}
	
		return false;
	}
	
	/**
	 * Returns true if the active user has the permission to view attachment previews (thumbnails).
	 * 
	 * @param	integer	$objectID
	 * @return	boolean
	 */
	public function canViewPreview($objectID) {
		if ($objectID) {
			$content = new Content($objectID);
			if (!$content->isVisible()) return false;
				
			return WCF::getSession()->getPermission('user.ultimate.content.canViewAttachmentPreview');
		}
	
		return false;
	}
	
	/**
	 * Returns true if the active user has the permission to upload attachments.
	 * 
	 * @param	integer	$objectID
	 * @param	integer	$parentObjectID
	 * @return	boolean
	 */
	public function canUpload($objectID, $parentObjectID = 0) {
		return WCF::getSession()->getPermission('user.ultimate.editing.canUploadAttachment');
	}
	
	/**
	 * Returns true if the active user has the permission to delete attachments.
	 * 
	 * @param	integer	$objectID
	 * @return	boolean
	 */
	public function canDelete($objectID) {
		return (WCF::getSession()->getPermission('admin.content.ultimate.canEditContent'));
	}
	
	/**
	 * Gets the container object of an attachment.
	 * 
	 * @param	integer	$objectID
	 * @return	\ultimate\data\content\Content|null
	 */
	public function getObject($objectID) {
		if (isset($this->cachedObjects[$objectID])) return $this->cachedObjects[$objectID];
	
		return null;
	}
	
	/**
	 * Caches the data of the given container objects.
	 * 
	 * @param	integer[]	$objectIDs
	 */
	public function cacheObjects(array $objectIDs) {
		$contentList = new ContentList();
		$contentList->setObjectIDs($objectIDs);
		$contentList->readObjects();
		$this->cachedObjects = $contentList->getObjects();
	}
}
