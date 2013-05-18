<?php
/**
 * Contains the UltimateContentAdd form.
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
use ultimate\data\content\ContentAction;
use ultimate\data\content\ContentEditor;
use ultimate\system\cache\builder\CategoryCacheBuilder;
use ultimate\system\cache\builder\ContentTagCloudCacheBuilder;
use ultimate\util\ContentUtil;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\tag\Tag;
use wcf\form\MessageForm;
use wcf\form\RecaptchaForm;
use wcf\system\bbcode\PreParser;
use wcf\system\cache\builder\UserGroupCacheBuilder;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\tagging\TagEngine;
use wcf\system\Regex;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\DateUtil;
use wcf\util\DateTimeUtil;
use wcf\util\MessageUtil;
use wcf\util\StringUtil;

/**
 * Shows the UltimateContentAdd form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateContentAddForm extends MessageForm {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$templateName
	 */
	public $templateName = 'ultimateContentAdd';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canAddContent'
	);
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.MessageForm.html#$enableMultilangualism
	*/
	public $enableMultilangualism = true;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.content.add';
	
	/**
	 * Contains the description of the content.
	 * @var	string
	 */
	public $description = '';
	
	/**
	 * Contains the slug of the content.
	 * @var	string
	 */
	public $slug = '';
	
	/**
	 * Contains the chosen categories.
	 * @var	integer[]
	 */
	public $categoryIDs = array();
	
	/**
	 * Contains all categories.
	 * @var	\ultimate\data\category\Category[]
	 */
	public $categories = array();
	
	/**
	 * Contains all tags.
	 * @var \wcf\data\tag\Tag[]
	 */
	public $availableTags = array();
	
	/**
	 * Contains all chosen tags.
	 * @var string
	 */
	public $tags = '';
	
	/**
	 * Contains the i18n tags.
	 * @var string[]|array[]
	 */
	public $tagsI18n = array();
	   
	/**
	 * Contains the maximal length of the text.
	 * @var	integer	0 means there's no limitation
	 */
	public $maxTextLength = 0;
	
	/**
	 * Contains the visibility.
	 * @var	string
	 */
	public $visibility = 'public';
	
	/**
	 * Contains the chosen groupIDs.
	 * @var	integer[]
	 */
	public $groupIDs = array();
	
	/**
	 * Contains all available groups.
	 * @var	\wcf\data\user\group\UserGroup[]
	*/
	public $groups = array();
	
	/**
	 * Contains the publish date.
	 * @var	string
	*/
	public $publishDate = '';
	
	/**
	 * Contains the publish date as timestamp.
	 * @var	integer
	 */
	public $publishDateTimestamp = TIME_NOW;
	
	/**
	 * Contains all status options.
	 * @var	string[]
	 */
	public $statusOptions = array();
	
	/**
	 * Contains the status id.
	 * @var	integer
	*/
	public $statusID = 0;
	
	/**
	 * Contains the save type.
	 * @var	string
	 */
	public $saveType = '';
	
	/**
	 * jQuery datepicker date format.
	 * @var	string
	 */
	protected $dateFormat = 'yy-mm-dd';
	
	/**
	 * Contains the timestamp from the begin of the add process.
	 * @var	integer
	 */
	protected $startTime = 0;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readParameters
	 */
	public function readParameters() {
		parent::readParameters();
		
		I18nHandler::getInstance()->register('subject');
		I18nHandler::getInstance()->register('description');
		I18nHandler::getInstance()->register('tags');
		I18nHandler::getInstance()->register('text');
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		$this->categories = CategoryCacheBuilder::getInstance()->getData(array(), 'categories');
		unset ($this->categories[1]);
		
		$this->groups = UserGroupCacheBuilder::getInstance()->getData(array(), 'groups');
		
		// read tags
		$tags = ContentTagCloudCacheBuilder::getInstance()->getData();
		
		$languages = WCF::getLanguage()->getLanguages();
		
		/* @var $language \wcf\data\language\Language */
		/* @var $tag \wcf\data\tag\TagCloudTag */
		foreach ($languages as $languageID => $language) {
			$this->availableTags[$languageID] = array();
			foreach ($tags as $tagID => $tag) {
				if ($tag->__get('languageID') != $languageID) continue;
				$this->availableTags[$languageID][] = $tag;
			}
		}
		
		// fill status options
		$this->statusOptions[0] = WCF::getLanguage()->get('wcf.acp.ultimate.status.draft');
		$this->statusOptions[1] = WCF::getLanguage()->get('wcf.acp.ultimate.status.pendingReview');
		
		// fill publishDate with default value (today)
		/* @var $dateTime \DateTime */
		$dateTime = null;
		if (isset($this->content)) {
			$dateTime = $this->content->__get('publishDateObject');
		}
		$this->formatDate($dateTime);
		
		parent::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#readFormParameters
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		if (I18nHandler::getInstance()->isPlainValue('subject')) $this->subject = StringUtil::trim(I18nHandler::getInstance()->getValue('subject'));
		if (I18nHandler::getInstance()->isPlainValue('description')) $this->description = StringUtil::trim(I18nHandler::getInstance()->getValue('description'));
		if (isset($_POST['slug'])) $this->slug = StringUtil::trim($_POST['slug']);
		if (isset($_POST['categoryIDs']) && is_array($_POST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray(($_POST['categoryIDs']));
		else $this->categoryIDs = array();
		$this->tagsI18n = I18nHandler::getInstance()->getValues('tags');
		if (I18nHandler::getInstance()->isPlainValue('text')) $this->text = MessageUtil::stripCrap(trim(I18nHandler::getInstance()->getValue('text')));
		if (isset($_POST['status'])) $this->statusID = intval($_POST['status']);
		if (isset($_POST['visibility'])) $this->visibility = StringUtil::trim($_POST['visibility']);
		if (isset($_POST['groupIDs'])) $this->groupIDs = ArrayUtil::toIntegerArray($_POST['groupIDs']);
		if (isset($_POST['publishDate'])) $this->publishDate = StringUtil::trim($_POST['publishDate']);
		//if (isset($_POST['dateFormat'])) $this->dateFormat = StringUtil::trim($_POST['dateFormat']);
		if (isset($_POST['save'])) $this->saveType = 'save';
		if (isset($_POST['publish'])) $this->saveType = 'publish';
		if (isset($_POST['startTime'])) $this->startTime = intval($_POST['startTime']);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#validate
	 */
	public function validate() {
		$this->validateSubject();
		$this->validateDescription();
		$this->validateSlug();
		$this->validateCategories();
		$this->validateTags();
		try {
			$this->validateText();
			// multilingualism
			$this->validateContentLanguage();
			$this->validatePublishDate();
			$this->validateStatus();
			$this->validateVisibility();
			RecaptchaForm::validate();
		}
		catch (UserInputException $e) {
			foreach ($this->tagsI18n as $languageID => $tags) {
				$this->tagsI18n[$languageID] = implode(',', $tags);
			}
			throw $e;
		}
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		if (!I18nHandler::getInstance()->isPlainValue('text')) RecaptchaForm::save();
		else parent::save();
		
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
			'categories' => $this->categoryIDs
		);
		
		if ($this->visibility == 'protected') {
			$parameters['groupIDs'] = $this->groupIDs;
		}
		
		$this->objectAction = new ContentAction(array(), 'create', $parameters);
		$this->objectAction->executeAction();
		
		$returnValues = $this->objectAction->getReturnValues();
		$contentID = $returnValues['returnValues']->contentID;
		$updateEntries = array();
		if (!I18nHandler::getInstance()->isPlainValue('subject')) {
			I18nHandler::getInstance()->save('subject', 'ultimate.content.'.$contentID.'.contentTitle', 'ultimate.content', PACKAGE_ID);
			$updateEntries['contentTitle'] = 'ultimate.content.'.$contentID.'.contentTitle';
		}
		if (!I18nHandler::getInstance()->isPlainValue('description')) {
			I18nHandler::getInstance()->save('description', 'ultimate.content.'.$contentID.'.contentDescription', 'ultimate.content', PACKAGE_ID);
			$updateEntries['contentDescription'] = 'ultimate.content.'.$contentID.'.contentDescription';
		}
		if (!I18nHandler::getInstance()->isPlainValue('text')) {
			I18nHandler::getInstance()->save('text', 'ultimate.content.'.$contentID.'.contentText', 'ultimate.content', PACKAGE_ID);
			$updateEntries['contentText'] = 'ultimate.content.'.$contentID.'.contentText';
			
			// parse URLs
			if ($this->preParse) {
				$textValues = I18nHandler::getInstance()->getValues('text');
				foreach ($textValues as $languageID => $text) {
					$textValues[$languageID] = PreParser::getInstance()->parse($text);
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
						'ultimate.content.'.$contentID.'.contentText',
						PACKAGE_ID
					));
				}
				WCF::getDB()->commitTransaction();
			}
		}
		if (!empty($updateEntries)) {
			$contentEditor = new ContentEditor($returnValues['returnValues']);
			$contentEditor->update($updateEntries);
		}
		
		// save tags
		foreach ($this->tagsI18n as $languageID => $tags) {
			if (empty($tags)) {
				$this->tagsI18n[$languageID] = '';
				continue;
			}
			TagEngine::getInstance()->addObjectTags('de.plugins-zum-selberbauen.ultimate.contentTaggable', $contentID, $tags, $languageID);
			$this->tagsI18n[$languageID] = implode(',', $tags);
		}
		
		$this->saved();
		
		WCF::getTPL()->assign('success', true);
		
		// showing empty form
		$this->subject = $this->description = $this->slug = $this->text = $this->publishDate = '';
		$this->publishDateTimestamp = $this->statusID = 0;
		$this->visibility = 'public';
		I18nHandler::getInstance()->reset();
		$this->categoryIDs = $this->groupIDs = array();
		$this->tags = '';
		$this->tagsI18n = array();
		$this->formatDate();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		ksort($this->statusOptions);
		WCF::getTPL()->assign(array(
			'description' => $this->description,
			'slug' => $this->slug,
			'action' => 'add',
			'categoryIDs' => $this->categoryIDs,
			'categories' => $this->categories,
			'languageID' => ($this->languageID ? $this->languageID : 0),
			'availableTags' => $this->availableTags,
			'tags' => $this->tags,
			'tagsI18n' => $this->tagsI18n,
			'groups' => $this->groups,
			'groupIDs' => $this->groupIDs,
			'statusOptions' => $this->statusOptions,
			'statusID' => $this->statusID,
			'visibility' => $this->visibility,
			'startTime' => $this->startTime,
			'publishDate' => $this->publishDate
		));
	}
	
	/**
	 * Formats the date.
	 * 
	 * @param	\DateTime	$dateTime	optional
	 */
	protected function formatDate(\DateTime $dateTime = null) {
		if ($dateTime === null) $dateTime = DateUtil::getDateTimeByTimestamp(TIME_NOW);
		$dateTime->setTimezone(WCF::getUser()->getTimezone());
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
				if (strlen($subject) < 4) {
					throw new UserInputException('subject', 'tooShort');
				}
			}
		} else {
			// checks if subject is empty; we don't have to do it twice
			parent::validateSubject();
	
			if (strlen($this->subject) < 4) {
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
			if (!I18nHandler::getInstance()->validateValue('description')) {
				throw new UserInputException('description');
			}
			$descriptionValues = I18nHandler::getInstance()->getValues('description');
			foreach ($descriptionValues as $languageID => $description) {
				if (strlen($description) < 4) {
					throw new UserInputException('description', 'tooShort');
				}
			}
		}
		else {
			if (empty($this->description)) {
				throw new UserInputException('description');
			}
	
			if (strlen($this->description) < 4) {
				throw new UserInputException('description', 'tooShort');
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
			foreach ($textValues as $languageID => $text) {
				if ($this->maxTextLength != 0 && strlen($text) > $this->maxTextLength) {
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
		if (!I18nHandler::getInstance()->validateValue('tags', true)) {
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
			} elseif ($this->publishDateTimestamp < TIME_NOW) {
				$this->statusID = 3; // published
				if (isset($this->statusOptions[2])) unset($this->statusOptions[2]);
				if (!isset($this->statusOptions[3])) $this->statusOptions[3] = WCF::getLanguage()->get('wcf.acp.ultimate.status.published');
			}
		} else {
			if (isset($this->statusOptions[2])) unset($this->statusOptions[2]);
			if (isset($this->statusOptions[3])) unset($this->statusOptions[3]);
		}
		
		if (!array_key_exists($this->statusID, $this->statusOptions)) {
			throw new UserInputException('status', 'notValid');
		}
	}
	
	/**
	 * Validates visibility.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateVisibility() {
		$allowedValues = array(
			'public',
			'protected',
			'private'
		);
		if (!in_array($this->visibility, $allowedValues)) {
			throw new UserInputException('visibility', 'notValid');
		}
		
		// validate groupIDs, only important for protected
		if ($this->visibility != 'protected') return;
		
		if (empty($this->groupIDs)) {
			throw new UserInputException('groupIDs', 'notSelected');
		}
		
		foreach ($this->groupIDs as $groupID) {
			if (array_key_exists($groupID, $this->groups)) continue;
			throw new UserInputException('groupIDs', 'notValid');
			break;
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
		$dateTimeNow = new \DateTime('@'.TIME_NOW, WCF::getUser()->getTimezone());
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
		// for the very unlikely reason that the date is not in the format
		// Y-m-d, we have to make it that way
		
		/*$phpDateFormat = DateTimeUtil::getPHPDateFormatFromDateTimePicker($this->dateFormat);
		$phpDateFormat .= ' H:i';
		$dateTime = \DateTime::createFromFormat(
			$phpDateFormat,
			$this->publishDate,
			WCF::getUser()->getTimezone()
		);
		$this->publishDateTimestamp = $dateTime->format('U');*/
	}
	
}
