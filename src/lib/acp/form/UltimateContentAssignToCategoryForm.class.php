<?php
/**
 * Contains the UltimateContentAssignToCategory form.
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
use ultimate\data\content\ContentAction;
use wcf\acp\form\ACPForm;
use wcf\system\cache\CacheHandler;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\SystemException;
use wcf\util\ArrayUtil;

/**
 * Shows the UltimateContentAssignToCategory form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateContentAssignToCategoryForm extends ACPForm {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$templateName
	 */
	public $templateName = 'ultimateContentAssignToCategory';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canEditContent'
	);
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.acp.form.ACPForm.html#$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.content';
	
	/**
	 * Contains content ids.
	 * @var integer[]
	 */
	public $contentIDs = array();
	
	/**
	 * Contains Content objects.
	 * @var \ultimate\data\content\CategorizedContent[]
	 */
	public $contents = array();
	
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
		$this->typeID = ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.content');
		if ($this->typeID === null) {
			throw new SystemException("Clipboard item type 'de.plugins-zum-selberbauen.content' is unknown.");
		}
		
		// get content ids
		$contents = ClipboardHandler::getInstance()->getMarkedItems($this->typeID);
		if (!isset($contents['de.plugins-zum-selberbauen.content']) || empty($contents['de.plugins-zum-selberbauen.content'])) throw new IllegalLinkException();
		
		// load contents
		$this->contentIDs = array_keys($contents['de.plugins-zum-selberbauen.content']);
		$this->contents = $contents['de.plugins-zum-selberbauen.content'];
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		$this->loadCache();
		
		// determine already used categories
		$invalidCategoryIDs = array();
		foreach ($this->contents as $contentID => $content) {
			$categories = $content->__get('categories');
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
		$this->validateContentIDs();
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
		$this->objectAction = new ContentAction($this->contentIDs, 'update', $parameters);
		$this->objectAction->executeAction();
		
		ClipboardHandler::getInstance()->removeItems($this->typeID);
		
		$this->saved();
		
		WCF::getTPL()->assign('message', 'wcf.acp.ultimate.content.assignToCategory.success');
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
			'contents' => $this->contents,
			'contentIDs' => $this->contentIDs
		));
	}
	
	/**
	 * Loads the cache.
	 */
	protected function loadCache() {
		$cacheName = 'category';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\CategoryCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->categories = CacheHandler::getInstance()->get($cacheName, 'categories');
	}
	
	/**
	 * Validates the content ids.
	 * 
	 * @throws	\wcf\system\exception\IllegalLinkException	if content ids are empty
	 */
	protected function validateContentIDs() {
		if (empty($this->contentIDs)) {
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
