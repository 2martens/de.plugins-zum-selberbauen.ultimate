<?php
namespace ultimate\acp\form;
use ultimate\acp\form\UltimatePageAddForm;
use ultimate\data\page\Page;
use ultimate\data\page\PageAction;
use ultimate\data\page\PageEditor;
use ultimate\util\PageUtil;
use wcf\form\AbstractForm;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Shows the UltimatePageEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimatePageEditForm extends UltimatePageAddForm {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.acp.form.ACPForm.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.page';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canEditPage'
	);
	
	/**
	 * Contains the page id.
	 * @var	integer
	 */
	public $pageID = 0;
	
	/**
	 * Contains the Page object of this page.
	 * @var	\ultimate\data\page\Page
	 */
	public $page = null;
	
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
		if (isset($_REQUEST['id'])) $this->pageID = intval($_REQUEST['id']);
		$page = new Page($this->pageID);
		if (!$page->__get('pageID')) {
			throw new IllegalLinkException();
		}
		
		$this->page = $page;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		$this->contents = PageUtil::getAvailableContents($this->pageID);
		$this->pages = PageUtil::getAvailablePages($this->pageID);
		
		// reading cache
		$cacheName = 'usergroups';
		$cacheBuilderClassName = '\wcf\system\cache\builder\UserGroupCacheBuilder';
		$file = WCF_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->groups = CacheHandler::getInstance()->get($cacheName, 'groups');
			
		/* @var $dateTime \DateTime */
		$dateTime = $this->page->__get('publishDateObject');
		$this->formatDate($dateTime);
			
		// get status data
		$this->statusID = $this->page->__get('status');
		$this->statusOptions = array(
			0 => WCF::getLanguage()->get('wcf.acp.ultimate.status.draft'),
			1 => WCF::getLanguage()->get('wcf.acp.ultimate.status.pendingReview'),
		);
			
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
		$this->visibility = $this->page->__get('visibility');
		$this->groupIDs = array_keys($this->page->__get('groups'));
		
		if (empty($_POST)) {
			$this->contentID = $this->page->getContent()->__get('contentID');
			$this->pageTitle = $this->page->__get('pageTitle');
			$this->pageSlug = $this->page->__get('pageSlug');
			$this->pageParent = $this->page->__get('pageParent');
			$this->lastModified = $this->page->__get('lastModified');
			
			I18nHandler::getInstance()->setOptions('pageTitle', PACKAGE_ID, $this->pageTitle, 'ultimate.page.\d+.pageTitle');
		}
		AbstractForm::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		AbstractForm::save();
		
		$this->pageTitle = 'ultimate.page.'.$this->pageID.'.pageTitle';
		if (I18nHandler::getInstance()->isPlainValue('pageTitle')) {
			I18nHandler::getInstance()->remove($this->pageTitle, PACKAGE_ID);
			$this->pageTitle = I18nHandler::getInstance()->getValue('pageTitle');
		} else {
			I18nHandler::getInstance()->save('pageTitle', $this->pageTitle, 'ultimate.page', PACKAGE_ID);
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
		
		$this->objectAction = new PageAction(array($this->pageID), 'update', $parameters);
		$this->objectAction->executeAction();
		
		$this->saved();
		
		$dateTime = DateUtil::getDateTimeByTimestamp($this->publishDateTimestamp);
		$this->formatDate($dateTime);
		
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
			'pageID' => $this->pageID,
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
	
}
