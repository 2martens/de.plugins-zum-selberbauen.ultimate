<?php
/**
 * Contains the ContentVersionAddForm class.
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
 * @subpackage	form
 * @category	Ultimate CMS
 */
namespace ultimate\form;
use ultimate\data\content\CategorizedContent;
use ultimate\data\content\Content;
use ultimate\data\content\language\ContentLanguageEntryCache;
use ultimate\data\content\ContentAction;
use ultimate\page\IEditSuitePage;
use ultimate\system\cache\builder\CategoryCacheBuilder;
use ultimate\system\cache\builder\ContentAttachmentCacheBuilder;
use ultimate\util\ContentUtil;
use wcf\data\tag\Tag;
use wcf\form\AbstractCaptchaForm;
use wcf\form\MessageForm;
use wcf\system\bbcode\PreParser;
use wcf\system\cache\builder\TagObjectCacheBuilder;
use wcf\system\cache\builder\TypedTagCloudCacheBuilder;
use wcf\system\cache\builder\UltimateTagCloudCacheBuilder;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\request\UltimateLinkHandler;
use wcf\system\tagging\TagEngine;
use wcf\system\Regex;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\HeaderUtil;
use wcf\util\MessageUtil;
use wcf\util\StringUtil;

/**
 * Provides a form to add a new content version.
 *
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	form
 * @category	Ultimate CMS
 */
class ContentVersionAddForm extends MessageForm implements IEditSuitePage {
	/**
	 * name of the template for the called page
	 * @var	string
	 */
	public $templateName = 'editSuite';

	public $action = 'add';

	/**
	 * indicates if you need to be logged in to access this page
	 * @var	boolean
	 */
	public $loginRequired = true;

	/**
	 * enables template usage
	 * @var	string
	 */
	public $useTemplate = true;

	/**
	 * Array of needed permissions.
	 * @var string[]
	 */
	public $neededPermissions = array(
		'user.ultimate.editing.canAccessEditSuite',
		'user.ultimate.editing.canAddContentVersion'
	);

	/**
	 * If true, multilingualism is enabled.
	 * @var boolean
	 */
	public $enableMultilingualism = true;

	/**
	 * If 1, the signature setting is shown.
	 * @var	integer
	 */
	public $showSignatureSetting = 0;

	/**
	 * The object type for attachments.
	 * @var string
	 */
	public $attachmentObjectType = 'de.plugins-zum-selberbauen.ultimate.content';

	/**
	 * The content id.
	 * @var integer
	 */
	public $contentID = 0;

	/**
	 * The Content object.
	 * @var \ultimate\data\content\CategorizedContent
	 */
	public $content = null;

	/**
	 * The description of the content.
	 * @var	string
	 */
	public $description = '';

	/**
	 * The meta description.
	 * @var string
	 */
	public $metaDescription = '';

	/**
	 * The meta keywords.
	 * @var string
	 */
	public $metaKeywords = '';

	/**
	 * The i18n tags.
	 * @var string[]|array[]
	 */
	public $tagsI18n = array();

	/**
	 * The maximal length of the text.
	 * @var	integer	0 means there's no limitation
	 */
	public $maxTextLength = 0;

	/**
	 * The publish date.
	 * @var	string
	 */
	public $publishDate = '';

	/**
	 * The publish date as timestamp.
	 * @var	integer
	 */
	public $publishDateTimestamp = TIME_NOW;

	/**
	 * All status options.
	 * @var	string[]
	 */
	public $statusOptions = array();

	/**
	 * The status id.
	 * @var	integer
	 */
	public $statusID = 0;

	/**
	 * The save type.
	 * @var	string
	 */
	public $saveType = '';

	/**
	 * The attachment list.
	 * @var \wcf\data\attachment\GroupedAttachmentList
	 */
	public $attachmentList = null;

	/**
	 * jQuery datepicker date format.
	 * @var	string
	 */
	protected $dateFormat = 'yy-mm-dd';

	/**
	 * The timestamp from the begin of the add process.
	 * @var	integer
	 */
	protected $startTime = 0;

	/**
	 * True, if the form has been successfully finished.
	 *
	 * @var	boolean
	 */
	protected $success = false;

	/**
	 * A list of active EditSuite menu items.
	 * @var string[]
	 */
	protected $activeMenuItems = array(
		'ContentListPage',
		'ultimate.edit.contents'
	);

	/**
	 * Contains the i18nValues.
	 * @var string[][]
	 */
	protected $i18nValues = array(
		'subject' => array(),
		'description' => array(),
		'text' => array(),
		'tags' => array()
	);

	/**
	 * Contains the i18nValues.
	 * @var string[]
	 */
	protected $i18nPlainValues = array(
		'subject' => '',
		'description' => '',
		'text' => '',
		'tags' => ''
	);

	/**
	 * @see \ultimate\page\IEditSuitePage::getActiveMenuItems()
	 */
	public function getActiveMenuItems() {
		return $this->activeMenuItems;
	}

	/**
	 * @see \ultimate\page\IEditSuitePage::getJavascript()
	 */
	public function getJavascript() {
		return WCF::getTPL()->fetch('__editSuiteJS.ContentVersionAddForm', 'ultimate');
	}

	/**
	 * Reads parameters.
	 */
	public function readParameters() {
		parent::readParameters();
		if (isset($_REQUEST['success'])) $this->success = true;

		I18nHandler::getInstance()->register('subject');
		I18nHandler::getInstance()->register('description');
		I18nHandler::getInstance()->register('tags');
		I18nHandler::getInstance()->register('text');

		// the 'edit' part
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
	 */
	public function readData() {
		// fill status options
		$this->statusOptions[0] = WCF::getLanguage()->get('wcf.acp.ultimate.status.draft');
		$this->statusOptions[1] = WCF::getLanguage()->get('wcf.acp.ultimate.status.pendingReview');

		parent::readData();

		// fill publishDate with default value (today)
		/* @var $dateTime \DateTime */
		$dateTime = null;
		if (isset($this->version)) {
			$dateTime = $this->version->__get('publishDateObject');
		}
		$this->formatDate($dateTime);

		$this->attachmentList = ContentAttachmentCacheBuilder::getInstance()->getData(array(), 'attachmentList');

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
		
		// set version if not existing
		/** @var \ultimate\data\content\version\ContentVersion $version */
		$version = null;
		if (!isset($this->version)) {
			/** @var \ultimate\data\content\Content $content */
			$content = $this->content->getDecoratedObject();
			$version = $this->$content->getCurrentVersion();
		}
		else {
			$version = $this->version;
		}

		// reading object fields
		$this->subject = $version->__get('contentTitle');
		$this->description = $version->__get('contentDescription');
		$this->text = $version->__get('contentText');
		$this->lastModified = $this->content->__get('lastModified');

		// prepare I18nHandler
		if (!ContentLanguageEntryCache::getInstance()->isNeutralValue($version->__get('versionID'), 'contentTitle')) {
			$contentTitle = ContentLanguageEntryCache::getInstance()->getValues($this->content->__get('versionID'), 'contentTitle');
			$this->i18nValues['subject'] = $contentTitle;
		}
		else {
			$this->i18nPlainValues['subject'] = $this->subject;
		}

		if (!ContentLanguageEntryCache::getInstance()->isNeutralValue($version->__get('versionID'), 'contentDescription')) {
			$contentDescription = ContentLanguageEntryCache::getInstance()->getValues($version->__get('versionID'), 'contentDescription');
			$this->i18nValues['description'] = $contentDescription;
		}
		else {
			$this->i18nPlainValues['description'] = $this->description;
		}

		if (!ContentLanguageEntryCache::getInstance()->isNeutralValue($version->__get('versionID'), 'contentText')) {
			$contentText = ContentLanguageEntryCache::getInstance()->getValues($version->__get('versionID'), 'contentText');
			$this->i18nValues['text'] = $contentText;
		}
		else {
			$this->i18nPlainValues['text'] = $this->text;
		}

		// read meta data
		$metaData = $this->content->__get('metaData');
		if (!empty($metaData)) {
			$this->metaDescription = (isset($metaData['metaDescription']) ? $metaData['metaDescription'] : '');
			$this->metaKeywords = (isset($metaData['metaKeywords']) ? $metaData['metaKeywords'] : '');
		}

		// read editor permissions
		$this->enableBBCodes = $version->__get('enableBBCodes');
		$this->enableHtml = $version->__get('enableHtml');
		$this->enableSmilies = $version->__get('enableSmilies');
	}

	/**
	 * Reads form input.
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		I18nHandler::getInstance()->readValues();
		if (I18nHandler::getInstance()->isPlainValue('subject')) $this->subject = StringUtil::trim(I18nHandler::getInstance()->getValue('subject'));
		if (I18nHandler::getInstance()->isPlainValue('description')) $this->description = StringUtil::trim(I18nHandler::getInstance()->getValue('description'));
		if (isset($_POST['metaDescription'])) $this->metaDescription = StringUtil::trim($_POST['metaDescription']);
		if (isset($_POST['metaKeywords'])) $this->metaKeywords = StringUtil::trim($_POST['metaKeywords']);
		$this->tagsI18n = I18nHandler::getInstance()->getValues('tags');
		if (I18nHandler::getInstance()->isPlainValue('text')) $this->text = MessageUtil::stripCrap(trim(I18nHandler::getInstance()->getValue('text')));
		if (isset($_POST['status'])) $this->statusID = intval($_POST['status']);
		if (isset($_POST['publishDate'])) $this->publishDate = StringUtil::trim($_POST['publishDate']);
		if (isset($_POST['save'])) $this->saveType = 'save';
		if (isset($_POST['publish'])) $this->saveType = 'publish';
		if (isset($_POST['startTime'])) $this->startTime = intval($_POST['startTime']);
	}

	/**
	 * Validates the form input.
	 */
	public function validate() {
		$this->success = false;
		try {
			$this->validateSubject();
			$this->validateDescription();
			$this->validateMetaDescription();
			$this->validateMetaKeywords();
			$this->validateTags();
			$this->validateText();
			// multilingualism
			$this->validateContentLanguage();
			$this->validatePublishDate();
			$this->validateStatus();
			AbstractCaptchaForm::validate();
		}
		catch (UserInputException $e) {
			foreach ($this->tagsI18n as $languageID => $tags) {
				if (is_string($tags)) {
					$this->tagsI18n[$languageID] = Tag::splitString($tags);
				}
			}
			throw $e;
		}
	}

	/**
	 * Saves the form input.
	 */
	public function save() {
		if (!I18nHandler::getInstance()->isPlainValue('text')) AbstractCaptchaForm::save();
		else parent::save();

		// retrieve I18n values
		$contentTitle = array();
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
				'contentText' => $contentText,
				'enableBBCodes' => $this->enableBBCodes,
				'enableHtml' => $this->enableHtml,
				'enableSmilies' => $this->enableSmilies,
				'publishDate' => $this->publishDateTimestamp,
				'lastModified' => TIME_NOW,
				'status' => $this->statusID
			),
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords,
			'attachmentHandler' => $this->attachmentHandler
		);

		$this->objectAction = new ContentAction(array($this->contentID), 'createVersion', $parameters);
		$this->objectAction->executeAction();
		$returnValues = $this->objectAction->getReturnValues();
		$version = $returnValues['returnValues'];

		// save tags
		foreach ($this->tagsI18n as $languageID => $tags) {
			if (empty($tags)) {
				$this->tagsI18n[$languageID] = '';
				continue;
			}
			TagEngine::getInstance()->addObjectTags('de.plugins-zum-selberbauen.ultimate.content', $this->contentID, $tags, $languageID);
			$this->tagsI18n[$languageID] = Tag::buildString($tags);
		}

		// reset cache
		TagObjectCacheBuilder::getInstance()->reset();
		TypedTagCloudCacheBuilder::getInstance()->reset();
		UltimateTagCloudCacheBuilder::getInstance()->reset();

		$objectAction = new ContentAction(array($this->contentID), 'updateSearchIndex');
		$objectAction->executeAction();

		$this->saved();

		$url = UltimateLinkHandler::getInstance()->getLink('ContentVersionEdit',
			array(
				'id' => $version->__get('versionID'),
				'application' => 'ultimate',
				'parent' => 'EditSuite'
			),
			'success=true'
		);
		HeaderUtil::redirect($url);
		// after initiating the redirect, no other code should be executed as the request for the original resource has ended
		exit;
	}

	/**
	 * Assigns variables to the template engine.
	 */
	public function assignVariables() {
		// fix for the broken assignment system (magic is in the works here); mind the capital I at the beginning
		WCF::getTPL()->assign(array(
			'I18nValues' => $this->i18nValues,
			'I18nPlainValues' => $this->i18nPlainValues
		));
		
		parent::assignVariables();
		I18nHandler::getInstance()->assignVariables();
		ksort($this->statusOptions);
		
		WCF::getTPL()->assign(array(
			// actual form variables
			'contentID' => $this->contentID,
			'description' => $this->description,
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords,
			'languageID' => ($this->languageID ? $this->languageID : 0),
			'tagsI18n' => $this->tagsI18n,
			'statusOptions' => $this->statusOptions,
			'statusID' => $this->statusID,
			'startTime' => $this->startTime,
			'publishDate' => $this->publishDate,
			'attachmentList' => $this->attachmentList,
		));
		
		WCF::getTPL()->assign(array(
			'activeMenuItems' => $this->activeMenuItems,
			'pageContent' => WCF::getTPL()->fetch('__editSuite.ContentVersionAddForm', 'ultimate'),
			'pageJS' => WCF::getTPL()->fetch('__editSuiteJS.ContentVersionAddForm', 'ultimate'),
			'initialController' => 'ContentVersionAddForm',
			'initialRequestType' => 'form',
			'initialURL' => '/EditSuite/ContentVersionAdd/'.$this->contentID.'/'
		));
	}

	/**
	 * Shows the requested page.
	 */
	public function show() {
		parent::show();
		if (!$this->useTemplate) {
			WCF::getTPL()->display($this->templateName, 'ultimate', false);
		}
	}

	/**
	 * Formats the date and saves it into object variables.
	 *
	 * @param	\DateTime	$dateTime	optional
	 */
	protected function formatDate(\DateTime $dateTime = null) {
		if ($dateTime === null) $dateTime = DateUtil::getDateTimeByTimestamp(TIME_NOW);
		$dateTime->setTimezone(WCF::getUser()->getTimezone());
		//$date = 'M/d/Y';
		$date = 'Y-m-d';
		$time = 'H:i:s';
		$format = $date.' '.$time;
		$this->publishDate = $dateTime->format($format);
		$this->publishDateTimestamp = $dateTime->getTimestamp();
	}

	/**
	 * Validates content subject.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateSubject() {
		if (!I18nHandler::getInstance()->isPlainValue('subject')) {
			if (!I18nHandler::getInstance()->validateValue('subject')) {
				throw new UserInputException('subject');
			}
			$subjectValues = I18nHandler::getInstance()->getValues('subject');
			foreach ($subjectValues as $languageID => $subject) {
				if (mb_strlen($subject) < 4) {
					throw new UserInputException('subject', 'tooShort');
				}
			}
		} else {
			// checks if subject is empty; we don't have to do it twice
			parent::validateSubject();

			if (mb_strlen($this->subject) < 4) {
				throw new UserInputException('subject', 'tooShort');
			}
		}
	}

	/**
	 * Validates content description.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateDescription() {
		if (!I18nHandler::getInstance()->isPlainValue('description')) {
			if (I18nHandler::getInstance()->validateValue('description')) {
				$descriptionValues = I18nHandler::getInstance()->getValues('description');
				foreach ($descriptionValues as $languageID => $description) {
					if (mb_strlen($description) < 4) {
						throw new UserInputException('description', 'tooShort');
					}
				}
			}
		}
		else {
			if (!empty($this->description)) {
				if (mb_strlen($this->description) < 4) {
					throw new UserInputException('description', 'tooShort');
				}
			}
		}
	}

	/**
	 * Validates the slug.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateSlug() {
		if (empty($this->slug)) {
			throw new UserInputException('slug');
		}
		if (!ContentUtil::isAvailableSlug($this->slug, (isset($this->contentID)) ? $this->contentID : 0)) {
			throw new UserInputException('slug', 'notUnique');
		}
	}

	/**
	 * Validates the meta description.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateMetaDescription() {
		if (mb_strlen($this->metaDescription) > 255) {
			throw new UserInputException('metaDescription', 'tooLong');
		}
	}

	/**
	 * Validates the meta keywords.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateMetaKeywords() {
		if (mb_strlen($this->metaKeywords) > 255) {
			throw new UserInputException('metaKeywords', 'tooLong');
		}
	}

	/**
	 * Validates content text.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateText() {
		if (!I18nHandler::getInstance()->isPlainValue('text')) {
			if (!I18nHandler::getInstance()->validateValue('text')) {
				throw new UserInputException('text');
			}
			$textValues = I18nHandler::getInstance()->getValues('description');
			foreach ($textValues as $text) {
				if ($this->maxTextLength != 0 && mb_strlen($text) > $this->maxTextLength) {
					throw new UserInputException('text', 'tooLong');
				}
			}
		}
		else {
			parent::validateText();
		}
	}

	/**
	 * Validates category.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateCategories() {
		// reading cache
		$categoryIDs = CategoryCacheBuilder::getInstance()->getData(array(), 'categoryIDs');
		foreach ($this->categoryIDs as $categoryID) {
			if (in_array($categoryID, $categoryIDs)) continue;
			throw new UserInputException('category', 'invalidIDs');
			break;
		}
		// add default category
		if (empty($this->categoryIDs)) {
			$this->categoryIDs[] = 1;
		}
	}

	/**
	 * Validates the tags.
	 *
	 * @throws \wcf\system\exception\UserInputException
	 */
	protected function validateTags() {
		if (!I18nHandler::getInstance()->validateValue('tags', true, true)) {
			throw new UserInputException('tags');
		}

		foreach ($this->tagsI18n as $languageID => $tags) {
			$this->tagsI18n[$languageID] = (!empty($tags) ? Tag::splitString($tags) : array());
		}
	}

	/**
	 * Validates status.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateStatus() {
		// change status to planned or publish
		if ($this->saveType == 'publish') {
			if ($this->publishDateTimestamp > TIME_NOW) {
				$this->statusID = 2; // planned
				if (!isset($this->statusOptions[2])) $this->statusOptions[2] = WCF::getLanguage()->get('wcf.acp.ultimate.status.scheduled');
				if (isset($this->statusOptions[3])) unset($this->statusOptions[3]);
			} else if ($this->publishDateTimestamp < TIME_NOW) {
				$this->statusID = 3; // published
				if (isset($this->statusOptions[2])) unset($this->statusOptions[2]);
				if (!isset($this->statusOptions[3])) $this->statusOptions[3] = WCF::getLanguage()->get('wcf.acp.ultimate.status.published');
			}
		} else {
			if (isset($this->statusOptions[2])) unset($this->statusOptions[2]);
			if (isset($this->statusOptions[3])) unset($this->statusOptions[3]);
			$this->publishDateTimestamp = 0;
		}

		if (!array_key_exists($this->statusID, $this->statusOptions)) {
			throw new UserInputException('status', 'notValid');
		}
	}

	/**
	 * Validates the publish date.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 * @throws	\wcf\system\exception\SystemException
	 */
	protected function validatePublishDate() {
		if (empty($this->publishDate)) {
			throw new UserInputException('publishDate');
		}

		$pattern = '\d{4}-\d{2}-\d{2} \d{2}:\d{2}';
		$regex = new Regex($pattern);
		if ($regex->match($this->publishDate)) {
			// the browser has implemented the input type date
			// or (more likely) the user hasn't changed the jQuery code
			// that means we get the date in the right order for processing
			$dateTime = \DateTime::createFromFormat(
				'Y-m-d H:i',
				$this->publishDate,
				WCF::getUser()->getTimezone()
			);
			$this->publishDateTimestamp = $dateTime->format('U');
			return;
		}
	}
}
