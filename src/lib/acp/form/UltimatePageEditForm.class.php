<?php
/**
 * The UltimatePageEdit form.
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\acp\form\UltimatePageAddForm;
use ultimate\data\page\Page;
use ultimate\data\page\PageAction;
use ultimate\data\page\PageEditor;
use ultimate\util\PageUtil;
use wcf\form\AbstractForm;
use wcf\system\cache\builder\UserGroupCacheBuilder;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Shows the UltimatePageEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimatePageEditForm extends UltimatePageAddForm {
	/**
	 * The active menu item.
	 * @var string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.page';
	
	/**
	 * Array of needed permissions.
	 * @var string[]
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canEditPage'
	);
	
	/**
	 * The page id.
	 * @var	integer
	 */
	public $pageID = 0;
	
	/**
	 * The Page object of this page.
	 * @var	\ultimate\data\page\Page
	 */
	public $page = null;
	
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
	 * @see UltimatePageAddForm::readParameters()
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
	 * Reads data.
	 * @see UltimatePageAddForm::readData()
	 */
	public function readData() {
		$this->contents = PageUtil::getAvailableContents($this->pageID);
		$this->pages = PageUtil::getAvailablePages($this->pageID);
		
		// reading cache
		$this->groups = UserGroupCacheBuilder::getInstance()->getData(array(), 'groups');
			
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
		} else if ($this->statusID == 3) {
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
			$metaData = $this->page->__get('metaData');
			$this->metaDescription = $metaData['metaDescription'];
			$this->metaKeywords = $metaData['metaKeywords'];
			
			I18nHandler::getInstance()->setOptions('pageTitle', PACKAGE_ID, $this->pageTitle, 'ultimate.page.\d+.pageTitle');
		}
		AbstractForm::readData();
	}
	
	/**
	 * Saves the form input.
	 * @see UltimatePageAddForm::save()
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
			'contentID' => $this->contentID,
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords
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
	 * Assigns the template variables.
	 * @see UltimatePageAddForm::assignVariables()
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
