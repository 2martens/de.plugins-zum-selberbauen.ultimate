<?php
namespace ultimate\system\user\notification\event;
use ultimate\system\cache\builder\ContentCacheBuilder;
use wcf\data\user\User;
use wcf\system\request\UltimateLinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * User notification event for content comment responses.
 *
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.notification.event
 * @category	Ultimate CMS
 */
class ContentCommentResponseUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * Contains the determined content for this event.
	 * 
	 * @var \ultimate\data\content\Content
	 */
	private $content = null;
	
	/**
	 * @see	wcf\system\user\notification\event\IUserNotificationEvent::getTitle()
	 */
	public function getTitle() {
		return $this->getLanguage()->get('wcf.user.notification.content.commentResponse.title');
	}
	
	/**
	 * @see	wcf\system\user\notification\event\IUserNotificationEvent::getMessage()
	 */
	public function getMessage() {
		$content = $this->getContent();
		$user = $content->__get('author');
		
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.content.commentResponse.message', array(
			'author' => $this->author,
			'owner' => $user
		));
	}
	
	/**
	 * @see	wcf\system\user\notification\event\IUserNotificationEvent::getEmailMessage()
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$content = $this->getContent();
		$user = $content->__get('author');
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.content.commentResponse.mail', array(
			'response' => $this->userNotificationObject,
			'author' => $this->author,
			'owner' => $user,
			'notificationType' => $notificationType,
			'link' => $this->getLink()
		));
	}
	
	/**
	 * @see	wcf\system\user\notification\event\IUserNotificationEvent::getLink()
	 */
	public function getLink() {
		$content = $this->getContent();
		/* @var $date \DateTime */
		$date = $content->__get('publishDateObject');
		return UltimateLinkHandler::getInstance()->getLink(null, array(
			'date' => ''. $date->format('Y-m-d'),
			'contentSlug' => $content->__get('contentSlug')
		));
	}
	
	/**
	 * Determines the content and returns it.
	 * 
	 * @return \ultimate\data\content\Content
	 */
	private function getContent()
	{
		if ($this->content === null) {
			$commentID = $this->userNotificationObject->__get('commentID');
			
			$sql = "SELECT objectID
			        FROM   wcf".WCF_N."_comment
			        WHERE  commentID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($commentID));
			$row = $statement->fetchArray();
			
			$contentID = $row['objectID'];
			
			$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
			/* @var $content \ultimate\data\content\Content */
			$this->content = $contents[$contentID];
		}
		return $this->content;
	}
}
