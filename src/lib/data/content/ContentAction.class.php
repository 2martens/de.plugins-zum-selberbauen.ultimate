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
use ultimate\data\content\language\ContentLanguageEntryCache;
use ultimate\data\content\language\ContentLanguageEntryEditor;
use ultimate\data\layout\LayoutAction;
use ultimate\system\layout\LayoutHandler;
use wcf\data\smiley\SmileyCache;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IMessageInlineEditorAction;
use wcf\data\IVersionableDatabaseObjectAction;
use wcf\system\bbcode\BBCodeHandler;
use wcf\system\bbcode\BBCodeParser;
use wcf\system\bbcode\PreParser;
use wcf\system\exception\UserInputException;
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
		'user.ultimate.content.canEditContent',		
		'user.ultimate.content.canAddContentVersion'
	);
	
	/**
	 * Array of permissions that are required for delete action.
	 * @var	string[]
	 */
	protected $permissionsDelete = array(
		'user.ultimate.content.canDeleteContent', 
		'user.ultimate.content.canDeleteContentVersion'
	);
	
	/**
	 * Array of permissions that are required for update action.
	 * @var	string[]
	 */
	protected $permissionsUpdate = array('user.ultimate.content.canEditContent');
	
	/**
	 * disallow requests for specified methods if the origin is not the ACP
	 * @var	string[]
	 */
	protected $requireACP = array();
	
	/**
	 * Resets cache if any of the listed actions is invoked
	 * @var	string[]
	 */
	protected $resetCache = array('create', 'createVersion', 'delete', 'deleteVersion', 'toggle', 'update', 'updatePosition');
	
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
		
		$languageEntryParameters = array(
			'contentTitle',
			'contentDescription',
			'contentText'
		);
		
		$versionData = array();
		$languageData = array();
		$tmpData = $this->parameters['data'];
		foreach ($tmpData as $key => $value) {
			if (in_array($key, $contentParameters)) continue;
			if (in_array($key, $languageEntryParameters)) {
				$languageData[$key] = $value;
			}
			else {
				$versionData[$key] = $value;
			}
			unset($this->parameters['data'][$key]);
		}
		
		// authorID is part of versionData as well
		$versionData['authorID'] = $this->parameters['data']['authorID'];
		
		$content = parent::create();
		$contentEditor = new ContentEditor($content);
		// create version
		$versionData['contentID'] = $content->__get('contentID');
		$version = $contentEditor->createVersion($versionData);
		// create language entries
		$preparedLanguageData = array();
		foreach ($languageData as $key => $value) {
			foreach ($value as $languageID => $__value) {
				if (!isset($preparedLanguageData[$languageID])) {
					$preparedLanguageData[$languageID] = array();
				}
				$preparedLanguageData[$languageID][$key] = $__value;
			}
		}
		ContentLanguageEntryEditor::createEntries($version->__get('versionID'), $preparedLanguageData);
		
		// update attachments
		if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
			$this->parameters['attachmentHandler']->updateObjectID($content->__get('contentID'));
		}
		
		// insert categories
		$categoryIDs = (isset($this->parameters['categories'])) ? $this->parameters['categories'] : array();
		$contentEditor->addToCategories($categoryIDs, false);
		
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
		
		$languageEntryParameters = array(
			'contentTitle',
			'contentDescription',
			'contentText'
		);
		
		$versionData = array();
		$languageData = array();
		$tmpData = $this->parameters['data'];
		foreach ($tmpData as $key => $value) {
			if (in_array($key, $contentParameters)) continue;
			if (in_array($key, $languageEntryParameters)) {
				$languageData[$key] = $value;
			}
			else {
				$versionData[$key] = $value;
			}
			unset($this->parameters['data'][$key]);
		}

		// authorID is part of versionData as well
		$versionData['authorID'] = $this->parameters['data']['authorID'];
		
		// prepare language data
		$preparedLanguageData = array();
		foreach ($languageData as $key => $value) {
			foreach ($value as $languageID => $__value) {
				if (!isset($preparedLanguageData[$languageID])) {
					$preparedLanguageData[$languageID] = array();
				}
				$preparedLanguageData[$languageID][$key] = $__value;
			}
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
				
				// create language entries
				ContentLanguageEntryEditor::createEntries($object->getObjectID(), $preparedLanguageData);
			}
		}
		
		$categoryIDs = (isset($this->parameters['categories'])) ? $this->parameters['categories'] : array();
		$removeCategories = (isset($this->parameters['removeCategories'])) ? $this->parameters['removeCategories'] : array();
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
			$textValues = ContentLanguageEntryCache::getInstance()->getValues($contentEditor->__get('versionID'), 'contentText');
			$titleValues = ContentLanguageEntryCache::getInstance()->getValues($contentEditor->__get('versionID'), 'contentTitle');
			foreach ($languages as $languageID => $language) {
				$text = $textValues[$languageID];
				$title = $titleValues[$languageID];
				$isI18n = (!ContentLanguageEntryCache::getInstance()->isNeutralValue($contentEditor->__get('versionID'), 'contentText') 
					|| !ContentLanguageEntryCache::getInstance()->isNeutralValue($contentEditor->__get('versionID'), 'contentTitle'));
				SearchIndexManager::getInstance()->add(
					'de.plugins-zum-selberbauen.ultimate.content',
					$contentEditor->__get('contentID'),
					$text,
					$title,
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
			
			$languageEntryParameters = array(
				'contentTitle',
				'contentDescription',
				'contentText'
			);
			
			$versionData = array();
			$languageData = array();
			$tmpData = $this->parameters['data'];
			foreach ($tmpData as $key => $value) {
				if (in_array($key, $contentParameters)) continue;
				if (in_array($key, $languageEntryParameters)) {
					$languageData[$key] = $value;
				}
				else {
					$versionData[$key] = $value;
				}
				unset($this->parameters['data'][$key]);
			}

			// authorID is part of versionData as well
			$versionData['authorID'] = $this->parameters['data']['authorID'];
			
			// prepare language data
			$preparedLanguageData = array();
			foreach ($languageData as $key => $value) {
				foreach ($value as $languageID => $__value) {
					if (!isset($preparedLanguageData[$languageID])) {
						$preparedLanguageData[$languageID] = array();
					}
					$preparedLanguageData[$languageID][$key] = $__value;
				}
			}
			
			foreach ($this->objects as $object) {
				$object->update($this->parameters['data']);
				$versionID = $object->getCurrentVersion()->__get('versionID');
				$object->updateVersion($versionID, $versionData);
				ContentLanguageEntryEditor::updateEntries($versionID, $preparedLanguageData);
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

		// execute action
		$affectedRows = call_user_func(array($this->className, 'deleteAll'), $objectIDs);
		
		// update search index
		SearchIndexManager::getInstance()->delete('de.plugins-zum-selberbauen.ultimate.content', $objectIDs);
		
		$layoutIDs = array();
		foreach ($this->objects as $object) {
			/* @var $layout \ultimate\data\layout\Layout */
			$layout = LayoutHandler::getInstance()->getLayoutFromObjectData($object->__get('contentID'), 'content');
			$layoutIDs[] = $layout->__get('layoutID');
		}
		$layoutAction = new LayoutAction($layoutIDs, 'delete', array());
		$layoutAction->executeAction();
		return $affectedRows;
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
	 * @throws \wcf\system\exception\UserInputException
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
			$contentData = array(
				WCF::getUser()->getLanguage()->getObjectID() => array(
					'contentText' => $contentData['contentText']
				)
			);
		} else {
			$contentData = array(
				ContentLanguageEntryCache::NEUTRAL_LANGUAGE => array(
					'contentText' => $contentData['contentText']
				)
			);
		}
		ContentLanguageEntryEditor::updateEntries($this->content->__get('versionID'), $contentData);
		
		return array(
			'actionName' => 'save',
			'message' => $content->getFormattedMessage()
		);
	}
}
