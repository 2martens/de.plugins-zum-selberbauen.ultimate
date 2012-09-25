<?php
/**
 * Contains the UltimateLinkAssignToCategory form.
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
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\data\link\LinkAction;
use wcf\acp\form\ACPForm;
use wcf\system\category\CategoryHandler;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Enter description here ...
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateLinkAssignToCategoryForm extends ACPForm {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$templateName
	 */
	public $templateName = 'ultimateLinkAssignToCategory';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.link.ultimate.canEditLink'
	);
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.acp.form.ACPForm.html#$activeMenuItem
	*/
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.link';
	
	/**
	 * Contains link ids.
	 * @var integer[]
	 */
	public $linkIDs = array();
	
	/**
	 * Contains Link objects.
	 * @var \ultimate\data\link\CategorizedLink[]
	*/
	public $links = array();
	
	/**
	 * Contains category ids.
	 * @var integer[]
	*/
	public $categoryIDs = array();
	
	/**
	 * Contains Category objects.
	 * @var \ultimate\data\category\Category[]
	*/
	public $categories = array();
	
	/**
	 * Contains the clipboard item type id.
	 * @var integer|null
	*/
	protected $typeID = null;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readParameters
	 */
	public function readParameters() {
		parent::readParameters();
		// get type id
		$this->typeID = ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.link');
		if ($this->typeID === null) {
			throw new SystemException("Clipboard item type 'de.plugins-zum-selberbauen.ultimate.link' is unknown.");
		}
		
		// get link ids
		$links = ClipboardHandler::getInstance()->getMarkedItems($this->typeID);
		if (!isset($links['de.plugins-zum-selberbauen.ultimate.link']) || empty($links['de.plugins-zum-selberbauen.ultimate.link'])) throw new IllegalLinkException();
		
		// load links
		$this->linkIDs = array_keys($links['de.plugins-zum-selberbauen.ultimate.link']);
		$this->links = $links['de.plugins-zum-selberbauen.ultimate.link'];
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		$this->loadCache();
		
		// determine already used categories
		$invalidCategoryIDs = array();
		foreach ($this->links as $linkID => $link) {
			$categories = $link->__get('categories');
			foreach ($categories as $categoryID => $category) {
				$invalidCategoryIDs[] = $categoryID;
			}
		}
		// delete those from the available categories
		$invalidCategoryIDs = array_unique($invalidCategoryIDs);
		foreach ($invalidCategoryIDs as $categoryID) {
			if (isset($this->categories[$categoryID])) {
				unset($this->categories[$categoryID]);
			}
		}
		parent::readData();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#readFormParameters
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_POST['categoryIDs']) && is_array($_POST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray($_POST['categoryIDs']);
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#validate
	 */
	public function validate() {
		parent::validate();
		$this->validateLinkIDs();
		$this->validateCategoryIDs();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		parent::save();
		
		$parameters = array(
			'categories' => $this->categoryIDs
		);
		$this->objectAction = new LinkAction($this->linkIDs, 'update', $parameters);
		$this->objectAction->executeAction();
		
		ClipboardHandler::getInstance()->unmark($this->linkIDs, $this->typeID);
		
		$this->saved();
		
		WCF::getTPL()->assign('message', 'wcf.clipboard.item.link.assignToCategory.success');
		WCF::getTPL()->display('success');
		exit;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'categories' => $this->categories,
			'categoryIDs' => $this->categoryIDs,
			'links' => $this->links,
			'linkIDs' => $this->linkIDs
		));
	}
	
	/**
	 * Loads the cache.
	 */
	protected function loadCache() {
		$this->categories = CategoryHandler::getInstance()->getCategories('de.plugins-zum-selberbauen.ultimate.linkCategory');
		
		// fix missing __toString method
		foreach ($this->categories as $categoryID => $category) {
			$this->categories[$categoryID] = $category->getTitle();
		}
	}
	
	/**
	 * Validates the link ids.
	 *
	 * @throws	\wcf\system\exception\IllegalLinkException	if link ids are empty
	 */
	protected function validateLinkIDs() {
		if (empty($this->linkIDs)) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * Validates the category ids.
	 *
	 * @throws UserInputException	if selected categories are invalid
	 */
	protected function validateCategoryIDs() {
		foreach ($this->categoryIDs as $categoryID) {
			if (!isset($this->categories[$categoryID])) {
				throw new UserInputException('categoryIDs', 'notValid');
			} else {
				$category = $this->categories[$categoryID];
				if (!$category->__get('categoryID')) {
					throw new UserInputException('categoryIDs');
				}
			}
		}
	}
}
