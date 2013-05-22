<?php
/**
 * Contains the UltimateCategoryEdit form.
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
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\acp\form\UltimateCategoryAddForm;
use ultimate\data\category\Category;
use ultimate\data\category\CategoryAction;
use ultimate\data\category\CategoryEditor;
use ultimate\util\CategoryUtil;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 * Shows the UltimateCategoryEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateCategoryEditForm extends UltimateCategoryAddForm {
	/**
	 * @var	string
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.category';
	
	/**
	 * @var	string[]
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canEditCategory'
	);
	
	/**
	 * Contains the category id.
	 * @var	integer
	*/
	public $categoryID = 0;
	
	/**
	 * Contains the Category object of this category.
	 * @var	\ultimate\data\category\Category
	 */
	public $category = null;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readParameters
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
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		$this->categories = CategoryUtil::getAvailableCategories($this->categoryID);
		if (empty($_POST)) {
			$this->categoryTitle = $this->category->__get('categoryTitle');
			$this->categorySlug = $this->category->__get('categorySlug');
			$this->categoryDescription = $this->category->__get('categoryDescription');
			$metaData = $this->category->__get('metaData');
			$this->metaDescription = $metaData['metaDescription'];
			$this->metaKeywords = $metaData['metaKeywords'];
			I18nHandler::getInstance()->setOptions('categoryTitle', PACKAGE_ID, $this->categoryTitle, 'ultimate.category.\d+.categoryTitle');
			I18nHandler::getInstance()->setOptions('categoryDescription', PACKAGE_ID, $this->categoryDescription, 'ultimate.category.\d+.categoryDescription');
		}
		AbstractForm::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		AbstractForm::save();
		
		$this->categoryTitle = 'ultimate.category.'.$this->categoryID.'.categoryTitle';
		if (I18nHandler::getInstance()->isPlainValue('categoryTitle')) {
			I18nHandler::getInstance()->remove($this->categoryTitle, PACKAGE_ID);
			$this->categoryTitle = I18nHandler::getInstance()->getValue('categoryTitle');
		} else {
			I18nHandler::getInstance()->save('categoryTitle', $this->categoryTitle, 'ultimate.category', PACKAGE_ID);
		}
		
		$this->categoryDescription = 'ultimate.category.'.$this->categoryID.'.categoryDescription';
		if (I18nHandler::getInstance()->isPlainValue('categoryDescription')) {
			I18nHandler::getInstance()->remove($this->categoryDescription, PACKAGE_ID);
			$this->categoryDescription = I18nHandler::getInstance()->getValue('categoryDescription');
		} else {
			I18nHandler::getInstance()->save('categoryDescription', $this->categoryDescription, 'ultimate.category', PACKAGE_ID);
		}
		
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
		
		$this->objectAction = new CategoryAction(array($this->categoryID), 'update', $parameters);
		$this->objectAction->executeAction();
		
		$this->saved();
		
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$useRequestData = (!empty($_POST)) ? true : false;
		I18nHandler::getInstance()->assignVariables($useRequestData);
		
		WCF::getTPL()->assign(array(
			'categoryID' => $this->categoryID,
			'action' => 'edit'
		));
	}
}
