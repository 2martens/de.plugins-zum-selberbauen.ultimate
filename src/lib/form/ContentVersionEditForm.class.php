<?php
/**
 * Contains the ContentVersionEditForm class.
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
use ultimate\data\content\language\ContentLanguageEntryCache;
use ultimate\data\content\version\ContentVersion;
use ultimate\data\content\CategorizedContent;
use ultimate\data\content\Content;
use ultimate\data\content\ContentAction;
use wcf\data\tag\Tag;
use wcf\form\AbstractCaptchaForm;
use wcf\form\MessageForm;
use wcf\system\bbcode\PreParser;
use wcf\system\cache\builder\TagObjectCacheBuilder;
use wcf\system\cache\builder\TypedTagCloudCacheBuilder;
use wcf\system\cache\builder\UltimateTagCloudCacheBuilder;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\request\UltimateLinkHandler;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Provides a form to edit a new content version.
 *
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	form
 * @category	Ultimate CMS
 */
class ContentVersionEditForm extends ContentVersionAddForm {
	public $action = 'edit';

	/**
	 * Array of needed permissions.
	 * @var string[]
	 */
	public $neededPermissions = array(
		'user.ultimate.editing.canAccessEditSuite',
		'user.ultimate.editing.canEditContentVersion'
	);

	/**
	 * The version ID.
	 * @var integer
	 */
	public $versionID = 0;

	/**
	 * The version object
	 * @var \ultimate\data\content\version\ContentVersion
	 */
	public $version = null;

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
	 * A list of active EditSuite menu items.
	 * @var string[]
	 */
	protected $activeMenuItems = array(
		'ContentListPage',
		'ultimate.edit.contents'
	);

	/**
	 * Reads parameters.
	 */
	public function readParameters() {
		MessageForm::readParameters();

		if (isset($_REQUEST['success'])) $this->success = true;

		I18nHandler::getInstance()->register('subject');
		I18nHandler::getInstance()->register('description');
		I18nHandler::getInstance()->register('tags');
		I18nHandler::getInstance()->register('text');
		
		// the 'edit' part
		if (isset($_REQUEST['id'])) $this->versionID = intval($_REQUEST['id']);

		$version = new ContentVersion($this->versionID);
		if (!$version->__get('versionID')) {
			throw new IllegalLinkException();
		}

		$this->version = $version;
		
		// reading content data
		$this->contentID = $this->version->contentID;
		$this->content = new CategorizedContent(new Content($this->contentID));
		
		// set attachment object id
		$this->attachmentObjectID = $this->contentID;
	}

	/**
	 * Reads data.
	 */
	public function readData() {
		// get status data
		$this->statusID = $this->version->__get('status');

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
	}

	/**
	 * Saves the form input.
	 */
	public function save() {
		AbstractCaptchaForm::save();

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
			$contentText[ContentLanguageEntryCache::NEUTRAL_LANGUAGE] = ($this->preParse ? PreParser::getInstance()->parse($this->text) : $this->text);
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
			'attachmentHandler' => $this->attachmentHandler,
			'versionID' => $this->versionID
		);

		$this->objectAction = new ContentAction(array($this->contentID), 'updateVersion', $parameters);
		$this->objectAction->executeAction();

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
				'id' => $this->versionID,
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
		WCF::getTPL()->assign(array(
			'versionID' => $this->versionID,
			'attachmentObjectID' => $this->contentID,
			'publishButtonLang' => WCF::getLanguage()->get($this->publishButtonLang),
			'saveButtonLang' => $this->saveButtonLang
		));
		
		WCF::getTPL()->assign(array(
			'initialController' => 'ContentVersionEditForm',
			'initialURL' => '/EditSuite/ContentVersionEdit/'.$this->versionID.'/'
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
