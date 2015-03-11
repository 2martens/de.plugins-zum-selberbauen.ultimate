<?php
/**
 * The UltimateCategoryEdit form.
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
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\data\category\language\CategoryLanguageEntryCache;
use ultimate\data\category\Category;
use ultimate\data\category\CategoryAction;
use ultimate\util\CategoryUtil;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 * Shows the UltimateCategoryEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateCategoryEditForm extends UltimateCategoryAddForm {
	/**
	 * The active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.category';
	
	/**
	 * The category id.
	 * @var	integer
	 */
	public $categoryID = 0;
	
	/**
	 * The Category object of this category.
	 * @var	\ultimate\data\category\Category
	 */
	public $category = null;
	
	/**
	 * Reads parameters.
	 * @see	UltimateCategoryAddForm::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		if (isset($_REQUEST['id'])) $this->categoryID = intval($_REQUEST['id']);
		$category = new Category($this->categoryID);
		if (!$category->__get('categoryID')) {
			throw new IllegalLinkException();
		}
		
		$this->category = $category;
	}
	
	/**
	 * Reads data.
	 * @see	UltimateCategoryAddForm::readData()
	 */
	public function readData() {
		$this->categories = CategoryUtil::getAvailableCategories($this->categoryID);
		$this->categoryTitle = $this->category->__get('categoryTitle');
		$this->categorySlug = $this->category->__get('categorySlug');
		$this->categoryDescription = $this->category->__get('categoryDescription');
		
		// prepare I18nHandler
		if (!CategoryLanguageEntryCache::getInstance()->isNeutralValue($this->category->__get('categoryID'), 'categoryTitle')) {
			$categoryTitle = CategoryLanguageEntryCache::getInstance()->getValues($this->category->__get('categoryID'), 'categoryTitle');
			I18nHandler::getInstance()->setValues('categoryTitle', $categoryTitle);
		}
		else {
			I18nHandler::getInstance()->setValue('categoryTitle', $this->categoryTitle);
		}
		
		if (!CategoryLanguageEntryCache::getInstance()->isNeutralValue($this->category->__get('categoryID'), 'categoryDescription')) {
			$categoryDescription = CategoryLanguageEntryCache::getInstance()->getValues($this->category->__get('categoryID'), 'categoryDescription');
			I18nHandler::getInstance()->setValues('categoryDescription', $categoryDescription);
		}
		else {
			I18nHandler::getInstance()->setValue('categoryDescription', $this->categoryDescription);
		}
		
		$metaData = $this->category->__get('metaData');
		if (!empty($metaData)) {
			$this->metaDescription = (isset($metaData['metaDescription']) ? $metaData['metaDescription'] : '');
			$this->metaKeywords = (isset($metaData['metaKeywords']) ? $metaData['metaKeywords'] : '');
		}
		$this->categoryParent = $this->category->__get('categoryParent');
		
		AbstractForm::readData();
	}
	
	/**
	 * Saves the form input.
	 * @see	UltimateCategoryAddForm::save()
	 */
	public function save() {
		AbstractForm::save();
		
		// retrieve I18n values
		$categoryTitle = array();
		// for the time being the existing entries will be removed, if an array entry with id 0 is provided
		if (I18nHandler::getInstance()->isPlainValue('categoryTitle')) {
			$categoryTitle[CategoryLanguageEntryCache::NEUTRAL_LANGUAGE] = $this->categoryTitle;
		}
		else {
			$categoryTitle = I18nHandler::getInstance()->getValues('categoryTitle');
		}
		$categoryDescription = array();
		if (I18nHandler::getInstance()->isPlainValue('categoryDescription')) {
			$categoryDescription[CategoryLanguageEntryCache::NEUTRAL_LANGUAGE] = $this->categoryDescription;
		}
		else {
			$categoryDescription = I18nHandler::getInstance()->getValues('categoryDescription');
		}
		
		$parameters = array(
			'data' => array(
				'categoryTitle' => $categoryTitle,
				'categorySlug' => $this->categorySlug,
				'categoryParent' => $this->categoryParent,
				'categoryDescription' => $categoryDescription
			),
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords
		);
		
		$this->objectAction = new CategoryAction(array($this->categoryID), 'update', $parameters);
		$this->objectAction->executeAction();
		
		$this->saved();
		
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * Assigns the template variables.
	 * @see	UltimateCategoryAddForm::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'categoryID' => $this->categoryID,
			'action' => 'edit'
		));
	}
}
