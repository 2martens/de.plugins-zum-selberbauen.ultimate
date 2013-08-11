<?php
/**
 * Contains the block data model action class.
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.block
 * @category	Ultimate CMS
 */
namespace ultimate\data\block;
use ultimate\system\blocktype\BlockTypeHandler;
use ultimate\system\cache\builder\BlockCacheBuilder;
use ultimate\system\cache\builder\BlockTypeCacheBuilder;
use ultimate\system\layout\LayoutHandler;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 * Executes block-related actions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.ultimate.block
 * @category	Ultimate CMS
 */
class BlockAction extends AbstractDatabaseObjectAction {
	/**
	 * The class name.
	 * @var string
	 */
	public $className = '\ultimate\data\block\BlockEditor';
	
	/**
	 * Array of permissions that are required for create action.
	 * string[]
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canManageBlocks');
	
	/**
	 * Array of permissions that are required for delete action.
	 * @var string[]
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canManageBlocks');
	
	/**
	 * Array of permissions that are required for update action.
	 * @var string[]
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canManageBlocks');
	
	/**
	 * Creates a block and respects additional AJAX requirements.
	 * 
	 * @return	(integer|string)[]
	 */
	public function createAJAX() {
		// serializes additionalData and query parameters
		$parameters = $this->parameters['data'];
		$templateID = $parameters['templateID'];
		$blockTypeID = $parameters['blockTypeID'];
		$blockTypes = BlockTypeCacheBuilder::getInstance()->getData(array(), 'blockTypes');
		$blockType = $blockTypes[$blockTypeID];
		$blockTypeName = $blockType->__get('blockTypeName');
		
		unset($parameters['templateID']);
		// handle i18n values, content block only right now, generalize later
		$metaAboveContent_i18n = array();
		$metaBelowContent_i18n = array();
		
		// only relevant for content block type
		// for later: make it variable so it is independent from specific fields and blocktypes
		if ($blockTypeName == 'ultimate.blocktype.content') {
			if (isset($parameters['additionalData']['metaAboveContent_i18n'])) {
				$metaAboveContent_i18n = $parameters['additionalData']['metaAboveContent_i18n'];
				unset($parameters['additionalData']['metaAboveContent_i18n']);
				$parameters['additionalData']['metaAboveContent'] = '';
			}
			if (isset($parameters['additionalData']['metaBelowContent_i18n'])) {
				$metaBelowContent_i18n = $parameters['additionalData']['metaBelowContent_i18n'];
				unset($parameters['additionalData']['metaBelowContent_i18n']);
				$parameters['additionalData']['metaBelowContent'] = '';
			}
			
			if ($parameters['additionalData']['sortField'] != ULTIMATE_SORT_CONTENT_SORTFIELD) {
				$sql = 'SELECT *
				        FROM   ultimate'.WCF_N.'_content
				        ORDER BY '.WCF::getDB()->escapeString($parameters['additionalData']['sortField'])
				        .' '.WCF::getDB()->escapeString($parameters['additionalData']['sortOrder']);
				$parameters['query'] = $sql;
				$parameters['parameters'] = array();
			}
		}
		if (isset($parameters['additionalData'])) {
			$parameters['additionalData'] = serialize($parameters['additionalData']);
		}
		if (isset($parameters['parameters'])) {
			$parameters['parameters'] = serialize($parameters['parameters']);
		}
		$this->parameters['data'] = $parameters;
		
		// set action to create, otherwise cache is not rebuild
		$this->action = 'create';
		
		// create the block
		$block = $this->create();
		
		// connect block with template
		$blockEditor = new BlockEditor($block);
		$blockEditor->addToTemplate($templateID);
		
		if ($blockTypeName == 'ultimate.blocktype.content') {
			// alter i18n values
			$metaAboveContent = 'ultimate.block.content.'.$block->__get('blockID').'.metaAboveContent';
			$metaBelowContent = 'ultimate.block.content.'.$block->__get('blockID').'.metaBelowContent';
			I18nHandler::getInstance()->register('metaAboveContent');
			I18nHandler::getInstance()->register('metaBelowContent');
			if (empty($metaAboveContent_i18n)) {
				I18nHandler::getInstance()->remove($metaAboveContent, PACKAGE_ID);
				$metaAboveContent = $block->__get('metaAboveContent');
			} else {
				I18nHandler::getInstance()->setValues('metaAboveContent', $metaAboveContent_i18n);
				I18nHandler::getInstance()->save('metaAboveContent', $metaAboveContent, 'ultimate.block', PACKAGE_ID);
			}
			
			if (empty($metaBelowContent_i18n)) {
				I18nHandler::getInstance()->remove($metaBelowContent, PACKAGE_ID);
				$metaBelowContent = $block->__get('metaBelowContent');
			} else {
				I18nHandler::getInstance()->setValues('metaBelowContent', $metaBelowContent_i18n);
				I18nHandler::getInstance()->save('metaBelowContent', $metaBelowContent, 'ultimate.block', PACKAGE_ID);
			}
			
			$additionalData = $block->__get('additionalData');
			$additionalData['metaAboveContent'] = $metaAboveContent;
			$additionalData['metaBelowContent'] = $metaBelowContent;
			
			$blockEditor->update(array(
				'additionalData' => serialize($additionalData)
			));
		}
		BlockEditor::resetCache();
		
		return array(
			'blockID' => $block->__get('blockID'),
			'blockTypeID' => $block->__get('blockTypeID'),
			'blockTypeName' => WCF::getLanguage()->get($blockTypeName)
		);
	}
	
	/**
	 * Edits a block and respects additional AJAX requirements.
	 *
	 * @return	(integer|string)[]
	 */
	public function editAJAX() {
		// serializes additionalData and query parameters
		$parameters = $this->parameters['data'];
		/* @var $blockEditor \ultimate\data\block\BlockEditor */
		$blockEditor = $this->objects[0];
		
		$blockTypeData = $blockEditor->__get('blockType');
		$blockTypeName = $blockTypeData->__get('blockTypeName');
	
		// handle i18n values, just content block right now, generalize later
		$metaAboveContent_i18n = array();
		$metaBelowContent_i18n = array();
		$metaAboveContent_plain = $metaBelowContent_plain = '';
		
		if (isset($parameters['additionalData']['metaAboveContent'])) {
			$metaAboveContent_plain = $parameters['additionalData']['metaAboveContent'];
		}
		if (isset($parameters['additionalData']['metaBelowContent'])) {
			$metaBelowContent_plain = $parameters['additionalData']['metaBelowContent'];
		}
		
		// only relevant for content block type
		// for later: make it variable so it is independent from specific fields and blocktypes
		if ($blockTypeName == 'ultimate.blocktype.content') {
			if (isset($parameters['additionalData']['metaAboveContent_i18n'])) {
				$metaAboveContent_i18n = $parameters['additionalData']['metaAboveContent_i18n'];
				unset($parameters['additionalData']['metaAboveContent_i18n']);
				$parameters['additionalData']['metaAboveContent'] = '';
			}
			if (isset($parameters['additionalData']['metaBelowContent_i18n'])) {
				$metaBelowContent_i18n = $parameters['additionalData']['metaBelowContent_i18n'];
				unset($parameters['additionalData']['metaBelowContent_i18n']);
				$parameters['additionalData']['metaBelowContent'] = '';
			}

			if ($parameters['additionalData']['sortField'] != ULTIMATE_SORT_CONTENT_SORTFIELD) {
				$sql = 'SELECT contentID
				        FROM   ultimate'.WCF_N.'_content
				        ORDER BY '.WCF::getDB()->escapeString($parameters['additionalData']['sortField'])
							        .' '.WCF::getDB()->escapeString($parameters['additionalData']['sortOrder']);
				$parameters['query'] = $sql;
				$parameters['parameters'] = array();
			}
		}
		if (isset($parameters['additionalData'])) {
			$parameters['additionalData'] = serialize($parameters['additionalData']);
		}
		if (isset($parameters['parameters'])) {
			$parameters['parameters'] = serialize($parameters['parameters']);
		}
		$this->parameters['data'] = $parameters;
		
		// set action to update, otherwise cache is not rebuild
		$this->action = 'update';
		$this->update();
	
		if ($blockTypeName == 'ultimate.blocktype.content') {
			// alter i18n values
			$metaAboveContent = 'ultimate.block.content.'.$blockEditor->__get('blockID').'.metaAboveContent';
			$metaBelowContent = 'ultimate.block.content.'.$blockEditor->__get('blockID').'.metaBelowContent';
			I18nHandler::getInstance()->register('metaAboveContent');
			I18nHandler::getInstance()->register('metaBelowContent');
			if (empty($metaAboveContent_i18n)) {
				I18nHandler::getInstance()->remove($metaAboveContent, PACKAGE_ID);
				$metaAboveContent = $metaAboveContent_plain;
			} else {
				I18nHandler::getInstance()->setValues('metaAboveContent', $metaAboveContent_i18n);
				I18nHandler::getInstance()->save('metaAboveContent', $metaAboveContent, 'ultimate.block', PACKAGE_ID);
			}
				
			if (empty($metaBelowContent_i18n)) {
				I18nHandler::getInstance()->remove($metaBelowContent, PACKAGE_ID);
				$metaBelowContent = $metaBelowContent_plain;
			} else {
				I18nHandler::getInstance()->setValues('metaBelowContent', $metaBelowContent_i18n);
				I18nHandler::getInstance()->save('metaBelowContent', $metaBelowContent, 'ultimate.block', PACKAGE_ID);
			}
				
			$blockEditor = new BlockEditor(new Block($this->objectIDs[0]));
			$additionalData = $blockEditor->__get('additionalData');
			$additionalData['metaAboveContent'] = $metaAboveContent;
			$additionalData['metaBelowContent'] = $metaBelowContent;
			
			$blockEditor->update(array(
				'additionalData' => serialize($additionalData)
			));
		}
		BlockEditor::resetCache();
	
		return array(
			'blockID' => $blockEditor->__get('blockID'),
			'blockTypeID' => $blockEditor->__get('blockTypeID'),
			'blockTypeName' => WCF::getLanguage()->get($blockTypeName)
		);
	}
	
	/**
	 * Validates the createAJAX method.
	 */
	public function validateCreateAJAX() {
		$this->validateCreate();
	}
	
	/**
	 * Validates the editAJAX method.
	 */
	public function validateEditAJAX() {
		$this->validateUpdate();
	}
	
	/**
	 * Returns blockType specific information.
	 * 
	 * @return	string
	 */
	public function getFormDataAJAX() {
		$parameters = $this->parameters['data'];
		$blockTypeID = intval($parameters['blockTypeID']);
		
		$blockType = BlockTypeHandler::getInstance()->getBlockType($blockTypeID);
		$optionsHTML = $blockType->getOptionsHTML();
		return $optionsHTML;
	}
	
	/**
	 * Returns blockType specific information in the edit case.
	 *
	 * @return	string
	 */
	public function getFormDataEditAJAX() {
		$parameters = $this->parameters['data'];
		$blockID = intval($parameters['blockID']);
		$blocks = BlockCacheBuilder::getInstance()->getData(array(), 'blocks');
		
		$block = $blocks[$blockID];
		$blockTypeID = $block->__get('blockTypeID');
		
		/* @var $blockType \ultimate\system\blocktype\IBlockType */
		$blockType = BlockTypeHandler::getInstance()->getBlockType($blockTypeID);
		$blockType->init('index', LayoutHandler::getInstance()->getLayout(LayoutHandler::INDEX), null, $blockID, null);
		$optionsHTML = $blockType->getOptionsHTML();
		return $optionsHTML;
	}
	
	/**
	 * Does nothing as the getFormDataAJAX doesn't require any permission.
	 */
	public function validateGetFormDataAJAX() {
		// no permissions required
	}
	
	/**
	 * Does nothing as the getFormDataEditAJAX doesn't require any permission.
	 */
	public function validateGetFormDataEditAJAX() {
		// no permissions required
	}
}
