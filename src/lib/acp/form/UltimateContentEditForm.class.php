<?php
/**
 * Contains the UltimateContentEdit form.
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
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\acp\form\UltimateContentAddForm;
use ultimate\data\content\CategorizedContent;
use ultimate\data\content\Content;
use ultimate\data\content\ContentAction;
use ultimate\data\content\ContentEditor;
use wcf\data\tag\Tag;
use wcf\form\AbstractForm;
use wcf\form\MessageForm;
use wcf\form\RecaptchaForm;
use wcf\system\bbcode\URLParser;
use wcf\system\cache\CacheHandler;
use wcf\system\language\I18nHandler;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Shows the UltimateContentEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateContentEditForm extends UltimateContentAddForm {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.acp.form.ACPForm.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.content';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canEditContent'
	);
	
	/**
	 * Contains the content id.
	 * @var	integer
	 */
	public $contentID = 0;
	
	/**
	 * Contains the Content object of this content.
	 * @var	\ultimate\data\content\CategorizedContent
	 */
	public $content = null;
	
	/**
	 * Contains the language output for the save button.
	 * @var	string
	 */
	protected $saveButtonLang = '';
	
	/**
	 * Contains the language output for the publish button.
	 * @var	string
	 */
	protected $publishButtonLang = '';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readParameters
	 */
	public function readParameters() {
		parent::readParameters();
		// I18nHandler::getInstance()->disableAssignValueVariables();
		if (isset($_REQUEST['id'])) $this->contentID = intval($_REQUEST['id']);
		$content = new CategorizedContent(new Content($this->contentID));
		if (!$content->__get('contentID')) {
			throw new IllegalLinkException();
		}
		
		$this->content = $content;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		// get languages
		$languages = WCF::getLanguage()->getLanguages();
		
		/* @var $language \wcf\data\language\Language */
		/* @var $tag \wcf\data\tag\TagCloudTag */
		foreach ($languages as $languageID => $language) {
			// group tags by language
			$this->tags[$languageID] = Tag::buildString(TagEngine::getInstance()->getObjectTags('de.plugins-zum-selberbauen.ultimate.contentTaggable', $this->content->__get('contentID'), $languageID));	
		}
		
		// get status data
		$this->statusID = $this->content->__get('status');
		
		// fill publish button with fitting language
		$this->publishButtonLang = WCF::getLanguage()->get('ultimate.button.publish');
		if ($this->statusID == 2) {
			$this->statusOptions[2] = WCF::getLanguage()->get('wcf.acp.ultimate.status.scheduled');
			$this->publishButtonLang = WCF::getLanguage()->get('ultimate.button.update');
		} elseif ($this->statusID == 3) {
			$this->statusOptions[3] = WCF::getLanguage()->get('wcf.acp.ultimate.status.published');
			$this->publishButtonLang = WCF::getLanguage()->get('ultimate.button.update');
		}
		
		// fill save button with fitting language
		$saveButtonLangArray = array(
			0 => WCF::getLanguage()->get('ultimate.button.saveAsDraft'),
			1 => WCF::getLanguage()->get('ultimate.button.saveAsPending'),
			2 => '',
			3 => ''
		);
		$this->saveButtonLang = $saveButtonLangArray[$this->statusID];
		
		// get visibility data
		$this->visibility = $this->content->__get('visibility');
		$this->groupIDs = array_keys($this->content->__get('groups'));
		
		// reading object fields
		$this->subject = $this->content->__get('contentTitle');
		$this->description = $this->content->__get('contentDescription');
		$this->slug = $this->content->__get('contentSlug');
		$this->text = $this->content->__get('contentText');
		$this->lastModified = $this->content->__get('lastModified');
		$this->categoryIDs = array_keys($this->content->__get('categories'));
		
		$languages = WCF::getLanguage()->getLanguages();
		foreach ($languages as $languageID => $language) {
			$tags = TagEngine::getInstance()->getObjectTags('de.plugins-zum-selberbauen.ultimate.contentTaggable', $this->contentID, $languageID);
			if (!empty($tags)) $this->tagsI18n[$languageID] = implode(',', $tags);
		}
		
		I18nHandler::getInstance()->setOptions('subject', PACKAGE_ID, $this->subject, 'ultimate.content.\d+.contentTitle');
		I18nHandler::getInstance()->setOptions('description', PACKAGE_ID, $this->description, 'ultimate.content.\d+.contentDescription');
		I18nHandler::getInstance()->setOptions('text', PACKAGE_ID, $this->text, 'ultimate.content.\d+.contentText');
		I18nHandler::getInstance()->setOptions('tags', PACKAGE_ID, '', 'ultimate.content.\d+.contentTags');
		
		parent::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		RecaptchaForm::save();
		
		$this->subject = 'ultimate.content.'.$this->contentID.'.contentTitle';
		if (I18nHandler::getInstance()->isPlainValue('subject')) {
			I18nHandler::getInstance()->remove($this->subject, PACKAGE_ID);
			$this->subject = I18nHandler::getInstance()->getValue('subject');
		} else {
			I18nHandler::getInstance()->save('subject', $this->subject, 'ultimate.content', PACKAGE_ID);
		}
		
		$this->description = 'ultimate.content.'.$this->contentID.'.contentDescription';
		if (I18nHandler::getInstance()->isPlainValue('description')) {
			I18nHandler::getInstance()->remove($this->description, PACKAGE_ID);
			$this->description = I18nHandler::getInstance()->getValue('description');
		} else {
			I18nHandler::getInstance()->save('description', $this->description, 'ultimate.content', PACKAGE_ID);
		}
		
		$text = 'ultimate.content.'.$this->contentID.'.contentText';
		if (I18nHandler::getInstance()->isPlainValue('text')) {
			I18nHandler::getInstance()->remove($text, PACKAGE_ID);
		} else {
			$this->text = $text;
			I18nHandler::getInstance()->save('text', $this->text, 'ultimate.content', PACKAGE_ID);
			// parse URLs
			if ($this->parseURL == 1) {
				$textValues = I18nHandler::getInstance()->getValues('text');
				foreach ($textValues as $languageID => $text) {
					$textValues[$languageID] = URLParser::getInstance()->parse($text);
				}
				
				// nasty workaround, because you can't change the values of I18nHandler before save
				$sql = 'UPDATE wcf'.WCF_N.'_language_item
						SET	languageItemValue = ?
						WHERE  languageID		= ?
						AND	languageItem	  = ?
						AND	packageID		 = ?';
				$statement = WCF::getDB()->prepareStatement($sql);
				WCF::getDB()->beginTransaction();
				foreach ($textValues as $languageID => $text) {
					$statement->executeUnbuffered(array(
						$text,
						$languageID,
						'ultimate.content.'.$this->contentID.'.contentText',
						PACKAGE_ID
					));
				}
				WCF::getDB()->commitTransaction();
			}
		}
		
		// change status to planned or publish
		if ($this->saveType == 'publish') {
			if ($this->publishDateTimestamp > TIME_NOW) {
				$this->statusID = 2; // scheduled
			} elseif ($this->publishDateTimestamp < TIME_NOW) {
				$this->statusID = 3; // published
			}
		}
		
		$parameters = array(
			'data' => array(
				'authorID' => WCF::getUser()->userID,
				'contentTitle' => $this->subject,
				'contentDescription' => $this->description,
				'contentText' => $this->text,
				'enableBBCodes' => $this->enableBBCodes,
				'enableHtml' => $this->enableHtml,
				'enableSmilies' => $this->enableSmilies,
				'publishDate' => $this->publishDateTimestamp,
				'lastModified' => TIME_NOW,
				'status' => $this->statusID,
				'visibility' => $this->visibility
			),
			'categories' => $this->categoryIDs
		);
		
		if ($this->visibility == 'protected') {
			$parameters['groupIDs'] = $this->groupIDs;
		}
		
		$action = new ContentAction(array($this->contentID), 'update', $parameters);
		$action->executeAction();
		
		// save tags
		foreach ($this->tagsI18n as $languageID => $tags) {
			if (empty($tags)) {
				$this->tagsI18n[$languageID] = '';
				continue;
			}
			TagEngine::getInstance()->addObjectTags('de.plugins-zum-selberbauen.ultimate.contentTaggable', $this->content->__get('contentID'), $tags, $languageID);
			$this->tagsI18n[$languageID] = implode(',', $tags);
		}
		$this->saved();
		
		$date = WCF::getLanguage()->getDynamicVariable(
			'ultimate.date.dateFormat',
			array(
				'britishEnglish' => ULTIMATE_GENERAL_ENGLISHLANGUAGE
			)
		);
		$time = WCF::getLanguage()->get('wcf.date.timeFormat');
		$format = str_replace(
			'%time%',
			$time,
			str_replace(
				'%date%',
				$date,
				WCF::getLanguage()->get('ultimate.date.dateTimeFormat')
			)
		);
		
		$dateTime = DateUtil::getDateTimeByTimestamp($this->publishDateTimestamp);
		$dateTime->setTimezone(WCF::getUser()->getTimezone());
		$this->publishDate = $dateTime->format($format);
		
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$useRequestData = (!empty($_POST)) ? true : false;
		I18nHandler::getInstance()->assignVariables($useRequestData);
		
		WCF::getTPL()->assign(array(
			'contentID' => $this->contentID,
			'publishButtonLang' => $this->publishButtonLang,
			'action' => 'edit'
		));
		
		// hide the save button if you edit a page which is already scheduled or published
		if (!empty($this->saveButtonLang)) {
			// status id == (0|1)
			WCF::getTPL()->assign('saveButtonLang', $this->saveButtonLang);
		}
		else {
			// status id == (2|3)
			WCF::getTPL()->assign('disableSaveButton', true);
		}
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#show
	 */
	public function show() {
		if (!empty($this->activeMenuItem)) {
			ACPMenu::getInstance()->setActiveMenuItem($this->activeMenuItem);
		}
		MessageForm::show();
	}
}
