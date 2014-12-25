<?php
/**
 * Contains the page data model action class.
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
 * @subpackage	data.page
 * @category	Ultimate CMS
 */
namespace ultimate\data\page;
use ultimate\data\page\language\PageLanguageEntryEditor;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\util\ArrayUtil;

/**
 * Executes page-related actions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.page
 * @category	Ultimate CMS
 */
class PageAction extends AbstractDatabaseObjectAction {
	/**
	 * The class name.
	 * @var	string
	 */
	public $className = 'ultimate\data\page\PageEditor';
	
	/**
	 * Array of permissions that are required for create action.
	 * @var	string[]
	 */
	protected $permissionsCreate = array('user.ultimate.editing.canAddPage');
	
	/**
	 * Array of permissions that are required for delete action.
	 * @var	string[]
	 */
	protected $permissionsDelete = array('user.ultimate.editing.canDeletePage');
	
	/**
	 * Array of permissions that are required for update action.
	 * @var	string[]
	 */
	protected $permissionsUpdate = array('user.ultimate.editing.canEditPage');
	
	/**
	 * Creates a page.
	 * 
	 * @return	Page
	 */
	public function create() {
		$languageEntryParameters = array(
			'pageTitle'
		);
		
		$languageData = array();
		$tmpData = $this->parameters['data'];
		foreach ($tmpData as $key => $value) {
			if (!in_array($key, $languageEntryParameters)) continue;
				
			$languageData[$key] = $value;
			unset($this->parameters['data'][$key]);
		}
		
		$preparedLanguageData = array();
		foreach ($languageData as $key => $value) {
			foreach ($value as $languageID => $__value) {
				if (!isset($preparedLanguageData[$languageID])) {
					$preparedLanguageData[$languageID] = array();
				}
				$preparedLanguageData[$languageID][$key] = $__value;
			}
		}
		
		$page = parent::create();
		PageLanguageEntryEditor::createEntries($page->__get('pageID'), $preparedLanguageData);
		$pageEditor = new PageEditor($page);
		
		// connect with content
		$contentID = (isset($this->parameters['contentID'])) ? intval($this->parameters['contentID']) : 0;
		$pageEditor->addContent($contentID, false);
		
		// connect with userGroups
		$groupIDs = (isset($this->parameters['groupIDs'])) ? ArrayUtil::toIntegerArray($this->parameters['groupIDs']) : array();
		if (!empty($groupIDs)) {
			$pageEditor->addGroups($groupIDs, false);
		}
		
		// insert meta description/keywords
		$metaDescription = (isset($this->parameters['metaDescription'])) ? $this->parameters['metaDescription'] : '';
		$metaKeywords = (isset($this->parameters['metaKeywords'])) ? $this->parameters['metaKeywords'] : '';
		$pageEditor->addMetaData($metaDescription, $metaKeywords);
		
		return $page;
	}
	
	/**
	 * Updates one or more objects.
	 */
	public function update() {
		if (isset($this->parameters['data'])) {
			$languageEntryParameters = array(
				'pageTitle'
			);
				
			$languageData = array();
			$tmpData = $this->parameters['data'];
			foreach ($tmpData as $key => $value) {
				if (!in_array($key, $languageEntryParameters)) continue;
					
				$languageData[$key] = $value;
				unset($this->parameters['data'][$key]);
			}
				
			$preparedLanguageData = array();
			foreach ($languageData as $key => $value) {
				foreach ($value as $languageID => $__value) {
					if (!isset($preparedLanguageData[$languageID])) {
						$preparedLanguageData[$languageID] = array();
					}
					$preparedLanguageData[$languageID][$key] = $__value;
				}
			}
			
			parent::update();
			
			foreach ($this->objects as $object) {
				PageLanguageEntryEditor::updateEntries($object->__get('pageID'), $preparedLanguageData);
			}
		}
		else {
			if (empty($this->objects)) {
				$this->readObjects();
			}
		}
		
		$contentID = (isset($this->parameters['contentID'])) ? intval($this->parameters['contentID']) : 0;
		$groupIDs = (isset($this->parameters['groupIDs'])) ? ArrayUtil::toIntegerArray($this->parameters['groupIDs']) : array();
		$metaDescription = (isset($this->parameters['metaDescription'])) ? $this->parameters['metaDescription'] : '';
		$metaKeywords = (isset($this->parameters['metaKeywords'])) ? $this->parameters['metaKeywords'] : '';
		
		foreach ($this->objects as $pageEditor) {
			/* @var $pageEditor \ultimate\data\page\PageEditor */
			if ($contentID) {
				$pageEditor->addContent($contentID);
			}
			
			if (!empty($groupIDs)) {
				$pageEditor->addGroups($groupIDs);
			}
			
			$pageEditor->addMetaData($metaDescription, $metaKeywords);
		}
	}
}
