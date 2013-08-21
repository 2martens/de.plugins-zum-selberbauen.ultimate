<?php
/**
 * The UltimateCategoryAdd form.
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
use ultimate\data\category\CategoryAction;
use ultimate\data\category\CategoryEditor;
use ultimate\data\category\CategoryList;
use ultimate\util\CategoryUtil;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the UltimateCategoryAdd form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateCategoryAddForm extends AbstractForm {
	/**
	 * The active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.category.add';
	
	/**
	 * The template name.
	 * @var	string
	 */
	public $templateName = 'ultimateCategoryAdd';
	
	/**
	 * Array of needed permissions.
	 * @var	string[]
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canManageCategories'
	);
	
	/**
	 * All available categories.
	 * @var	ultimate\data\category\Category[]
	 */
	public $categories = array();
	
	/**
	 * The title of the category.
	 * @var	string
	 */
	public $categoryTitle = '';
	
	/**
	 * The category slug.
	 * @var	string
	 */
	public $categorySlug = '';
	
	/**
	 * The parent id of this category.
	 * @var	integer	if 0 this category has no parent
	 */
	public $categoryParent = 0;
	
	/**
	 * The description of this category.
	 * @var	string
	 */
	public $categoryDescription = '';
	
	/**
	 * The meta description.
	 * @var string
	 */
	public $metaDescription = '';
	
	/**
	 * The meta keywords.
	 * @var string
	 */
	public $metaKeywords = '';
	
	/**
	 * Reads parameters.
	 */
	public function readParameters() {
		parent::readParameters();
		I18nHandler::getInstance()->register('categoryTitle');
		I18nHandler::getInstance()->register('categoryDescription');
	}
	
	/**
	 * Reads data.
	 */
	public function readData() {
		$this->categories = CategoryUtil::getAvailableCategories();
		parent::readData();
	}
	
	/**
	 * Reads form input.
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		if (I18nHandler::getInstance()->isPlainValue('categoryTitle')) $this->categoryTitle = StringUtil::trim(I18nHandler::getInstance()->getValue('categoryTitle'));
		if (I18nHandler::getInstance()->isPlainValue('categoryDescription')) $this->categoryDescription = StringUtil::trim(I18nHandler::getInstance()->getValue('categoryDescription'));
		if (isset($_POST['metaDescription'])) $this->metaDescription = StringUtil::trim($_POST['metaDescription']);
		if (isset($_POST['metaKeywords'])) $this->metaKeywords = StringUtil::trim($_POST['metaKeywords']);
		
		if (isset($_POST['categoryParent'])) $this->categoryParent = intval($_POST['categoryParent']);
		if (isset($_POST['categorySlug'])) $this->categorySlug = StringUtil::trim($_POST['categorySlug']);
	}
	
	/**
	 * Validates the form input.
	 */
	public function validate() {
		parent::validate();
		$this->validateTitle();
		$this->validateSlug();
		$this->validateMetaDescription();
		$this->validateMetaKeywords();
		$this->validateParent();
		$this->validateDescription();
	}
	
	/**
	 * Saves the form input.
	 */
	public function save() {
		parent::save();
		
		$parameters = array(
			'data' => array(
				'categoryTitle' => $this->categoryTitle,
				'categorySlug' => $this->categorySlug,
				'categoryParent' => $this->categoryParent,
				'categoryDescription' => $this->categoryDescription
			),
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords
		);
		
		$this->objectAction = new CategoryAction(array(), 'create', $parameters);
		$this->objectAction->executeAction();
		
		$returnValues = $this->objectAction->getReturnValues();
		$categoryID = $returnValues['returnValues']->categoryID;
		$updateValues = array();
		if (!I18nHandler::getInstance()->isPlainValue('categoryTitle')) {
			I18nHandler::getInstance()->save('categoryTitle', 'ultimate.category.'.$categoryID.'.categoryTitle', 'ultimate.category', PACKAGE_ID);
			$updateValues['categoryTitle'] = 'ultimate.category.'.$categoryID.'.categoryTitle';
		}
		if (!I18nHandler::getInstance()->isPlainValue('categoryDescription')) {
			$values = I18nHandler::getInstance()->getValues('categoryDescription');
			$update = true;
			foreach ($values as $value) {
				if (!empty($value)) continue;
				$update = false;
			}
			if ($update) {
				I18nHandler::getInstance()->save('categoryDescription', 'ultimate.category.'.$categoryID.'.categoryDescription', 'ultimate.category', PACKAGE_ID);
				$updateValues['categoryDescription'] = 'ultimate.category.'.$categoryID.'.categoryDescription';
			}
		}
		if (!empty($updateValues)) {
			$categoryEditor = new CategoryEditor($returnValues['returnValues']);
			$categoryEditor->update($updateValues);
		}
		$this->saved();
		
		WCF::getTPL()->assign(
			'success', true
		);
		
		// showing empty form
		$this->categoryParent = 0;
		$this->categoryTitle = $this->categorySlug = $this->categoryDescription= $this->metaDescription = $this->metaKeywords = '';
		I18nHandler::getInstance()->reset();
		$this->categories = array();
	}
	
	/**
	 * Assigns template variables.
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'categoryParent' => $this->categoryParent,
			'categories' => $this->categories,
			'categoryTitle' => $this->categoryTitle,
			'categorySlug' => $this->categorySlug,
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords,
			'action' => 'add'
		));
	}
	
	/**
	 * Validates the category title.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateTitle() {
		if (!I18nHandler::getInstance()->isPlainValue('categoryTitle')) {
			if (!I18nHandler::getInstance()->validateValue('categoryTitle')) {
				throw new UserInputException('categoryTitle');
			}
		}
		else {
			if (empty($this->categoryTitle)) {
				throw new UserInputException('categoryTitle');
			}
			if (!CategoryUtil::isAvailableTitle($this->categoryTitle, (isset($this->categoryID) ? $this->categoryID : 0), $this->categoryParent)) {
				throw new UserInputException('categoryTitle', 'notUnique');
			}
		}
	}
	
	/**
	 * Validates category parent.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateParent() {
		if ($this->categoryParent != 0 && !array_key_exists($this->categoryParent, $this->categories)) {
			throw new UserInputException('categoryParent', 'notValid');
		}
	}
	
	/**
	 * Validates category slug.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateSlug() {
		if (empty($this->categorySlug)) {
			throw new UserInputException('categorySlug');
		}
		if (!CategoryUtil::isAvailableSlug($this->categorySlug, (isset($this->categoryID) ? $this->categoryID : 0), $this->categoryParent)) {
			throw new UserInputException('categorySlug', 'notUnique');
		}
	}
	
	/**
	 * Validates the meta description.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateMetaDescription() {
		if (strlen($this->metaDescription) > 255) {
			throw new UserInputException('metaDescription', 'tooLong');
		}
	}
	
	/**
	 * Validates the meta keywords.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateMetaKeywords() {
		if (strlen($this->metaKeywords) > 255) {
			throw new UserInputException('metaKeywords', 'tooLong');
		}
	}
	
	/**
	 * Validates the category description.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateDescription() {
		if (!I18nHandler::getInstance()->isPlainValue('categoryDescription')) {
			if (!I18nHandler::getInstance()->validateValue('categoryDescription', false, true)) {
				throw new UserInputException('categoryDescription');
			}
		}
	}
}
