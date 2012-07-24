<?php
namespace ultimate\acp\form;
use ultimate\acp\form\UltimateCategoryAddForm;
use ultimate\data\category\Category;
use ultimate\data\category\CategoryAction;
use ultimate\data\category\CategoryEditor;
use ultimate\system\UltimateCore;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;

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
     * @see \wcf\acp\form\ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.acp.menu.link.ultimate.category';
    
    /**
     * @see \wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'admin.content.ultimate.canEditCategory'
    );
    
    /**
     * Contains the category id.
     * @var int
    */
    public $categoryID = 0;
    
    /**
     * Contains the CategoryEditor object of this category.
     * @var \ultimate\data\category\CategoryEditor
     */
    public $category = null;
    
    /**
     * @see \wcf\page\IPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_REQUEST['id'])) $this->categoryID = intval($_REQUEST['id']);
        $category = new Category($this->categoryID);
        if (!$category->categoryID) {
            throw new IllegalLinkException();
        }
    
        $this->category = new CategoryEditor($category);
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        if (!count($_POST)) {
            $this->categoryTitle = $this->category->categoryTitle;
            $this->categorySlug = $this->category->categorySlug;
            $this->categoryDescription = $this->category->categoryDescription;
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
    
        UltimateCore::getTPL()->assign('success', true);
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
    
        $useRequestData = (count($_POST)) ? true : false;
        I18nHandler::getInstance()->assignVariables($useRequestData);
    
        UltimateCore::getTPL()->assign(array(
            'categoryID' => $this->categoryID,
            'action' => 'edit'
        ));
    }
}
