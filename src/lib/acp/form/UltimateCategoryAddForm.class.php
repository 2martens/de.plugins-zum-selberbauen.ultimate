<?php
namespace ultimate\acp\form;
use ultimate\data\category\CategoryList;
use ultimate\data\category\CategoryAction;
use ultimate\data\category\CategoryEditor;
use ultimate\util\CategoryUtil;
use wcf\acp\form\ACPForm;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the UltimateCategoryAdd form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateCategoryAddForm extends ACPForm {
	/**
	 * @var	string
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.acp.form.ACPForm.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.category.add';
	
	/**
	 * @var	string
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$templateName
	 */
	public $templateName = 'ultimateCategoryAdd';
	
	/**
	 * @var	string[]
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canAddCategory'
	);
	
	/**
	 * Contains all available categories.
	 * @var	ultimate\data\category\Category[]
	 */
	public $categories = array();
	
	/**
	 * Contains the title of the category.
	 * @var	string
	 */
	public $categoryTitle = '';
	
	/**
	 * Contains the category slug.
	 * @var	string
	 */
	public $categorySlug = '';
	
	/**
	 * Contains the parent id of this category.
	 * @var	integer	if 0 this category has no parent
	 */
	public $categoryParent = 0;
	
	/**
	 * Contains the description of this category.
	 * @var	string
	 */
	public $categoryDescription = '';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readParameters
	 */
	public function readParameters() {
		parent::readParameters();
		I18nHandler::getInstance()->register('categoryTitle');
		I18nHandler::getInstance()->register('categoryDescription');
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		$this->categories = CategoryUtil::getAvailableCategories();
		parent::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#readFormParameters
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		I18nHandler::getInstance()->enableAssignValueVariables();
		if (I18nHandler::getInstance()->isPlainValue('categoryTitle')) $this->categoryTitle = StringUtil::trim(I18nHandler::getInstance()->getValue('categoryTitle'));
		if (I18nHandler::getInstance()->isPlainValue('categoryDescription')) $this->categoryDescription = StringUtil::trim(I18nHandler::getInstance()->getValue('categoryDescription'));
		
		if (isset($_POST['categoryParent'])) $this->categoryParent = intval($_POST['categoryParent']);
		if (isset($_POST['categorySlug'])) $this->categorySlug = StringUtil::trim($_POST['categorySlug']);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#validate
	 */
	public function validate() {
		parent::validate();
		$this->validateTitle();
		$this->validateSlug();
		$this->validateParent();
		$this->validateDescription();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		parent::save();
		
		$parameters = array(
			'data' => array(
				'categoryTitle' => $this->categoryTitle,
				'categorySlug' => $this->categorySlug,
				'categoryParent' => $this->categoryParent,
				'categoryDescription' => $this->categoryDescription
			)
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
		$this->categoryTitle = $this->categorySlug = $this->categoryDescription = '';
		I18nHandler::getInstance()->disableAssignValueVariables();
		$this->categories = array();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'categoryParent' => $this->categoryParent,
			'categories' => $this->categories,
			'categoryTitle' => $this->categoryTitle,
			'categorySlug' => $this->categorySlug,
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
			if (!CategoryUtil::isAvailableTitle($this->categoryTitle, $this->categoryParent)) {
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
		if (!CategoryUtil::isAvailableSlug($this->categorySlug, $this->categoryParent)) {
			throw new UserInputException('categorySlug', 'notUnique');
		}
	}
	
	/**
	 * Validates the category description.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateDescription() {
		if (!I18nHandler::getInstance()->isPlainValue('categoryDescription')) {
			if (!I18nHandler::getInstance()->validateValue('categoryDescription')) {
				throw new UserInputException('categoryDescription');
			}
		}
		else {
			if (empty($this->categoryDescription)) {
				throw new UserInputException('categoryDescription');
			}
		}
	}
}
