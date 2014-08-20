<?php
/**
 * Contains the ContentVersion class.
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
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content.version
 * @category	Ultimate CMS
 */
namespace ultimate\data\content\version;
use ultimate\data\content\language\ContentLanguageEntryCache;
use wcf\data\user\User;
use wcf\data\AbstractVersion;
use wcf\util\DateUtil;

/**
 * Represents a content version.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content.version
 * @category	Ultimate CMS
 * 
 * @property-read	integer								$contentID
 * @property-read	integer								$versionID
 * @property-read	string								$contentTitle
 * @property-read	string								$contentDescription
 * @property-read	string								$contentText
 * @property-read	integer								$authorID
 * @property-read	\wcf\data\user\User					$author
 * @property-read	integer								$attachments
 * @property-read	boolean								$enableSmilies
 * @property-read	boolean								$enableHtml
 * @property-read	boolean								$enableBBCodes
 * @property-read	integer								$publishDate
 * @property-read	\DateTime|null						$publishDateObject	null if the content is neither planned nor published
 * @property-read	integer								$status	(0, 1, 2, 3)
 */
class ContentVersion extends AbstractVersion {
	/**
	 * name of the primary index column
	 * @var	string
	 */
	protected static $databaseTableIndexName = 'versionID';
	
	/**
	 * The class name of the corresponding versionable object class (FQCN).
	 * @var string
	 */
	protected static $versionableObjectClass = '\ultimate\data\content\Content';
	
	/**
	 * @see \wcf\data\IVersion::isReleased()
	 */
	public function isReleased() {
		return ($this->status == 3);
	}
	
	/**
	 * Checks if the current user can see this content version.
	 * 
	 * @return	boolean
	 */
	public function isVisible() {
		return $this->isReleased();
	}
	
	/**
	 * @see \wcf\data\DatabaseObject::__get()
	 */
	public function __get($name) {
		$result = parent::__get($name);
		if ($result === null) {
			$result = ContentLanguageEntryCache::getInstance()->get($this->versionID, $name);
		}
	
		return $result;
	}
	
	/**
	 * Handles data.
	 *
	 * @param	array	$data
	 */
	protected function handleData($data) {
		if (!isset($data['versionID']) || !isset($data['contentID'])) {
			parent::handleData($data);
			return;
		}
		
		$data['contentID'] = intval($data['contentID']);
		$data['versionID'] = intval($data['versionID']);
		$data['authorID'] = intval($data['authorID']);
		$data['author'] = new User($data['authorID']);
		$data['attachments'] = intval($data['attachments']);
		$data['enableSmilies'] = (boolean) intval($data['enableSmilies']);
		$data['enableHtml'] = (boolean) intval($data['enableHtml']);
		$data['enableBBCodes'] = (boolean) intval($data['enableBBCodes']);
		$data['publishDate'] = intval($data['publishDate']);
		$data['publishDateObject'] = ($data['publishDate'] ? DateUtil::getDateTimeByTimestamp($data['publishDate']) : null);
		$data['status'] = intval($data['status']);
		parent::handleData($data);
	}
}
