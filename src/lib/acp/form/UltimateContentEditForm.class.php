<?php
namespace ultimate\acp\form;
use wcf\data\tag\Tag;

use wcf\system\tagging\TagEngine;

use ultimate\acp\form\UltimateContentAddForm;
use ultimate\data\content\Content;
use ultimate\data\content\ContentAction;
use ultimate\data\content\ContentEditor;
use wcf\form\AbstractForm;
use wcf\form\MessageForm;
use wcf\form\RecaptchaForm;
use wcf\system\bbcode\URLParser;
use wcf\system\cache\CacheHandler;
use wcf\system\language\I18nHandler;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Shows the UltimateContentEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateContentEditForm extends UltimateContentAddForm {
	/**
	 * @see	\wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.content';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
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
	 * @var	\ultimate\data\content\Content
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
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		// I18nHandler::getInstance()->disableAssignValueVariables();
		if (isset($_REQUEST['id'])) $this->contentID = intval($_REQUEST['id']);
		$content = new Content($this->contentID);
		if (!$content->__get('contentID')) {
			throw new IllegalLinkException();
		}
		
		$this->content = $content;
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
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
			
			if (!empty($_POST)) {
				// reading object fields
				$this->subject = $this->content->__get('contentTitle');
				$this->description = $this->content->__get('contentDescription');
				$this->slug = $this->content->__get('contentSlug');
				$this->text = $this->content->__get('contentText');
				$this->lastModified = $this->content->__get('lastModified');
				$this->categoryIDs = array_keys($this->content->getCategories());
				I18nHandler::getInstance()->setOptions('subject', PACKAGE_ID, $this->subject, 'ultimate.content.\d+.contentTitle');
				I18nHandler::getInstance()->setOptions('description', PACKAGE_ID, $this->description, 'ultimate.content.\d+.contentDescription');
				I18nHandler::getInstance()->setOptions('text', PACKAGE_ID, $this->text, 'ultimate.content.\d+.contentText');
			}
			parent::readData();
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
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
		foreach ($this->tags as $languageID => $tags) {
			TagEngine::getInstance()->addObjectTags('de.plugins-zum-selberbauen.ultimate.contentTaggable', $this->content->__get('contentID', $tags, $languageID));
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
	 * @see	\wcf\page\IPage::assignVariables()
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
	 * @see	\wcf\form\IForm::show()
	 */
	public function show() {
		if (!empty($this->activeMenuItem)) {
			ACPMenu::getInstance()->setActiveMenuItem($this->activeMenuItem);
		}
		MessageForm::show();
	}
}
