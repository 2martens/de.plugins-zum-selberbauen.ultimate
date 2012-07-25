<?php
namespace ultimate\acp\form;
use wcf\util\ArrayUtil;

use ultimate\data\category\CategoryList;
use ultimate\data\category\CategoryAction;
use ultimate\data\category\CategoryEditor;
use ultimate\system\UltimateCore;
use ultimate\util\CategoryUtil;
use wcf\acp\form\ACPForm;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;

/**
 * Shows the UltimateCategoryAdd form.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.form
 * @category Ultimate CMS
 */
class UltimateCategoryAddForm extends ACPForm {
    
    /**
     * @see \wcf\acp\form\ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.menu.link.ultimate.category.add';
    
    /**
     * @see \wcf\page\AbstractPage::$templateName
     */
    public $templateName = 'ultimateCategoryAdd';
    
    /**
     * @see \wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'admin.content.ultimate.canAddCategory'
    );
    
    /**
     * Contains the category id.
     * @var int
    */
    public $categoryID = 0;
    
    /**
     * Contains all available categories.
     * @var array<ultimate\data\category\Category>
     */
    public $categories = array();
    
    /**
     * Contains the title of the category.
     * @var string
     */
    public $categoryTitle = '';
    
    /**
     * Contains the category slug.
     * @var string
     */
    public $categorySlug = '';
    
    /**
     * Contains the parent id of this category.
     * @var int if 0 this category has no parent
     */
    public $categoryParent = 0;
    
    /**
     * Contains the description of this category.
     * @var string
     */
    public $categoryDescription = '';
    
    /**
     * @see \wcf\page\IPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        I18nHandler::getInstance()->register('categoryTitle');
        I18nHandler::getInstance()->register('categoryDescription');
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        $cache = 'category';
        $cacheBuilderClass = '\ultimate\system\cache\builder\UltimateCategoryCacheBuilder';
        $file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
        CacheHandler::getInstance()->addResource($cache, $file, $cacheBuilderClass);
        $cacheOutput = CacheHandler::getInstance()->get($cache);
        $this->categories = $cacheOutput['categories'];
        parent::readData();
    }
    
    /**
     * @see \wcf\form\IForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
    
        I18nHandler::getInstance()->readValues();
        if (I18nHandler::getInstance()->isPlainValue('categoryTitle')) $this->categoryTitle = trim(I18nHandler::getInstance()->getValue('categoryTitle'));
        if (I18nHandler::getInstance()->isPlainValue('categoryDescription')) $this->categoryDescription = trim(I18nHandler::getInstance()->getValue('categoryDescription'));
    
        if (isset($_POST['categoryParent'])) $this->categoryParent = intval($_POST['categoryParent']);
        if (isset($_POST['categorySlug'])) $this->categorySlug = trim($_POST['categorySlug']);
    }
    
    /**
     * @see \wcf\form\IForm::validate()
     */
    public function validate() {
        parent::validate();
        $this->validateTitle();
        $this->validateSlug();
        $this->validateParent();
        // $this->validateDescription();
    }
    
    /**
     * @see \wcf\form\IForm::save()
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
        if (count($updateValues)) {
            $categoryEditor = new CategoryEditor($returnValues['returnValues']);
            $categoryEditor->update($updateValues);
        }
        $this->saved();
    
        UltimateCore::getTPL()->assign(
            'success', true
        );
    
        //showing empty form
        $this->categoryID = 0;
        $this->categoryTitle = $this->categorySlug = $this->categoryDescription = '';
        $this->categories = array();
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
    
        I18nHandler::getInstance()->assignVariables();
        UltimateCore::getTPL()->assign(array(
            'categoryID' => $this->categoryID,
            'categories' => $this->categories,
            'categoryTitle' => $this->categoryTitle,
            'categorySlug' => $this->categorySlug,
            'action' => 'add'
        ));
    }
    
    /**
     * Validates the category title.
     *
     * @throws \wcf\system\exception\UserInputException
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
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateParent() {
        if ($this->categoryParent != 0 && !array_key_exists($this->categoryParent, $this->categories)) {
            throw new UserInputException('categoryParent', 'notValid');
        }
    }
    
    /**
     * Validates category slug.
     *
     * @throws \wcf\system\exception\UserInputException
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
     * @throws \wcf\system\exception\UserInputException
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
