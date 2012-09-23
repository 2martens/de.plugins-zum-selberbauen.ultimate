<?php
namespace ultimate\acp\form;
use ultimate\data\content\ContentList;
use ultimate\data\page\PageAction;
use ultimate\data\page\PageEditor;
use ultimate\util\PageUtil;
use wcf\acp\form\ACPForm;
use wcf\system\cache\CacheHandler;
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
class UltimatePageAddForm extends ACPForm {
	/**
	 * @var	string[]
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.acp.form.ACPForm.html#$activeMenuItem
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
		$cacheName = 'usergroups';
		$cacheBuilderClassName = '\wcf\system\cache\builder\UserGroupCacheBuilder';
		$file = WCF_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->groups = CacheHandler::getInstance()->get($cacheName, 'groups');
		
		// fill status options
		$this->statusOptions = array(
			0 => WCF::getLanguage()->get('wcf.acp.ultimate.status.draft'),
			1 => WCF::getLanguage()->get('wcf.acp.ultimate.status.pendingReview'),
		);
		
		// fill publishDate with default value (today)
		$this->startTime = TIME_NOW;
		$dateTime = DateUtil::getDateTimeByTimestamp(TIME_NOW);
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
		
		parent::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#readFormParameters
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		I18nHandler::getInstance()->enableAssignValueVariables();
		if (I18nHandler::getInstance()->isPlainValue('pageTitle')) $this->pageTitle = StringUtil::trim(I18nHandler::getInstance()->getValue('pageTitle'));
		if (isset($_POST['pageParent'])) $this->pageParent = intval($_POST['pageParent']);
		if (isset($_POST['content'])) $this->contentID = intval($_POST['content']);
		if (isset($_POST['pageSlug'])) $this->pageSlug = StringUtil::trim($_POST['pageSlug']);
		if (isset($_POST['visibility'])) $this->visibility = StringUtil::trim($_POST['visibility']);
		if (isset($_POST['groupIDs'])) $this->groupIDs = ArrayUtil::toIntegerArray($_POST['groupIDs']);
		if (isset($_POST['publishDate'])) $this->publishDate = StringUtil::trim($_POST['publishDate']);
		if (isset($_POST['dateFormat'])) $this->dateFormat = StringUtil::trim($_POST['dateFormat']);
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
		$this->validateStatus();
		$this->validateVisibility();
		$this->validatePublishDate();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		parent::save();
		
		// change status to planned or publish
		if ($this->saveType == 'publish') {
			if ($this->publishDateTimestamp > TIME_NOW) {
				$this->statusID = 2; // planned
			} elseif ($this->publishDateTimestamp < TIME_NOW) {
				$this->statusID = 3; // published
			}
		}
		
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
			'contentID' => $this->contentID
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
		$this->pageTitle = $this->pageSlug = $this->publishDate = '';
		$this->visibility = 'public';
		I18nHandler::getInstance()->disableAssignValueVariables();
		$this->contents = $this->pages = $this->groupIDs = array();
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
	 * Validates status.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateStatus() {
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
		$phpDateFormat = DateTimeUtil::getPHPDateFormatFromDateTimePicker($this->dateFormat);
		$phpDateFormat .= ' H:i';
		$dateTime = \DateTime::createFromFormat(
			$phpDateFormat,
			$this->publishDate,
			WCF::getUser()->getTimezone()
		);
		$this->publishDateTimestamp = $dateTime->getTimestamp();
	}
}
