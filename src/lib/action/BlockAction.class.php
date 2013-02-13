<?php
/**
 * Contains the BlockAction class.
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
 * @subpackage	action
 * @category	Ultimate CMS
 */
namespace ultimate\action;
use ultimate\system\blocktype\BlockTypeHandler;
use ultimate\system\cache\builder\BlockCacheBuilder;
use ultimate\system\cache\builder\CategoryCacheBuilder;
use ultimate\system\cache\builder\ContentCacheBuilder;
use ultimate\system\cache\builder\PageCacheBuilder;
use wcf\action\AbstractSecureAction;
use wcf\action\AJAXProxyAction;
use wcf\system\event\EventHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\util\JSON;

/**
 * Handles block actions initiated in the VisualEditor.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	action
 * @category	Ultimate CMS
 */
class BlockAction extends AJAXProxyAction {
	/**
	 * Contains i18n options which were added by event listener.
	 * @var string[]	array('{optionName}' => '{blockTypeCSSIdentifier}')  
	 */
	public $i18nOptions = array();
	
	/**
	 * Contains the parameters which are sent to the object action.
	 * @var mixed[]
	 */
	public $parametersAction = array();
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.action.AJAXProxyAction.html#$className
	 */
	protected $className = '\ultimate\data\block\BlockAction';
	
	/**
	 * Contains all read block.
	 * @var \ultimate\data\block\Block
	 */
	protected $blocks = array();
	
	/**
	 * Contains all read categories.
	 * @var \ultimate\data\category\Category
	 */
	protected $categories = array();
	
	/**
	 * Contains all read contents.
	 * @var \ultimate\data\content\Content
	 */
	protected $contents = array();
	
	/**
	 * Contains all read pages.
	 * @var \ultimate\data\page\Page
	 */
	protected $pages = array();
	
	/**
	 * Contains the determined block type.
	 * @var \ultimate\system\blocktype\AbstractBlockType
	 */
	protected $blockType = null;
	
	/**
	 * Contains the determined request object.
	 * @var \ultimate\data\AbstractUltimateDatabaseObject
	 */
	protected $requestObject = null;
	
	/**
	 * Contains the determined request type.
	 * @var string
	 */
	protected $requestType = '';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.action.IAction.html#execute
	 */
	public function execute() {
		AbstractSecureAction::execute();
		
		$this->loadCache();
		
		// call method
		$this->determineBlockType();
		
		// fire event for i18n values
		EventHandler::getInstance()->fireAction($this, 'i18nInitialization');
		$this->{$this->actionName}();
		
		$this->executed();
		
		// send JSON-encoded response
		header('Content-type: application/json');
		echo JSON::encode($this->response);
		exit;
	}
	
	/**
	 * Determines the block type.
	 */
	protected function determineBlockType() {
		$layout = $this->parameters['layout'];
		$layoutID = 0;
		$requestType = '';
		$blockData = $this->parameters['blockOrigin'];
		
		// determine request type and object
		if (strpos($layout, '-')) {
			$parts = explode('-', $layout);
			$requestType = $parts[0];
			$layoutID = intval($parts[1]);
		}
		elseif (strpos($layout, '-') === false) {
			$requestType = $layout;
		}
		$requestObject = null;
		switch ($requestType) {
			case 'singleContent':
				$requestType = 'content';
				if ($layoutID) $requestObject = $this->contents[$layoutID];
				else $requestObject = array_shift($this->contents); // use one randomly selected content
				break;
			case 'singlePage':
				$requestType = 'page';
				if ($layoutID) $requestObject = $this->pages[$layoutID];
				else $requestObject = array_shift($this->pages); // use one randomly selected page
				break;
			case 'archiveCategory':
				$requestType = 'category';
				if ($layoutID) $requestObject = $this->categories[$layoutID];
				else $requestObject = array_shift($this->categories); // use one randomly selected category
				break;
		}
		
		// get block type
		$this->blockType = BlockTypeHandler::getInstance()->getBlockTypeByName($blockData['blockType']);
		$this->requestObject = $requestObject;
		$this->requestType = $requestType;
	}
	
	/**
	 * Loads the block content.
	 */
	protected function loadBlockContent() {
		$settings = $this->parameters['unsavedBlockSettings'];
		$options = $settings['settings'];
		$dimensionsPosition = array(
			'dimensions' => $settings['dimensions'],
			'position' => $settings['position']
		);
		$settings = array_merge_recursive($dimensionsPosition, $options);
		
		$blockID = $this->parameters['blockOrigin']['blockID'];
		$blockType = $this->parameters['blockOrigin']['blockType'];
		$templateID = $this->parameters['templateID'];
		
		// save multilingual values
		foreach ($options as $optionName => &$optionValue) {
			if (array_key_exists($optionName, $this->i18nOptions)) {
				if (!I18nHandler::getInstance()->isPlainValue($optionName)) {
					I18nHandler::getInstance()->save($optionName, 'ultimate.block.'.$blockType.'.'.$blockID.'.'.$optionName, 'ultimate.block', PACKAGE_ID);
					$optionValue = 'ultimate.block.'.$blockType.'.'.$blockID.'.'.$optionName;
				}
				else {
					I18nHandler::getInstance()->remove('ultimate.block.'.$blockType.'.'.$blockID.'.'.$optionName, PACKAGE_ID);
					$optionValue = I18nHandler::getInstance()->getValue($optionName);
				}
			}
		}
		
		// if block id is 0, create new block with given settings
		if (!$blockID) {
			$this->parametersAction = array(
				'data' => array(
					'blockTypeID' => BlockTypeHandler::getInstance()->getBlockTypeIDByName($blockType),
					'additionalData' => serialize($settings)
				),
				'templateID' => $templateID
			);
			
			// fire block create event
			EventHandler::getInstance()->fireAction($this, 'createBlockLoadBlockContent');
			
			// create object action instance
			$this->objectAction = new $this->className(array(), 'create', $this->parametersAction);
			$returnValues = array();
			
			// validate action
			try {
				$this->objectAction->validateAction();
			}
			catch (UserInputException $e) {
				$this->throwException($e);
			}
			catch (ValidateActionException $e) {
				$this->throwException($e);
			}
			
			try {
				$returnValues = $this->objectAction->executeAction();
				$blockID = $returnValues['returnValues']->__get('blockID');
			}
			catch (\Exception $e) {
				$this->throwException($e);
			}
		// otherwise save settings
		} else {
			$block = $this->blocks[$blockID];
			$settings = array_merge_recursive($block->__get('additionalData'), $settings);
			$this->parametersAction = array(
				'data' => array(
					'blockTypeID' => BlockTypeHandler::getInstance()->getBlockTypeIDByName($blockType),
					'additionalData' => serialize($settings)
				),
				'templateID' => $templateID
			);
			
			// fire block create event
			EventHandler::getInstance()->fireAction($this, 'updateBlockloadBlockContent');
			
			// create object action instance
			$this->objectAction = new $this->className(array($blockID), 'update', $this->parametersAction);
			$returnValues = array();
				
			// validate action
			try {
				$this->objectAction->validateAction();
			}
			catch (UserInputException $e) {
				$this->throwException($e);
			}
			catch (ValidateActionException $e) {
				$this->throwException($e);
			}
				
			try {
				$this->objectAction->executeAction();
			}
			catch (\Exception $e) {
				$this->throwException($e);
			}
		}
		
		// actually run the block type
		$this->blockType->init($this->requestType, $this->requestObject, $blockID, true);
		$this->response = $this->blockType->getHTML();
	}
	
	/**
	 * Loads the block options.
	 */
	protected function loadBlockOptions() {
		$options = $this->parameters['unsavedBlockOptions'];
		if ($options === null) $options = array();
		if (!empty($this->i18nOptions)) I18nHandler::getInstance()->readValues();
		
		$blockID = $this->parameters['blockOrigin']['blockID'];
		$block = $this->blocks[$blockID];
		$blockType = $this->parameters['blockOrigin']['blockType'];
		$blockTypeID = BlockTypeHandler::getInstance()->getBlockTypeIDByName($blockType);
		
		// save multilingual values
		foreach ($options as $optionName => &$optionValue) {
			if (array_key_exists($optionName, $this->i18nOptions)) {
				if (!I18nHandler::getInstance()->isPlainValue($optionName)) {
					$blockType = $this->i18nOptions[$optionName];
					I18nHandler::getInstance()->save($optionName, 'ultimate.block.'.$blockType.'.'.$blockID.'.'.$optionName, 'ultimate.block', PACKAGE_ID);
					$optionValue = 'ultimate.block.'.$blockType.'.'.$blockID.'.'.$optionName;
				}
				else {
					$blockType = $this->i18nOptions[$optionName];
					I18nHandler::getInstance()->remove('ultimate.block.'.$blockType.'.'.$blockID.'.'.$optionName, PACKAGE_ID);
					$optionValue = I18nHandler::getInstance()->getValue($optionName);
				}
			}
		}
		$options = array_merge_recursive($block->__get('additionalData'), $options);
		
		$this->parametersAction = array(
			'data' => array(
				'additionalData' => serialize($options)
			)
		);
		
		// fire block create event
		EventHandler::getInstance()->fireAction($this, 'updateBlockLoadBlockOptions');
		
		// create object action instance
		$this->objectAction = new $this->className(array($blockID), 'update', $this->parametersAction);
		$returnValues = array();
			
		// validate action
		try {
			$this->objectAction->validateAction();
		}
		catch (UserInputException $e) {
			$this->throwException($e);
		}
		catch (ValidateActionException $e) {
			$this->throwException($e);
		}
			
		try {
			$returnValues = $this->objectAction->executeAction();
			$blockID = $returnValues['returnValues']->__get('blockID');
		}
		catch (\Exception $e) {
			$this->throwException($e);
		}
		
		// actually run the block type
		$this->blockType->run($this->requestType, $this->requestObject, $blockID);
		$this->response = $this->blockType->getOptionsHTML();
	}
	
	/**
	 * Saves the options.
	 */
	protected function saveOptions() {
		if (!empty($this->i18nOptions)) I18nHandler::getInstance()->readValues();
		$parsedOptions = array();
		parse_str($this->parameters['options'], $parsedOptions);
		$templateID = $this->parameters['templateID'];
		
		$optionsToBlockID = array();
		foreach ($parsedOptions as $blockID => $blockArray) {
			foreach ($blockArray as $key => $value) {
				if ($key == 'settings') {
					$optionsToBlockID[$blockID] = array();
					foreach ($value as $optionName => $optionValue) {
						if (array_key_exists($optionName, $this->i18nOptions)) {
							if (!I18nHandler::getInstance()->isPlainValue($optionName)) {
								$blockType = $this->i18nOptions[$optionName];
								I18nHandler::getInstance()->save($optionName, 'ultimate.block.'.$blockType.'.'.$blockID.'.'.$optionName, 'ultimate.block', PACKAGE_ID);
								$optionValue = 'ultimate.block.'.$blockType.'.'.$blockID.'.'.$optionName;
							}
							else {
								$blockType = $this->i18nOptions[$optionName];
								I18nHandler::getInstance()->remove('ultimate.block.'.$blockType.'.'.$blockID.'.'.$optionName, PACKAGE_ID);
								$optionValue = I18nHandler::getInstance()->getValue($optionName);
							}
						}
						$optionsToBlockID[$blockID][$optionName] = $optionValue;
					}
				}
				else {
					$optionsToBlockID[$blockID][$key] = $value;
				}
			}
		}
		// save options
		// if you know an easier way for mass processing with actions, feel free to say
		foreach ($optionsToBlockID as $blockID => $optionsArray) {
			$block = $this->blocks[$blockID];
			$options = array_merge_recursive($block->__get('additionalData'), $optionsArray);
			$parameters = array(
				'data' => array(
					'additionalData' => serialize($options)
				),
				'templateID' => $templateID
			);
			
			// update block
			$this->objectAction = new $this->className(array($blockID), 'update', $parameters);
			$returnValues = array();
				
			// validate action
			try {
				$this->objectAction->validateAction();
			}
			catch (UserInputException $e) {
				$this->throwException($e);
			}
			catch (ValidateActionException $e) {
				$this->throwException($e);
			}
				
			try {
				$this->objectAction->executeAction();
			}
			catch (\Exception $e) {
				$this->throwException($e);
			}
		}
	}
	
	/**
	 * Loads the cache.
	 */
	protected function loadCache() {
		// reading cache
		$this->blocks = BlockCacheBuilder::getInstance()->getData(array(), 'blocks');
		$this->categories = CategoryCacheBuilder::getInstance()->getData(array(), 'categories');
		$this->contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		$this->pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
	}
}
