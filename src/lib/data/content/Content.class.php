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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\data\user\User;
use wcf\data\IMessage;
use wcf\data\ITitledObject;
use wcf\system\bbcode\MessageParser;
use wcf\system\request\UltimateLinkHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\StringUtil;

/**
 * Represents a content entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 * 
 * @property-read	integer								$contentID
 * @property-read	string								$contentTitle
 * @property-read	string								$contentDescription
 * @property-read	string								$contentText
 * @property-read	string								$contentSlug
 * @property-read	integer								$authorID
 * @property-read	\wcf\data\user\User					$author
 * @property-read	boolean								$enableSmilies
 * @property-read	boolean								$enableHtml
 * @property-read	boolean								$enableBBCodes
 * @property-read	integer								$cumulativeLikes
 * @property-read	integer								$views
 * @property-read	integer								$publishDate
 * @property-read	\DateTime							$publishDateObject
 * @property-read	integer								$lastModified
 * @property-read	integer								$status	(0, 1, 2, 3)
 * @property-read	string								$visibility	('public', 'protected', 'private')
 * @property-read	\wcf\data\user\group\UserGroup[]	$groups	(groupID => group)
 * @property-read	string[]							$metaData	('metaDescription' => metaDescription, 'metaKeywords' => metaKeywords)
 */
class Content extends AbstractUltimateDatabaseObject implements ITitledObject, IMessage {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableName
	 */
	protected static $databaseTableName = 'content';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'contentID';
	
	/**
	 * Contains the content to category database table name.
	 * @var	string
	 */
	protected $contentCategoryTable = 'content_to_category';
	
	/**
	 * Returns all user groups associated with this content.
	 * 
	 * @return	\wcf\data\user\group\UserGroup[]
	 */
	protected function getGroups() {
		$sql = 'SELECT	  groupTable.*
		        FROM      ultimate'.WCF_N.'_user_group_to_content groupToContent
		        LEFT JOIN wcf'.WCF_N.'_user_group groupTable
		        ON        (groupTable.groupID = groupToContent.groupID)
		        WHERE     groupToContent.contentID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->contentID));
		
		$groups = array();
		while ($group = $statement->fetchObject('\wcf\data\user\group\UserGroup')) {
			if ($group !== null) {
				$groups[$group->groupID] = $group;
			}
		}
		return $groups;
	}
	
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
	 * Returns the formatted text of this content.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return $this->getFormattedMessage();
	}
	
	/**
	 * @see	\wcf\data\IMessage::getMessage()
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
		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse(WCF::getLanguage()->get($this->contentText), $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
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
	 * Checks if the current user can see this content.
	 * 
	 * @return boolean
	 */
	public function isVisible() {
		$isVisible = false;
		if ($this->visibility == 'public') {
			$isVisible = true;
		}
		elseif ($this->visibility == 'protected') {
			$groupIDs = WCF::getUser()->getGroupIDs();
			$contentGroupIDs = array_keys($this->groups);
			$result = array_intersect($groupIDs, $contentGroupIDs);
			if (!empty($result)) {
				$isVisible = true;
			}
		} else {
			$isVisible = (WCF::getUser()->__get('userID') == $this->authorID);
		}
		return $isVisible;
	}
	
	/**
	 * Returns message creation timestamp.
	 *
	 * @return	integer
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
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#handleData
	 */
	protected function handleData($data) {
		$data['contentID'] = intval($data['contentID']);
		$data['authorID'] = intval($data['authorID']);
		$data['author'] = new User($data['authorID']);
		$data['enableSmilies'] = (boolean) intval($data['enableSmilies']);
		$data['enableHtml'] = (boolean) intval($data['enableHtml']);
		$data['enableBBCodes'] = (boolean) intval($data['enableBBCodes']);
		$data['cumulativeLikes'] = intval($data['cumulativeLikes']);
		$data['views'] = intval($data['views']);
		$data['publishDate'] = intval($data['publishDate']);
		$data['publishDateObject'] = DateUtil::getDateTimeByTimestamp($data['publishDate']);
		$data['lastModified'] = intval($data['lastModified']);
		$data['status'] = intval($data['status']);
		parent::handleData($data);
		$this->data['groups'] = $this->getGroups();
		$this->data['metaData'] = $this->getMetaData($this->contentID, 'content');
	}
}
