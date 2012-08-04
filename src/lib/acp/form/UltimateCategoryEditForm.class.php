<?php
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
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.form
 * @category Ultimate CMS
 */
class UltimateCategoryEditForm extends UltimateCategoryAddForm {
    /**
     * @var string
     * @see \wcf\acp\form\ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.acp.menu.link.ultimate.category';
    
    /**
     * @var string[]
     * @see \wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'admin.content.ultimate.canEditCategory'
    );
    
    /**
     * Contains the category id.
     * @var integer
    */
    public $categoryID = 0;
    
    /**
     * Contains the Category object of this category.
     * @var \ultimate\data\category\Category
     */
    public $category = null;
    
    /**
     * @see \wcf\page\IPage::readParameters()
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
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        if (!count($_POST)) {
            $this->categoryTitle = $this->category->__get('categoryTitle');
            $this->categorySlug = $this->category->__get('categorySlug');
            $this->categoryDescription = $this->category->__get('categoryDescription');
            $this->categories = CategoryUtil::getAvailableCategories($this->categoryID);
            I18nHandler::getInstance()->setOptions('categoryTitle', PACKAGE_ID, $this->categoryTitle, 'ultimate.category.\d+.categoryTitle');
            I18nHandler::getInstance()->setOptions('categoryDescription', PACKAGE_ID, $this->categoryDescription, 'ultimate.category.\d+.categoryDescription');
        }
        AbstractForm::readData();
    }
    
    /**
     * @see \wcf\form\IForm::save()
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
            $this->categoryTitle = I18nHandler::getInstance()->getValue('categoryDescription');
        } else {
            I18nHandler::getInstance()->save('categoryDescription', $this->categoryDescription, 'ultimate.category', PACKAGE_ID);
        }
    
        $parameters = array(
            'data' => array(
                'categoryTitle' => $this->categoryTitle,
                'categorySlug' => $this->categorySlug,
                'categoryParent' => $this->categoryParent,
                'categoryDescription' => $this->categoryDescription
            )
        );
    
        $this->objectAction = new CategoryAction(array($this->categoryID), 'update', $parameters);
        $this->objectAction->executeAction();
    
        $this->saved();
    
        WCF::getTPL()->assign('success', true);
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
    
        $useRequestData = (count($_POST)) ? true : false;
        I18nHandler::getInstance()->assignVariables($useRequestData);
    
        WCF::getTPL()->assign(array(
            'categoryID' => $this->categoryID,
            'action' => 'edit'
        ));
    }
}
