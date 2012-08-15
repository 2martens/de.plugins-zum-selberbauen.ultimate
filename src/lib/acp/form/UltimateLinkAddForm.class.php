<?php
namespace ultimate\acp\form;
use ultimate\data\link\LinkAction;
use ultimate\data\link\LinkEditor;
use ultimate\util\LinkUtil;
use wcf\acp\form\ACPForm;
use wcf\system\category\CategoryHandler;
use wcf\system\language\I18nHandler;
use wcf\system\Regex;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

/**
 * Shows the UltimateLinkAdd form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateLinkAddForm extends ACPForm {
	/**
	 * @var	string[]
	 * @see	\wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.link.add';
	
	/**
	 * @see	\wcf\page\AbstractPage::$templateName
	 */
	public $templateName = 'ultimateLinkAdd';
	
	/**
	 * @var	string[]
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canAddLink'
	);
	
	/**
	 * Contains the link name.
	 * @var string
	 */
	public $linkName = '';
	
	/**
	 * Contains the link URL.
	 * @var string
	 */
	public $linkURL = '';
	
	/**
	 * Contains the link description.
	 * @var string
	 */
	public $linkDescription = '';
	
	/**
	 * Contains the chosen categories.
	 * @var	integer[]
	 */
	public $categoryIDs = array();
	
	/**
	 * Contains all categories.
	 * @var	\wcf\data\category\Category[]
	*/
	public $categories = array();
	
	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		I18nHandler::getInstance()->register('linkName');
		I18nHandler::getInstance()->register('linkDescription');
	}
	
	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		$this->categories = CategoryHandler::getInstance()->getCategories('de.plugins-zum-selberbauen.ultimate.linkCategory');
		unset($this->categories[1]);
		parent::readData();
	}
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		I18nHandler::getInstance()->enableAssignValueVariables();
		if (I18nHandler::getInstance()->isPlainValue('linkName')) $this->linkName = StringUtil::trim($_POST['linkName']);
		if (I18nHandler::getInstance()->isPlainValue('linkDescription')) $this->linkDescription = StringUtil::trim($_POST['linkDescription']);
		if (isset($_POST['linkURL'])) $this->linkURL = StringUtil::trim($_POST['linkURL']);
		if (isset($_POST['categoryIDs']) && is_array($_POST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray(($_POST['categoryIDs']));
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		$this->validateName();
		$this->validateURL();
		$this->validateDescription();
		$this->validateCategories();
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		$parameters = array(
			'data' => array(
				'linkName' => $this->linkName,
				'linkURL' => $this->linkURL,
				'linkDescription' => $this->linkDescription
			),
			'categories' => $this->categoryIDs
		);
		
		$this->objectAction = new LinkAction(array(), 'create', $parameters);
		$this->objectAction->executeAction();
		
		// get new created link
		$returnValues = $this->objectAction->getReturnValues();
		$linkID = $returnValues['returnValues']->__get('linkID');
		$updateEntries = array();
		if (!I18nHandler::getInstance()->isPlainValue('linkName')) {
			I18nHandler::getInstance()->save('linkName', 'ultimate.link.'.$linkID.'.linkName', 'ultimate.link', PACKAGE_ID);
			$updateEntries['linkName'] = 'ultimate.link.'.$linkID.'.linkName';
		}
		if (!I18nHandler::getInstance()->isPlainValue('linDescription')) {
			I18nHandler::getInstance()->save('linkName', 'ultimate.link.'.$linkID.'.linkDescription', 'ultimate.link', PACKAGE_ID);
			$updateEntries['linkDescription'] = 'ultimate.link.'.$linkID.'.linkDescription';
		}
		if (!empty($updateEntries)) {
			$linkEditor = new LinkEditor($returnValues['returnValues']);
			$linkEditor->update($updateEntries);
		}
		$this->saved();
		
		WCF::getTPL()->assign(
			'success', true
		);
		
		// show empty form
		$this->linkName = $this->linkURL = $this->linkDescription = '';
		I18nHandler::getInstance()->disableAssignValueVariables();
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'linkURL' => $this->linkURL,
			'categories' => $this->categories,
			'categoryIDs' => $this->categoryIDs,
			'action' => 'add'
		));
	}
	
	/**
	 * Validates link name.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateLinkName() {
		if (!I18nHandler::getInstance()->isPlainValue('linkName')) {
			if (!I18nHandler::getInstance()->validateValue('linkName')) {
				throw new UserInputException('linkName');
			}
		}
		else {
			if (empty($this->linkName)) {
				throw new UserInputException('linkName');
			}
		}
	}
	
	/**
	 * Validates link url.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateLinkURL() {
		 if (empty($this->linkURL)) {
		 	throw new UserInputException('linkURL');
		 }
		 
		 if (!LinkUtil::isValidURL($this->linkURL)) {
		 	// try http scheme
		 	$this->linkURL = 'http://'.$this->linkURL;
		 	
		 	// if it still doesn't match
		 	if (!LinkUtil::isValidURL($this->linkURL)) {
		 		throw new UserInputException('linkURL', 'notValid');
		 	}
		 }
		 
		 if (!LinkUtil::isAvailableURL($this->linkURL)) {
		 	throw new UserInputException('linkURL', 'notUnique');
		 }
	}
	
	/**
	 * Validates the link categories.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateCategories() {
		foreach ($this->categoryIDs as $categoryID) {
			if (!in_array($categoryID, array_keys($this->categories))) {
				throw new UserInputException('category', 'notValid');
			}
		}
		if (empty($this->categoryIDs)) {
			// if no categories chosen, put link into uncategorized category
			$this->categoryIDs[] = 1;
		}
	}
}
