<?php
/**
 * The UltimateContentEdit form.
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
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\data\category\Category;
use ultimate\data\content\language\ContentLanguageEntryCache;
use ultimate\data\content\CategorizedContent;
use ultimate\data\content\Content;
use ultimate\data\content\ContentAction;
use ultimate\system\cache\builder\ContentCacheBuilder;
use wcf\data\tag\Tag;
use wcf\form\AbstractCaptchaForm;
use wcf\form\MessageForm;
use wcf\system\bbcode\PreParser;
use wcf\system\cache\builder\TagObjectCacheBuilder;
use wcf\system\cache\builder\TypedTagCloudCacheBuilder;
use wcf\system\cache\builder\UltimateTagCloudCacheBuilder;
use wcf\system\exception\IllegalLinkException;
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
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateContentEditForm extends UltimateContentAddForm {
	/**
	 * The active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.content';
	
	/**
	 * Array of needed permissions.
	 * @var string[]
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canEditContent'
	);
	
	/**
	 * The content id.
	 * @var	integer
	 */
	public $contentID = 0;
	
	/**
	 * The Content object of this content.
	 * @var	\ultimate\data\content\CategorizedContent
	 */
	public $content = null;
	
	/**
	 * The language output for the save button.
	 * @var	string
	 */
	protected $saveButtonLang = '';
	
	/**
	 * The language output for the publish button.
	 * @var	string
	 */
	protected $publishButtonLang = '';
	
	/**
	 * Reads parameters.
	 * @see	UltimateContentAddForm::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		if (isset($_REQUEST['id'])) $this->contentID = intval($_REQUEST['id']);
		$content = new CategorizedContent(new Content($this->contentID));
		if (!$content->__get('contentID')) {
			throw new IllegalLinkException();
		}
		
		$this->content = $content;
		// set attachment object id
		$this->attachmentObjectID = $this->contentID;
	}
	
	/**
	 * Reads data.
	 * @see	UltimateContentAddForm::readData()
	 */
	public function readData() {
		// get status data
		$this->statusID = $this->content->__get('status');
		
		// fill publish button with fitting language
		$this->publishButtonLang = 'ultimate.button.publish';
		if ($this->statusID == 2) {
			$this->statusOptions[2] = WCF::getLanguage()->get('wcf.acp.ultimate.status.scheduled');
			$this->publishButtonLang = 'ultimate.button.update';
		} else if ($this->statusID == 3) {
			$this->statusOptions[3] = WCF::getLanguage()->get('wcf.acp.ultimate.status.published');
			$this->publishButtonLang = 'ultimate.button.update';
		}
		
		// fill save button with fitting language
		// default to save as draft if the content is published or planned
		$saveButtonLangArray = array(
			0 => WCF::getLanguage()->get('ultimate.button.saveAsDraft'),
			1 => WCF::getLanguage()->get('ultimate.button.saveAsPending'),
			2 => WCF::getLanguage()->get('ultimate.button.saveAsDraft'),
			3 => WCF::getLanguage()->get('ultimate.button.saveAsDraft')
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
		
		// prepare I18nHandler
		if (!ContentLanguageEntryCache::getInstance()->isNeutralValue($this->content->__get('versionID'), 'contentTitle')) {
			$contentTitle = ContentLanguageEntryCache::getInstance()->getValues($this->content->__get('versionID'), 'contentTitle');
			I18nHandler::getInstance()->setValues('subject', $contentTitle);
		}
		else {
			I18nHandler::getInstance()->setValue('subject', $this->subject);
		}
		
		if (!ContentLanguageEntryCache::getInstance()->isNeutralValue($this->content->__get('versionID'), 'contentDescription')) {
			$contentDescription = ContentLanguageEntryCache::getInstance()->getValues($this->content->__get('versionID'), 'contentDescription');
			I18nHandler::getInstance()->setValues('description', $contentDescription);
		}
		else {
			I18nHandler::getInstance()->setValue('description', $this->description);
		}
		
		if (!ContentLanguageEntryCache::getInstance()->isNeutralValue($this->content->__get('versionID'), 'contentText')) {
			$contentText = ContentLanguageEntryCache::getInstance()->getValues($this->content->__get('versionID'), 'contentText');
			I18nHandler::getInstance()->setValues('text', $contentText);
		}
		else {
			I18nHandler::getInstance()->setValue('text', $this->text);
		}
		
		// read meta data
		$metaData = $this->content->__get('metaData');
		if (!empty($metaData)) {
			$this->metaDescription = (isset($metaData['metaDescription']) ? $metaData['metaDescription'] : '');
			$this->metaKeywords = (isset($metaData['metaKeywords']) ? $metaData['metaKeywords'] : '');
		}
		
		// read editor permissions
		$this->enableBBCodes = $this->content->__get('enableBBCodes');
		$this->enableHtml = $this->content->__get('enableHtml');
		$this->enableSmilies = $this->content->__get('enableSmilies');
	}
	
	/**
	 * Saves the form input.
	 * @see	UltimateContentAddForm::save()
	 */
	public function save() {
		AbstractCaptchaForm::save();
		
		// retrieve I18n values
		$contentTitle = array();
		// for the time being the existing entries will be removed, if an array entry with id 0 is provided
		if (I18nHandler::getInstance()->isPlainValue('subject')) {
			$contentTitle[ContentLanguageEntryCache::NEUTRAL_LANGUAGE] = $this->subject;
		}
		else {
			$contentTitle = I18nHandler::getInstance()->getValues('subject');
		}
		$contentDescription = array();
		if (I18nHandler::getInstance()->isPlainValue('description')) {
			$contentDescription[ContentLanguageEntryCache::NEUTRAL_LANGUAGE] = $this->description;
		}
		else {
			$contentDescription = I18nHandler::getInstance()->getValues('description');
		}
		$contentText = array();
		if (I18nHandler::getInstance()->isPlainValue('text')) {
			$contentText[ContentLanguageEntryCache::NEUTRAL_LANGUAGE] = $this->text;
		}
		else {
			$contentText = I18nHandler::getInstance()->getValues('text');
			if ($this->preParse) {
				foreach ($contentText as $languageID => $text) {
					$contentText[$languageID] = PreParser::getInstance()->parse($text);
				}
			}
		}
		
		$parameters = array(
			'data' => array(
				'authorID' => WCF::getUser()->userID,
				'contentTitle' => $contentTitle,
				'contentDescription' => $contentDescription,
				'contentSlug' => $this->slug,
				'contentText' => $contentText,
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
			'metaKeywords' => $this->metaKeywords,
			'attachmentHandler' => $this->attachmentHandler
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
		// reset cache
		TagObjectCacheBuilder::getInstance()->reset();
		TypedTagCloudCacheBuilder::getInstance()->reset();
		UltimateTagCloudCacheBuilder::getInstance()->reset();
		
		$objectAction = new ContentAction(array($this->contentID), 'updateSearchIndex');
		$objectAction->executeAction();
		
		$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		$content = $contents[$this->contentID];
		
		// create recent activity event if published
		if ($this->content->__get('status') != 3 && $this->statusID == 3 && !in_array(Category::PAGE_CATEGORY, $this->categoryIDs)) {
			UserActivityEventHandler::getInstance()->fireEvent(
				'de.plugins-zum-selberbauen.ultimate.recentActivityEvent.content',
				$this->contentID,
				null,
				$content->__get('authorID'),
				$this->publishDateTimestamp
			);
		} else if ($this->content->__get('status') == 3 && $this->statusID != 3) {
			UserActivityEventHandler::getInstance()->removeEvents(
				'de.plugins-zum-selberbauen.ultimate.recentActivityEvent.content',
				array($this->contentID)
			);
		}
		
		$this->saved();
		
		$dateTime = DateUtil::getDateTimeByTimestamp($this->publishDateTimestamp);
		$this->formatDate($dateTime);
		
		$url = LinkHandler::getInstance()->getLink('UltimateContentEdit',
			array(
				'id' => $this->content->__get('contentID'),
				'application' => 'ultimate'
			),
			'success=true'
		);
		HeaderUtil::redirect($url);
		// after initiating the redirect, no other code should be executed as the request for the original resource has ended
		exit;
	}
	
	/**
	 * Assigns the template variables.
	 * @see	UltimateContentAddForm::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'contentID' => $this->contentID,
			'publishButtonLang' => WCF::getLanguage()->get($this->publishButtonLang),
			'saveButtonLang' => $this->saveButtonLang,
			'publishButtonLangRaw' => $this->publishButtonLang,
			'action' => 'edit'
		));
		
		if ($this->success) {
			WCF::getTPL()->assign('success', true);
		}
	}
	
	/**
	 * Shows the form.
	 */
	public function show() {
		if (!empty($this->activeMenuItem)) {
			ACPMenu::getInstance()->setActiveMenuItem($this->activeMenuItem);
		}
		MessageForm::show();
	}
}
