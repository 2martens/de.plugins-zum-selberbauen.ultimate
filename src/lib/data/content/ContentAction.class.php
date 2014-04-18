<?php
/**
 * Contains the content data model action class.
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
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use ultimate\data\layout\LayoutAction;
use ultimate\data\layout\LayoutList;
use ultimate\system\cache\builder\ContentCacheBuilder;
use ultimate\system\layout\LayoutHandler;
use wcf\data\smiley\SmileyCache;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IMessageInlineEditorAction;
use wcf\data\IVersionableDatabaseObjectAction;
use wcf\system\bbcode\BBCodeHandler;
use wcf\system\bbcode\BBCodeParser;
use wcf\system\bbcode\PreParser;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\search\SearchIndexManager;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Executes content-related functions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class ContentAction extends AbstractDatabaseObjectAction implements IMessageInlineEditorAction, IVersionableDatabaseObjectAction {
	/**
	 * The class name.
	 * @var	string
	 */
	public $className = '\ultimate\data\content\ContentEditor';
	
	/**
	 * Array of permissions that are required for create action.
	 * @var	string[]
	 */
	protected $permissionsCreate = array(
		'admin.content.ultimate.canAddContent',		
		'admin.content.ultimate.canAddContentVersion'
	);
	
	/**
	 * Array of permissions that are required for delete action.
	 * @var	string[]
	 */
	protected $permissionsDelete = array(
		'admin.content.ultimate.canDeleteContent', 
		'admin.content.ultimate.canDeleteContentVersion'
	);
	
	/**
	 * Array of permissions that are required for update action.
	 * @var	string[]
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canEditContent');
	
	/**
	 * disallow requests for specified methods if the origin is not the ACP
	 * @var	string[]
	 */
	protected $requireACP = array('create', 'updateSearchIndex', 'delete');
	
	/**
	 * current content object
	 * @var	\ultimate\data\content\Content
	 */
	protected $content = null;
	
	/**
	 * Creates new content.
	 * 
	 * @return	\ultimate\data\content\Content
	 */
	public function create() {
		if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
			$this->parameters['data']['attachments'] = count($this->parameters['attachmentHandler']);
		}
		
		// separating core content data from version data
		$contentParameters = array(
			'contentID', 
			'contentSlug', 
			'authorID', 
			'cumulativeLikes', 
			'views', 
			'lastModified'
		);
		
		$versionData = array();
		$tmpData = $this->parameters['data'];
		foreach ($tmpData as $key => $value) {
			if (in_array($key, $contentParameters)) continue;
			$versionData[$key] = $value;
			unset($this->parameters['data'][$key]);
		}
		
		$content = parent::create();
		$contentEditor = new ContentEditor($content);
		// create version
		$versionData['contentID'] = $content->__get('contentID');
		$version = $contentEditor->createVersion($versionData);
		
		// update attachments
		if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
			$this->parameters['attachmentHandler']->updateObjectID($content->__get('contentID'));
		}
		
		// insert categories
		$categoryIDs = (isset($this->parameters['categories'])) ? $this->parameters['categories'] : array();
		$contentEditor->addToCategories($categoryIDs, false);
		
		// connect with userGroups
		$groupIDs = (isset($this->parameters['groupIDs'])) ? ArrayUtil::toIntegerArray($this->parameters['groupIDs']) : array();
		if (!empty($groupIDs)) {
			$contentEditor->addGroups($version->__get('versionID'), $groupIDs);
		}
		
		// insert meta description/keywords
		$metaDescription = (isset($this->parameters['metaDescription'])) ? $this->parameters['metaDescription'] : '';
		$metaKeywords = (isset($this->parameters['metaKeywords'])) ? $this->parameters['metaKeywords'] : '';
		$contentEditor->addMetaData($metaDescription, $metaKeywords);
		
		return $content;
	}
	
	/**
	 * @see \wcf\data\IVersionableDatabaseObjectAction::createVersion()
	 */
	public function createVersion() {
		if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
			$this->parameters['data']['attachments'] = count($this->parameters['attachmentHandler']);
		}
		
		// separating core content data from version data
		$contentParameters = array(
			'contentID',
			'contentSlug',
			'authorID',
			'cumulativeLikes',
			'views',
			'lastModified'
		);
		
		$versionData = array();
		$tmpData = $this->parameters['data'];
		foreach ($tmpData as $key => $value) {
			if (in_array($key, $contentParameters)) continue;
			$versionData[$key] = $value;
			unset($this->parameters['data'][$key]);
		}
		
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		// update object
		$versions = array();
		if (isset($this->parameters['data'])) {
			foreach ($this->objects as $object) {
				$object->update($this->parameters['data']);
				if (isset($this->parameters['counters'])) {
					$object->updateCounters($this->parameters['counters']);
				}
				// create new version
				$versionData['contentID'] = $object->__get('contentID');
				$versions[$object->getObjectID()] = $object->createVersion($versionData);
			}
		}
		
		$categoryIDs = (isset($this->parameters['categories'])) ? $this->parameters['categories'] : array();
		$removeCategories = (isset($this->parameters['removeCategories'])) ? $this->parameters['removeCategories'] : array();
		$groupIDs = (isset($this->parameters['groupIDs'])) ? ArrayUtil::toIntegerArray($this->parameters['groupIDs']) : array();
		$metaDescription = (isset($this->parameters['metaDescription'])) ? $this->parameters['metaDescription'] : '';
		$metaKeywords = (isset($this->parameters['metaKeywords'])) ? $this->parameters['metaKeywords'] : '';
		
		foreach ($this->objects as $contentEditor) {
			/* @var $contentEditor \ultimate\data\content\ContentEditor */
			if (!empty($categoryIDs)) {
				$contentEditor->addToCategories($categoryIDs);
			}
			
			if (!empty($removeCategories)) {
				$contentEditor->removeFromCategories($removeCategories);
			}
			
			if (!empty($groupIDs)) {
				$contentEditor->addGroups($versions[$contentEditor->getObjectID()]->__get('versionID'), $groupIDs);
			}
			
			$contentEditor->addMetaData($metaDescription, $metaKeywords);
		}
	}
	
	/**
	 * @see \wcf\data\IVersionableDatabaseObjectAction::validateCreateVersion()
	 */
	public function validateCreateVersion() {
		WCF::getSession()->checkPermissions(array('admin.content.ultimate.canAddContentVersion'));
	}
	
	/**
	 * Updates search index.
	 */
	public function updateSearchIndex() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		// update search index
		foreach ($this->objects as $contentEditor) {
			$languages = LanguageFactory::getInstance()->getLanguages();
			SearchIndexManager::getInstance()->delete('de.plugins-zum-selberbauen.ultimate.content', array($contentEditor->__get('contentID')));
			foreach ($languages as $languageID => $language) {
				$text = $language->get($contentEditor->__get('contentText'));
				$title = $language->get($contentEditor->__get('contentTitle'));
				$isI18n = (strpos($contentEditor->__get('contentText'), 'ultimate.content') !== false || strpos($contentEditor->__get('contentTitle'), 'ultimate.content') !== false);
				SearchIndexManager::getInstance()->add(
					'de.plugins-zum-selberbauen.ultimate.content',
					$contentEditor->__get('contentID'),
					(!empty($text) ? $text : $contentEditor->__get('contentText')),
					(!empty($title) ? $title : $contentEditor->__get('contentTitle')),
					$contentEditor->getTime(),
					$contentEditor->getUserID(),
					$contentEditor->getUsername(),
					($isI18n ? $languageID : null)
				);
				if (!$isI18n) {
					break;
				}
			}
		}
	}
	
	/**
	 * Updates one or more objects.
	 */
	public function update() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		$versions = array();
		if (isset($this->parameters['data'])) {
			if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
				$this->parameters['data']['attachments'] = count($this->parameters['attachmentHandler']);
			}
			
			// separating core content data from version data
			$contentParameters = array(
				'contentID',
				'contentSlug',
				'authorID',
				'cumulativeLikes',
				'views',
				'lastModified'
			);
			
			$versionData = array();
			$tmpData = $this->parameters['data'];
			foreach ($tmpData as $key => $value) {
				if (in_array($key, $contentParameters)) continue;
				$versionData[$key] = $value;
				unset($this->parameters['data'][$key]);
			}
			
			foreach ($this->objects as $object) {
				$object->update($this->parameters['data']);
				$versions[$objectID] = $object->getCurrentVersion();
				$object->updateVersion($versions[$object->getObjectID()]->__get('versionID'), $versionData);
			}
		}
		
		if (isset($this->parameters['counters'])) {
			foreach ($this->objects as $object) {
				$object->updateCounters($this->parameters['counters']);
			}
		}
		
		$categoryIDs = (isset($this->parameters['categories'])) ? $this->parameters['categories'] : array();
		$removeCategories = (isset($this->parameters['removeCategories'])) ? $this->parameters['removeCategories'] : array();
		$groupIDs = (isset($this->parameters['groupIDs'])) ? ArrayUtil::toIntegerArray($this->parameters['groupIDs']) : array();
		$metaDescription = (isset($this->parameters['metaDescription'])) ? $this->parameters['metaDescription'] : '';
		$metaKeywords = (isset($this->parameters['metaKeywords'])) ? $this->parameters['metaKeywords'] : '';
		
		foreach ($this->objects as $contentEditor) {
			/* @var $contentEditor \ultimate\data\content\ContentEditor */
			if (!empty($categoryIDs)) {
				$contentEditor->addToCategories($categoryIDs);
			}
			
			if (!empty($removeCategories)) {
				$contentEditor->removeFromCategories($removeCategories);
			}
			
			if (!empty($groupIDs)) {
				$contentEditor->addGroups($versions[$contentEditor->getObjectID()], $groupIDs);
			}
			
			$contentEditor->addMetaData($metaDescription, $metaKeywords);
		}
	}
	
	/**
	 * Deletes one or more objects.
	 */
	public function delete() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
	
		// get ids
		$objectIDs = array();
		foreach ($this->objects as $object) {
			$objectIDs[] = $object->getObjectID();
		}
		
		// update search index
		SearchIndexManager::getInstance()->delete('de.plugins-zum-selberbauen.ultimate.content', $objectIDs);
		
		$layoutIDs = array();
		foreach ($this->objects as $object) {
			/* @var $layout \ultimate\data\layout\Layout */
			$layout = null;
			if (defined('TESTING_MODE') && TESTING_MODE) {
				$layoutList = new LayoutList();
				$layoutList->readObjects();
				$layouts = $layoutList->getObjects();
				foreach ($layouts as $__layout) {
					if ($__layout->__get('objectID') == $object->__get('contentID') && $__layout->__get('objectType') == 'content') {
						$layout = $__layout;
					}
				}
			}
			else {
				$layout = LayoutHandler::getInstance()->getLayoutFromObjectData($object->__get('contentID'), 'content');
			}
			$layoutIDs[] = $layout->__get('layoutID');
		}
		$layoutAction = new LayoutAction($layoutIDs, 'delete', array());
		$layoutAction->executeAction();
		// execute action
		return call_user_func(array($this->className, 'deleteAll'), $objectIDs);
	}
	
	/**
	 * @see \wcf\data\IVersionableDatabaseObjectAction::deleteVersion()
	 */
	public function deleteVersion() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		// get ids
		$objectIDs = array();
		foreach ($this->objects as $object) {
			/* @var $object \ultimate\data\content\ContentEditor */
			$object->deleteVersion($this->parameters['versionID']);
		}
	}
	
	/**
	 * @see \wcf\data\IVersionableDatabaseObjectAction::validateDeleteVersion()
	 */
	public function validateDeleteVersion() {
		WCF::getSession()->checkPermissions(array('admin.content.ultimate.canDeleteContentVersion'));
	}
	
	/**
	 * Validates prior to calling beginEdit.
	 */
	public function validateBeginEdit() {
		$this->parameters['objectID'] = (isset($this->parameters['objectID'])) ? intval($this->parameters['objectID']) : 0;
		if (!$this->parameters['objectID']) {
			throw new UserInputException('objectID');
		}
		else {
			$this->content = new Content($this->parameters['objectID']);
			if (!$this->content->__get('contentID')) {
				throw new UserInputException('objectID');
			}
			
			WCF::getSession()->checkPermissions($this->permissionsUpdate);
		}
	}
	
	/**
	 * Starts the edit process.
	 */
	public function beginEdit() {
		BBCodeHandler::getInstance()->setAllowedBBCodes(explode(',', WCF::getSession()->getPermission('user.message.allowedBBCodes')));
		
		WCF::getTPL()->assign(array(
			'defaultSmilies' => SmileyCache::getInstance()->getCategorySmilies(),
			'permissionCanUseSmilies' => 'user.message.canUseSmilies',
			'content' => $this->content,
			'wysiwygSelector' => 'messageEditor'.$this->content->__get('contentID')
		));
		
		return array(
			'actionName' => 'beginEdit',
			'template' => WCF::getTPL()->fetch('contentInlineEditor', 'ultimate')
		);
	}
	
	/**
	 * Validates prior to calling save.
	 */
	public function validateSave() {
		if (!isset($this->parameters['data']) || !isset($this->parameters['data']['message']) || empty($this->parameters['data']['message'])) {
			throw new UserInputException('message');
		}
		
		$this->validateBeginEdit();
		$this->validateMessage($this->parameters['data']['message']);
	}
	
	/**
	 * Validates the message.
	 * 
	 * @param	string	$message
	 */
	public function validateMessage($message) {
		// search for disallowed bbcodes
		$disallowedBBCodes = BBCodeParser::getInstance()->validateBBCodes($message, explode(',', WCF::getSession()->getPermission('user.message.allowedBBCodes')));
		if (!empty($disallowedBBCodes)) {
			throw new UserInputException('text', WCF::getLanguage()->getDynamicVariable('wcf.message.error.disallowedBBCodes', array('disallowedBBCodes' => $disallowedBBCodes)));
		}
	}
	
	/**
	 * Saves the changes.
	 */
	public function save() {
		$contentData = array(
			'contentText' => $this->parameters['data']['message']
		);
		$isI18n = intval($this->parameters['data']['isI18n']);
				
		// pre-parse message text
		$contentData['contentText'] = PreParser::getInstance()->parse($contentData['contentText'], explode(',', WCF::getSession()->getPermission('user.message.allowedBBCodes')));
		$content = $this->content;
		if ($isI18n) {
			I18nHandler::getInstance()->register('text');
			I18nHandler::getInstance()->setValues('text', array(
				WCF::getUser()->getLanguage()->getObjectID() => $contentData['contentText']
			));
			I18nHandler::getInstance()->save('text', 'ultimate.content.'.$this->content->__get('contentID').'.contentText', 'ultimate.content', PACKAGE_ID);
		} else {
			// execute update action
			$action = new ContentAction(array($this->content), 'update', array('data' => $contentData));
			$action->executeAction();
			
			// load new post
			$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
			$content = $contents[$this->content->__get('contentID')];
		}
		
		return array(
			'actionName' => 'save',
			'message' => $content->getFormattedMessage()
		);
	}
}
