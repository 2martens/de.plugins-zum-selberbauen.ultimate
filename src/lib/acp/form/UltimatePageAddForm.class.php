<?php
namespace ultimate\acp\form;
use ultimate\data\content\ContentList;
use ultimate\data\page\PageAction;
use ultimate\data\page\PageEditor;
use ultimate\system\UltimateCore;
use ultimate\util\PageUtil;
use wcf\acp\form\ACPForm;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;

/**
 * Shows the UltimatePageAdd form.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.form
 * @category Ultimate CMS
 */
class UltimatePageAddForm extends ACPForm {
    
    /**
     * @see \wcf\acp\form\ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.menu.link.ultimate.page.add';
    
    /**
     * @see \wcf\page\AbstractPage::$templateName
     */
    public $templateName = 'ultimatePageAdd';
    
    /**
     * @see \wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'admin.content.ultimate.canAddPage'
    );
    
    /**
     * Contains the content id.
     * @var int
     */
    public $contentID = 0;
    
    /**
     * Contains all available contents.
     * @var array<ultimate\data\content\Content>
     */
    public $contents = array();
    
    /**
     * Contains the title of the page.
     * @var string
     */
    public $pageTitle = '';
    
    /**
     * Contains the page slug.
     * @var string
     */
    public $pageSlug = '';
    
    
    /**
     * @see \wcf\page\IPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        I18nHandler::getInstance()->register('pageTitle');
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        $this->contents = PageUtil::getAvailableContents();
        parent::readData();
    }
    
    /**
     * @see \wcf\form\IForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        
        I18nHandler::getInstance()->readValues();
        if (I18nHandler::getInstance()->isPlainValue('pageTitle')) $this->pageTitle = trim(I18nHandler::getInstance()->getValue('pageTitle'));
        // @todo: implement parent page
        if (isset($_POST['content'])) $this->contentID = intval($_POST['content']);
        if (isset($_POST['pageSlug'])) $this->pageSlug = trim($_POST['pageSlug']);
    }
    
    /**
     * @see \wcf\form\IForm::validate()
     */
    public function validate() {
        parent::validate();
        $this->validateContentID();
        $this->validateTitle();
        $this->validateSlug();
    }
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        parent::save();
        
        $parameters = array(
            'data' => array(
                'authorID' => UltimateCore::getUser()->userID,
                'pageTitle' => $this->pageTitle,
                'pageSlug' => $this->pageSlug,
                'lastModified' => TIME_NOW
            ),
            'contentID' => $this->contentID
        );
        
        $this->objectAction = new PageAction(array(), 'create', $parameters);
        $this->objectAction->executeAction();
        
        if (!I18nHandler::getInstance()->isPlainValue('pageTitle')) {
            $returnValues = $this->objectAction->getReturnValues();
            $pageID = $returnValues['returnValues']->pageID;
            I18nHandler::getInstance()->save('pageTitle', 'ultimate.page.'.$pageID.'.pageTitle', 'ultimate.page', PACKAGE_ID);
        
            $pageEditor = new PageEditor($returnValues['returnValues']);
            $pageEditor->update(array(
                'pageTitle' => 'ultimate.page.'.$pageID.'.pageTitle'
            ));
        }
        
        $this->saved();
        
        UltimateCore::getTPL()->assign(
            'success', true
        );
        
        //showing empty form
        $this->contentID = 0;
        $this->pageTitle = $this->pageSlug = '';
        $this->contents = array();
    }
    
    
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        
        I18nHandler::getInstance()->assignVariables();
        UltimateCore::getTPL()->assign(array(
            'contentID' => $this->contentID,
            'contents' => $this->contents,
            'pageTitle' => $this->pageTitle,
            'pageSlug' => $this->pageSlug,
            'action' => 'add'
        ));
    }
    
    /**
     * Validates the contentID.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateContentID() {
        if (!$this->contentID) {
            throw new UserInputException('content', 'notSelected');
        }
        if (!array_key_exists($this->contentID, $this->contents)) {
            throw new UserInputException('content', 'notValid');
        }
    }
    
    /**
     * Validates the page title.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateTitle() {
        if (!I18nHandler::getInstance()->isPlainValue('pageTitle')) {
            if (!I18nHandler::getInstance()->validateValue('pageTitle')) {
                throw new UserInputException('pageTitle');
            }
        }
        else {
            if (empty($this->pageTitle)) {
                throw new UserInputException('pageTitle');
            }
            if (!PageUtil::isAvailableTitle($this->pageTitle)) {
                throw new UserInputException('pageTitle', 'notUnique');
            }
        }
    }
    
    /**
     * Validates page slug.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateSlug() {
        if (empty($this->pageSlug)) {
            throw new UserInputException('pageSlug');
        }
    
        if (!PageUtil::isAvailableSlug($this->pageSlug)) {
            throw new UserInputException('pageSlug', 'notUnique');
        }
    }
}
