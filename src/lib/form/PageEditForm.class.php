<?php
/**
 * The PageEdit form.
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
use ultimate\acp\form\UltimatePageAddForm;
use ultimate\data\page\language\PageLanguageEntryCache;
use ultimate\data\page\Page;
use ultimate\data\page\PageAction;
use ultimate\util\PageUtil;
use wcf\form\AbstractForm;
use wcf\system\acl\ACLHandler;
use wcf\system\cache\builder\UserGroupCacheBuilder;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\request\UltimateLinkHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\HeaderUtil;

/**
 * Shows the UltimatePageEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	form
 * @category	Ultimate CMS
 */
class PageEditForm extends PageAddForm {
	/**
	 * Array of needed permissions.
	 * @var string[]
	 */
	public $neededPermissions = array(
		'user.ultimate.content.canEditPage'
	);

	public $action = 'edit';
	
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
		
		// get status data
		$this->statusID = $this->page->__get('status');
		$this->statusOptions = array(
			0 => WCF::getLanguage()->get('wcf.acp.ultimate.status.draft'),
			1 => WCF::getLanguage()->get('wcf.acp.ultimate.status.pendingReview'),
		);
		
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
		
		$this->contentID = $this->page->getContent()->__get('contentID');
		$this->pageTitle = $this->page->__get('pageTitle');
		$this->pageSlug = $this->page->__get('pageSlug');
		$this->pageParent = $this->page->__get('pageParent');
		$this->lastModified = $this->page->__get('lastModified');
		
		// prepare I18nHandler
		if (!PageLanguageEntryCache::getInstance()->isNeutralValue($this->page->__get('pageID'), 'pageTitle')) {
			$pageTitle = PageLanguageEntryCache::getInstance()->getValues($this->page->__get('pageID'), 'pageTitle');
			$this->i18nValues['pageTitle'] = $pageTitle;
		}
		else {
			$this->i18nPlainValues['pageTitle'] = $this->pageTitle;
		}

		// read meta data
		$metaData = $this->page->__get('metaData');
		if (!empty($metaData)) {
			$this->metaDescription = (isset($metaData['metaDescription']) ? $metaData['metaDescription'] : '');
			$this->metaKeywords = (isset($metaData['metaKeywords']) ? $metaData['metaKeywords'] : '');
		}
	}
	
	/**
	 * Saves the form input.
	 * @see UltimatePageAddForm::save()
	 */
	public function save() {
		AbstractForm::save();
		
		// retrieve I18n values
		$pageTitle = array();
		// for the time being the existing entries will be removed, if an array entry with id 0 is provided
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
		
		$this->objectAction = new PageAction(array($this->pageID), 'update', $parameters);
		$this->objectAction->executeAction();

		// save ACL
		ACLHandler::getInstance()->save($this->pageID, $this->objectTypeID);
		UserStorageHandler::getInstance()->resetAll('ultimatePagePermissions');
		
		$this->saved();
		
		$dateTime = DateUtil::getDateTimeByTimestamp($this->publishDateTimestamp);
		$this->formatDate($dateTime);

		$url = UltimateLinkHandler::getInstance()->getLink('PageEdit',
			array(
				'id' => $this->page->__get('pageID'),
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
	 * Assigns the template variables.
	 * @see UltimatePageAddForm::assignVariables()
	 */
	public function assignVariables() {
		WCF::getTPL()->assign(array(
			'pageID' => $this->pageID,
			'publishButtonLang' => WCF::getLanguage()->get($this->publishButtonLang),
			'saveButtonLang' => $this->saveButtonLang,
			'publishButtonLangRaw' => $this->publishButtonLang
		));

		WCF::getTPL()->assign(array(
			'initialController' => 'PageEditForm',
			'initialURL' => '/EditSuite/PageEdit/'.$this->pageID.'/'
		));

		if ($this->success) {
			WCF::getTPL()->assign('success', true);
		}

		// fix for the broken assignment system (magic is in the works here); mind the capital I at the beginning
		WCF::getTPL()->assign(array(
			'I18nValues' => $this->i18nValues,
			'I18nPlainValues' => $this->i18nPlainValues
		));

		parent::assignVariables();
	}
	
}
