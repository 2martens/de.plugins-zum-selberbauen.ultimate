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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\acp\form\UltimateContentAddForm;
use ultimate\data\category\Category;
use ultimate\data\content\CategorizedContent;
use ultimate\data\content\Content;
use ultimate\data\content\ContentAction;
use ultimate\data\content\ContentEditor;
use ultimate\system\cache\builder\ContentCacheBuilder;
use ultimate\system\cache\builder\ContentTagCloudCacheBuilder;
use wcf\data\tag\Tag;
use wcf\form\AbstractForm;
use wcf\form\MessageForm;
use wcf\form\RecaptchaForm;
use wcf\system\bbcode\PreParser;
use wcf\system\language\I18nHandler;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\request\LinkHandler;
use wcf\system\tagging\TagEngine;
use wcf\system\user\activity\event\UserActivityEventHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\HeaderUtil;

/**
 * Shows the UltimateContentEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateContentEditForm extends UltimateContentAddForm {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$activeMenuItem
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
		// get status data
		$this->statusID = $this->content->__get('status');
		
		// fill publish button with fitting language
		$this->publishButtonLang = 'ultimate.button.publish';
		if ($this->statusID == 2) {
			$this->statusOptions[2] = WCF::getLanguage()->get('wcf.acp.ultimate.status.scheduled');
			$this->publishButtonLang = 'ultimate.button.update';
		} elseif ($this->statusID == 3) {
			$this->statusOptions[3] = WCF::getLanguage()->get('wcf.acp.ultimate.status.published');
			$this->publishButtonLang = 'ultimate.button.update';
		}
		
		// fill save button with fitting language
		$saveButtonLangArray = array(
			0 => WCF::getLanguage()->get('ultimate.button.saveAsDraft'),
			1 => WCF::getLanguage()->get('ultimate.button.saveAsPending'),
			2 => '',
			3 => ''
		);
		$this->saveButtonLang = $saveButtonLangArray[$this->statusID];
		
		parent::readData();
		
		// get languages
		$languages = WCF::getLanguage()->getLanguages();
		
		/* @var $language \wcf\data\language\Language */
		/* @var $tag \wcf\data\tag\TagCloudTag */
		foreach ($languages as $languageID => $language) {
			// group tags by language
			$this->tagsI18n[$languageID] = TagEngine::getInstance()->getObjectTags(
				'de.plugins-zum-selberbauen.ultimate.content', 
				$this->content->__get('contentID'), 
				array($languageID)
			);
		}
		
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
		
		// read meta data
		$metaData = $this->content->__get('metaData');
		if (!empty($metaData)) {
			$this->metaDescription = (isset($metaData['metaDescription']) ? $metaData['metaDescription'] : '');
			$this->metaKeywords = (isset($metaData['metaKeywords']) ? $metaData['metaKeywords'] : '');
		}
		I18nHandler::getInstance()->setOptions('subject', PACKAGE_ID, $this->subject, 'ultimate.content.\d+.contentTitle');
		I18nHandler::getInstance()->setOptions('description', PACKAGE_ID, $this->description, 'ultimate.content.\d+.contentDescription');
		I18nHandler::getInstance()->setOptions('text', PACKAGE_ID, $this->text, 'ultimate.content.\d+.contentText');
		I18nHandler::getInstance()->setOptions('tags', PACKAGE_ID, '', 'ultimate.content.\d+.contentTags');
		
		// read editor permissions
		$this->enableBBCodes = $this->content->__get('enableBBCodes');
		$this->enableHtml = $this->content->__get('enableHtml');
		$this->enableSmilies = $this->content->__get('enableSmilies');
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
			// parse URLs
			if ($this->preParse) {
				$textValues = I18nHandler::getInstance()->getValues('text');
				foreach ($textValues as $languageID => $text) {
					$textValues[$languageID] = PreParser::getInstance()->parse($text);
				}
				I18nHandler::getInstance()->setValues('text', $textValues);
			}
			I18nHandler::getInstance()->save('text', $this->text, 'ultimate.content', PACKAGE_ID);
		}
		
		$parameters = array(
			'data' => array(
				'authorID' => WCF::getUser()->userID,
				'contentTitle' => $this->subject,
				'contentDescription' => $this->description,
				'contentSlug' => $this->slug,
				'contentText' => $this->text,
				'enableBBCodes' => $this->enableBBCodes,
				'enableHtml' => $this->enableHtml,
				'enableSmilies' => $this->enableSmilies,
				'publishDate' => $this->publishDateTimestamp,
				'lastModified' => TIME_NOW,
				'status' => $this->statusID,
				'visibility' => $this->visibility
			),
			'categories' => $this->categoryIDs,
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords
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
			TagEngine::getInstance()->addObjectTags('de.plugins-zum-selberbauen.ultimate.content', $this->content->__get('contentID'), $tags, $languageID);
			$this->tagsI18n[$languageID] = Tag::buildString($tags);
		}
		
		$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		$content = $contents[$this->contentID];
		
		// create recent activity event if published
		if ($this->content->__get('status') != 3 && $this->statusID == 3 && in_array(Category::PAGE_CATEGORY, $this->categoryIDs)) {
			UserActivityEventHandler::getInstance()->fireEvent(
				'de.plugins-zum-selberbauen.ultimate.recentActivityEvent.content',
				$this->contentID,
				null,
				$content->__get('authorID'),
				$content->__get('publishDate')
			);
		}
		
		$this->saved();
		
		$dateTime = DateUtil::getDateTimeByTimestamp($this->publishDateTimestamp);
		$this->formatDate($dateTime);
		
		$url = LinkHandler::getInstance()->getLink('UltimateContentEdit',
			array(
				'id' => $this->content->__get('contentID')
			),
			'success=true'
		);
		HeaderUtil::redirect($url);
		exit;
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
			'publishButtonLang' => WCF::getLanguage()->get($this->publishButtonLang),
			'publishButtonLangRaw' => $this->publishButtonLang,
			'action' => 'edit'
		));
		
		if ($this->success) {
			WCF::getTPL()->assign('success', true);
		}
		
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
