<?php
/**
 * Contains the UltimatePageAdd form.
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
use ultimate\data\content\ContentList;
use ultimate\data\page\PageAction;
use ultimate\data\page\PageEditor;
use ultimate\util\PageUtil;
use wcf\form\AbstractForm;
use wcf\system\cache\builder\UserGroupCacheBuilder;
use wcf\system\exception\UserInputException;
use wcf\system\exception\SystemException;
use wcf\system\language\I18nHandler;
use wcf\system\Regex;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\DateUtil;
use wcf\util\DateTimeUtil;
use wcf\util\StringUtil;

/**
 * Shows the UltimatePageAdd form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimatePageAddForm extends AbstractForm {
	/**
	 * @var	string[]
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.page.add';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$templateName
	 */
	public $templateName = 'ultimatePageAdd';
	
	/**
	 * @var	string[]
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canAddPage'
	);
	
	/**
	 * Contains the content id.
	 * @var	integer
	 */
	public $contentID = 0;
	
	/**
	 * Contains all available contents.
	 * @var	\ultimate\data\content\Content[]
	 */
	public $contents = array();
	
	/**
	 * Contains the title of the page.
	 * @var	string
	 */
	public $pageTitle = '';
	
	/**
	 * Contains the page slug.
	 * @var	string
	 */
	public $pageSlug = '';
	
	/**
	 * Contains the meta description.
	 * @var string
	 */
	public $metaDescription = '';
	
	/**
	 * Contains the meta keywords.
	 * @var string
	 */
	public $metaKeywords = '';
	
	/**
	 * Contains all possible parent pages.
	 * @var	\ultimate\data\page\Page[]
	 */
	public $pages = array();
	
	/**
	 * Contains the parent page id.
	 * @var	integer
	 */
	public $pageParent = 0;
	
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
	 * @see http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readParameters
	 */
	public function readParameters() {
		parent::readParameters();
		I18nHandler::getInstance()->register('pageTitle');
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		$this->contents = PageUtil::getAvailableContents();
		$this->pages = PageUtil::getAvailablePages();
		$this->groups = UserGroupCacheBuilder::getInstance()->getData(array(), 'groups');
		
		// fill status options
		$this->statusOptions = array(
			0 => WCF::getLanguage()->get('wcf.acp.ultimate.status.draft'),
			1 => WCF::getLanguage()->get('wcf.acp.ultimate.status.pendingReview'),
		);
		
		// fill publishDate with default value (today)
		$this->formatDate();
		
		parent::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#readFormParameters
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
		if (isset($_POST['visibility'])) $this->visibility = StringUtil::trim($_POST['visibility']);
		if (isset($_POST['groupIDs'])) $this->groupIDs = ArrayUtil::toIntegerArray($_POST['groupIDs']);
		if (isset($_POST['publishDate'])) $this->publishDate = StringUtil::trim($_POST['publishDate']);
		// if (isset($_POST['dateFormat'])) $this->dateFormat = StringUtil::trim($_POST['dateFormat']);
		if (isset($_POST['save'])) $this->saveType = 'save';
		if (isset($_POST['publish'])) $this->saveType = 'publish';
		if (isset($_POST['startTime'])) $this->startTime = intval($_POST['startTime']);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#validate
	 */
	public function validate() {
		parent::validate();
		$this->validateContentID();
		$this->validatePageParent();
		$this->validateTitle();
		$this->validateSlug();
		$this->validateMetaDescription();
		$this->validateMetaKeywords();
		$this->validatePublishDate();
		$this->validateStatus();
		$this->validateVisibility();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		parent::save();
		
		$parameters = array(
			'data' => array(
				'authorID' => WCF::getUser()->userID,
				'pageParent' => $this->pageParent,
				'pageTitle' => $this->pageTitle,
				'pageSlug' => $this->pageSlug,
				'publishDate' => $this->publishDateTimestamp,
				'lastModified' => TIME_NOW,
				'status' => $this->statusID,
				'visibility' => $this->visibility
			),
			'contentID' => $this->contentID,
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords
		);
		
		if ($this->visibility == 'protected') {
			$parameters['groupIDs'] = $this->groupIDs;
		}
		
		$this->objectAction = new PageAction(array(), 'create', $parameters);
		$this->objectAction->executeAction();
		
		if (!I18nHandler::getInstance()->isPlainValue('pageTitle')) {
			$returnValues = $this->objectAction->getReturnValues();
			$pageID = $returnValues['returnValues']->pageID;
			I18nHandler::getInstance()->save('pageTitle', 'ultimate.page.'.$pageID.'.pageTitle', 'ultimate.page', PACKAGE_ID);
		
			$pageEditor = new PageEditor($returnValues['returnValues']);
			$pageEditor->update(array(
				'pageTitle' => 'ultimate.page.'.$pageID.'.pageTitle'
			));
		}
		
		$this->saved();
		
		WCF::getTPL()->assign(
			'success', true
		);
		
		// showing empty form
		$this->contentID = $this->pageParent = $this->statusID = $this->publishDateTimestamp = 0;
		$this->pageTitle = $this->pageSlug = $this->publishDate = $this->metaDescription = $this->metaKeywords = '';
		$this->visibility = 'public';
		I18nHandler::getInstance()->reset();
		$this->contents = $this->pages = $this->groupIDs = array();
		$this->formatDate();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'contentID' => $this->contentID,
			'contents' => $this->contents,
			'pages' => $this->pages,
			'pageParent' => $this->pageParent,
			'pageSlug' => $this->pageSlug,
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords,
			'groups' => $this->groups,
			'groupIDs' => $this->groupIDs,
			'publishDate' => $this->publishDate,
			'statusOptions' => $this->statusOptions,
			'statusID' => $this->statusID,
			'visibility' => $this->visibility,
			'startTime' => $this->startTime,
			'action' => 'add'
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
		if (strlen($this->metaDescription) > 255) {
			throw new UserInputException('metaDescription', 'tooLong');
		}
	}
	
	/**
	 * Validates the meta keywords.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateMetaKeywords() {
		if (strlen($this->metaKeywords) > 255) {
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
			$this->publishDateTimestamp = $dateTime->getTimestamp();
			return;
		}
		// for the very unlikely reason that the date is not in the format
		// Y-m-d, we have to make it that way
		/* $phpDateFormat = DateTimeUtil::getPHPDateFormatFromDateTimePicker($this->dateFormat);
		$phpDateFormat .= ' H:i';
		$dateTime = \DateTime::createFromFormat(
			$phpDateFormat,
			$this->publishDate,
			WCF::getUser()->getTimezone()
		);
		$this->publishDateTimestamp = $dateTime->getTimestamp();
		*/
	}
}
