<?php
/**
 * Contains the content data model class.
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
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use ultimate\data\AbstractUltimateVersionableDatabaseObject;
use wcf\data\user\User;
use wcf\data\IMessage;
use wcf\data\ITitledObject;
use wcf\system\bbcode\AttachmentBBCode;
use wcf\system\bbcode\MessageParser;
use wcf\system\request\UltimateLinkHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\StringUtil;

/**
 * Represents a content entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 * 
 * @property-read	integer								$contentID
 * @property-read	integer								$versionID
 * @property-read	string								$contentTitle
 * @property-read	string								$contentDescription
 * @property-read	string								$contentText
 * @property-read	string								$contentSlug
 * @property-read	integer								$authorID
 * @property-read	\wcf\data\user\User					$author
 * @property-read	integer								$attachments
 * @property-read	boolean								$enableSmilies
 * @property-read	boolean								$enableHtml
 * @property-read	boolean								$enableBBCodes
 * @property-read	integer								$cumulativeLikes
 * @property-read	integer								$views
 * @property-read	integer								$publishDate
 * @property-read	\DateTime|null						$publishDateObject	null if the content is neither planned nor published
 * @property-read	integer								$lastModified
 * @property-read	integer								$status	(0, 1, 2, 3)
 * @property-read	string								$visibility	('public', 'protected', 'private')
 * @property-read	string[]							$metaData	('metaDescription' => metaDescription, 'metaKeywords' => metaKeywords)
 * @property-read	\wcf\data\user\group\UserGroup[]	$groups
 */
class Content extends AbstractUltimateVersionableDatabaseObject implements ITitledObject, IMessage {
	/**
	 * The database table name.
	 * @var string
	 */
	protected static $databaseTableName = 'content';
	
	/**
	 * If true, the database table index is used as identity.
	 * @var	boolean
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * The database table index name.
	 * @var	string
	 */
	protected static $databaseTableIndexName = 'contentID';
	
	/**
	 * The class name of the corresponding version class (FQCN).
	 * @var string
	 */
	protected static $versionClassName = '\ultimate\data\content\version\ContentVersion';
	
	/**
	 * Name of the version cache class (FQCN).
	 * @var string
	 */
	protected static $versionCacheClass = '\ultimate\data\content\version\ContentVersionCache';
	
	/**
	 * The content to category database table name.
	 * @var	string
	 */
	protected $contentCategoryTable = 'content_to_category';
	
	/**
	 * True, if the current content is visible for the current user.
	 * @var boolean
	 */
	private $isVisible = null;
	
	// TODO Language system compatible with versions
	// TODO usage of custom language system instead of WCF system
	
	/**
	 * Returns the title of this content (without language interpreting).
	 *
	 * To use language interpreting, use getLangTitle method.
	 *
	 * @return	string
	 */
	public function getTitle() {
		return $this->contentTitle;
	}
	
	/**
	 * Returns the language interpreted content title.
	 * 
	 * @return string
	 */
	public function getLangTitle() {
		return WCF::getLanguage()->get($this->contentTitle);
	}
	
	/**
	 * Checks if the current user can see this content.
	 * 
	 * @return boolean
	 */
	public function isVisible() {
		if ($this->isVisible === null) {
			$isVisible = false;
			
			$versions = $this->getVersions();
			foreach ($versions as $version) {
				/* @var $version \wcf\data\IVersion */
				$isVisible = $version->isVisible();
				if ($isVisible) break;
			}
			$this->isVisible = $isVisible;
		}
		
		return $this->isVisible;
	}
	
	/**
	 * Returns content publish timestamp.
	 *
	 * @return	integer	0 if the content isn't published yet
	 */
	public function getTime() {
		return $this->publishDate;
	}
	
	/**
	 * Returns author's user id.
	 *
	 * @return	integer
	 */
	public function getUserID() {
		return $this->authorID;
	}
	
	/**
	 * Returns author's username.
	 *
	 * @return	string
	 */
	public function getUsername() {
		return $this->author->username;
	}
	
	/**
	 * Returns the link to the object.
	 * 
	 * Works only properly if isVisible returns true. If isVisible returns false, an exception might occur.
	 *
	 * @return	string
	 */
	public function getLink() {
		return UltimateLinkHandler::getInstance()->getLink(null, array(
			'application' => 'ultimate',
			'date' => $this->publishDateObject->format('Y-m-d'),
			'contentSlug' => $this->contentSlug
		));
	}
	
	/**
	 * Returns the formatted text of this content.
	 *
	 * @return	string
	 */
	public function __toString() {
		return $this->getFormattedMessage();
	}
	
	/**
	 * Returns the language interpreted message of this content.
	 *
	 * @return string
	 */
	public function getMessage() {
		return WCF::getLanguage()->get($this->contentText);
	}
	
	/**
	 * Returns the formatted content.
	 *
	 * @return	string
	 */
	public function getFormattedMessage() {
		// assign embedded attachments
		AttachmentBBCode::setObjectID($this->contentID);
	
		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse(WCF::getLanguage()->get($this->contentText), $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
	}
	
	/**
	 * Returns an excerpt of this content.
	 *
	 * @param	integer		$maxLength
	 *
	 * @return	string
	 */
	public function getExcerpt($maxLength = 255) {
		return StringUtil::truncateHTML($this->getSimplifiedFormattedMessage(), $maxLength);
	}
	
	/**
	 * Returns a simplified version of the formatted content.
	 *
	 * @return	string
	 */
	public function getSimplifiedFormattedMessage() {
		MessageParser::getInstance()->setOutputType('text/simplified-html');
		return MessageParser::getInstance()->parse(WCF::getLanguage()->get($this->contentText), $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
	}
	
	/**
	 * Handles data.
	 * 
	 * @param	array	$data
	 */
	protected function handleData($data) {
		if (!isset($data['contentID'])) {
			parent::handleData($data);
			return;
		}
		
		$data['contentID'] = intval($data['contentID']);
		$data['authorID'] = intval($data['authorID']);
		$data['author'] = new User($data['authorID']);
		$data['cumulativeLikes'] = intval($data['cumulativeLikes']);
		$data['views'] = intval($data['views']);
		$data['lastModified'] = intval($data['lastModified']);
		parent::handleData($data);
		$this->data['metaData'] = $this->getMetaData($this->contentID, 'content');
	}
}
