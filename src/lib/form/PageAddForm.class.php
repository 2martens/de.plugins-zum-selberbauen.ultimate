<?php
/**
 * The PageAdd form.
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
 * @subpackage	form
 * @category	Ultimate CMS
 */
namespace ultimate\form;
use ultimate\data\page\language\PageLanguageEntryCache;
use ultimate\data\page\PageAction;
use ultimate\page\IEditSuitePage;
use ultimate\util\PageUtil;
use wcf\form\AbstractForm;
use wcf\system\acl\ACLHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\Regex;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\StringUtil;

/**
 * Provides a form to add a new page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	form
 * @category	Ultimate CMS
 */
class PageAddForm extends AbstractForm implements IEditSuitePage {
	/**
	 * The template name.
	 * @var string
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
	 * @var	string[]
	 */
	public $neededPermissions = array(
		'user.ultimate.content.canEditPage'
	);

	/**
	 * The object type id.
	 * @var	integer
	 */
	public $objectTypeID = 0;

	/**
	 * The content id.
	 * @var	integer
	 */
	public $contentID = 0;
	
	/**
	 * All available contents.
	 * @var	\ultimate\data\content\Content[]
	 */
	public $contents = array();
	
	/**
	 * The title of the page.
	 * @var	string
	 */
	public $pageTitle = '';
	
	/**
	 * The page slug.
	 * @var	string
	 */
	public $pageSlug = '';
	
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
	 * All possible parent pages.
	 * @var	\ultimate\data\page\Page[]
	 */
	public $pages = array();
	
	/**
	 * The parent page id.
	 * @var	integer
	 */
	public $pageParent = 0;
	
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
	 * If true, creating a page was successfully.
	 * @var boolean
	 */
	protected $success = false;

	/**
	 * A list of active EditSuite menu items.
	 * @var string[]
	 */
	protected $activeMenuItems = array(
		'PageAddForm',
		'ultimate.edit.contents'
	);

	/**
	 * Contains the i18nValues.
	 * @var string[][]
	 */
	protected $i18nValues = array(
		'pageTitle' => array()
	);

	/**
	 * Contains the i18nValues.
	 * @var string[]
	 */
	protected $i18nPlainValues = array(
		'pageTitle' => ''
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
		return WCF::getTPL()->fetch('__editSuiteJS.PageAddForm', 'ultimate');
	}
	
	/**
	 * Reads parameters.
	 */
	public function readParameters() {
		parent::readParameters();
		I18nHandler::getInstance()->register('pageTitle');
		if (isset($_REQUEST['success'])) $this->success = true;

		$this->objectTypeID = ACLHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.page');
	}
	
	/**
	 * Reads data.
	 */
	public function readData() {
		// fill status options
		$this->statusOptions = array(
			0 => WCF::getLanguage()->get('wcf.acp.ultimate.status.draft'),
			1 => WCF::getLanguage()->get('wcf.acp.ultimate.status.pendingReview'),
		);

		parent::readData();

		// fill publishDate with default value (today)
		/* @var $dateTime \DateTime */
		$dateTime = null;
		if (isset($this->page)) {
			$dateTime = $this->page->__get('publishDateObject');
		}
		$this->formatDate($dateTime);

		$this->contents = PageUtil::getAvailableContents();
		$this->pages = PageUtil::getAvailablePages();
	}
	
	/**
	 * Reads form input.
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		if (I18nHandler::getInstance()->isPlainValue('pageTitle')) $this->pageTitle = StringUtil::trim(I18nHandler::getInstance()->getValue('pageTitle'));
		if (isset($_POST['pageParent'])) $this->pageParent = intval($_POST['pageParent']);
		if (isset($_POST['content'])) $this->contentID = intval($_POST['content']);
		if (isset($_POST['pageSlug'])) $this->pageSlug = StringUtil::trim($_POST['pageSlug']);
		if (isset($_POST['metaDescription'])) $this->metaDescription = StringUtil::trim($_POST['metaDescription']);
		if (isset($_POST['metaKeywords'])) $this->metaKeywords = StringUtil::trim($_POST['metaKeywords']);
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
		parent::validate();
		$this->validateContentID();
		$this->validatePageParent();
		$this->validateTitle();
		$this->validateSlug();
		$this->validateMetaDescription();
		$this->validateMetaKeywords();
		$this->validatePublishDate();
		$this->validateStatus();
	}
	
	/**
	 * Saves the input.
	 */
	public function save() {
		parent::save();
		
		// retrieve I18n values
		$pageTitle = array();
		if (I18nHandler::getInstance()->isPlainValue('pageTitle')) {
			$pageTitle[PageLanguageEntryCache::NEUTRAL_LANGUAGE] = $this->pageTitle;
		}
		else {
			$pageTitle = I18nHandler::getInstance()->getValues('pageTitle');
		}
		
		$parameters = array(
			'data' => array(
				'authorID' => WCF::getUser()->userID,
				'pageParent' => $this->pageParent,
				'pageTitle' => $pageTitle,
				'pageSlug' => $this->pageSlug,
				'publishDate' => $this->publishDateTimestamp,
				'lastModified' => TIME_NOW,
				'status' => $this->statusID
			),
			'contentID' => $this->contentID,
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords
		);
		
		$this->objectAction = new PageAction(array(), 'create', $parameters);
		$this->objectAction->executeAction();

		$returnValues = $this->objectAction->getReturnValues();
		/* @var \ultimate\data\content\Content $content */
		$page = $returnValues['returnValues'];
		$pageID = $page->pageID;

		// save ACL
		ACLHandler::getInstance()->save($pageID, $this->objectTypeID);
		UserStorageHandler::getInstance()->resetAll('ultimatePagePermissions');

		$this->saved();

		WCF::getTPL()->assign('success', true);
		
		// showing empty form
		$this->pageTitle = $this->pageSlug = $this->metaDescription = $this->metaKeywords = '';
		$this->publishDateTimestamp = $this->statusID = 0;
		I18nHandler::getInstance()->reset();
		$this->formatDate();

		ACLHandler::getInstance()->disableAssignVariables();
	}
	
	/**
	 * Assigns template variables.
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

		ACLHandler::getInstance()->assignVariables($this->objectTypeID);
		WCF::getTPL()->assign(array(
			'contentID' => $this->contentID,
			'contents' => $this->contents,
			'pages' => $this->pages,
			'pageParent' => $this->pageParent,
			'pageSlug' => $this->pageSlug,
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords,
			'publishDate' => $this->publishDate,
			'statusOptions' => $this->statusOptions,
			'statusID' => $this->statusID,
			'startTime' => $this->startTime,
			'objectTypeID' => $this->objectTypeID
		));

		WCF::getTPL()->assign(array(
			'activeMenuItems' => $this->activeMenuItems,
			'pageContent' => WCF::getTPL()->fetch('__editSuite.PageAddForm', 'ultimate'),
			'pageJS' => WCF::getTPL()->fetch('__editSuiteJS.PageAddForm', 'ultimate'),
			'initialController' => 'PageAddForm',
			'initialRequestType' => 'form',
			'initialURL' => '/EditSuite/PageAdd/'
		));
		
		if ($this->success) {
			WCF::getTPL()->assign('success', true);
		}
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
	 * Formats the date.
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
	 * Validates the contentID.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateContentID() {
		if (!$this->contentID) {
			throw new UserInputException('content', 'notSelected');
		}
		if (!array_key_exists($this->contentID, $this->contents)) {
			throw new UserInputException('content', 'notValid');
		}
	}
	
	/**
	 * Validates the parent page.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validatePageParent() {
		if ($this->pageParent != 0 && !array_key_exists($this->pageParent, $this->pages)) {
			throw new UserInputException('pageParent', 'notValid');
		}
	}
	
	/**
	 * Validates the page title.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateTitle() {
		if (!I18nHandler::getInstance()->isPlainValue('pageTitle')) {
			if (!I18nHandler::getInstance()->validateValue('pageTitle')) {
				throw new UserInputException('pageTitle');
			}
		}
		else {
			if (empty($this->pageTitle)) {
				throw new UserInputException('pageTitle');
			}
			if (!PageUtil::isAvailableTitle($this->pageTitle, (isset($this->pageID) ? $this->pageID : 0), $this->pageParent)) {
				throw new UserInputException('pageTitle', 'notUnique');
			}
		}
	}
	
	/**
	 * Validates page slug.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateSlug() {
		if (empty($this->pageSlug)) {
			throw new UserInputException('pageSlug');
		}
	
		if (!PageUtil::isAvailableSlug($this->pageSlug, (isset($this->pageID) ? $this->pageID : 0), $this->pageParent)) {
			throw new UserInputException('pageSlug', 'notUnique');
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
	 * Validates status.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateStatus() {
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
			$this->publishDateTimestamp = $dateTime->getTimestamp();
			return;
		}
	}
}
