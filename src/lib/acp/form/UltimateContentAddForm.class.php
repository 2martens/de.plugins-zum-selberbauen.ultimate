<?php
namespace ultimate\acp\form;
use ultimate\data\content\ContentAction;
use ultimate\system\UltimateCore;
use wcf\form\MessageForm;
use wcf\form\RecaptchaForm;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\UserInputException;
use wcf\system\menu\acp\ACPMenu;
use wcf\util\ArrayUtil;

/**
 * Show the UltimateContentAdd form.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.form
 * @category Ultimate CMS
 */
class UltimateContentAddForm extends MessageForm {
    
    /**
     * @see \wcf\page\AbstractPage::$templateName
     */
    public $templateName = 'ultimateContentAdd';
    
    /**
     * @see \wcf\acp\form\ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.acp.menu.link.ultimate.content.add';
    
    /**
     * @see \wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'admin.content.ultimate.canAddContent'
    );
                
    /**
     * Contains the description of the content.
     * @var string
     */
    protected $description = '';
    
    /**
     * Contains the chosen categories.
     * @var array
     */
    protected $categoryIDs = array();
    
    /**
     * Contains all categories.
     * @var array<ultimate\data\category\Category>
     */
    protected $categories = array();
       
    /**
     * Contains the maximal length of the text.
     * @var int | 0 means there's no limitation
     */
    public $maxTextLength = 0;
    
    /**
     * @see \wcf\form\IForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_POST['description'])) $this->description = trim($_POST['description']);
        if (isset($_POST['categoryIDs']) && is_array($_POST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray(($_POST['categoryIDs']));
    }
    
    /**
     * @see \wcf\form\IForm::validate()
     */
    public function validate() {
        $this->validateSubject();
        $this->validateDescription();
        $this->validateCategories();
        $this->validateText();
        // multilingualism
        $this->validateContentLanguage();
        
        RecaptchaForm::validate();
    }
    
    /**
     * Validates content subject.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateSubject() {
        parent::validateSubject();
        if (strlen($this->subject) < 4) {
            throw new UserInputException('subject', 'tooShort');
        }
    }
    
    /**
     * Validates content description.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateDescription() {
        if (empty($this->description)) {
            throw new UserInputException('description');
        }
        
        if (strlen($this->description) < 4) {
            throw new UserInputException('description', 'tooShort');
        }
    }
    
    /**
     * Validates category.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateCategories() {
        $cacheOutput = CacheHandler::getInstance()->get('category');
        $categoryIDs = $cacheOutput['categoryIDs'];
        foreach ($this->categoryIDs as $categoryID) {
            if (in_array($categoryID, $categoryIDs)) continue;
            throw new UserInputException('category', 'invalidIDs');
            break;
        }
    }
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        parent::save();
        $parameters = array(
            'data' => array(
            	'contentTitle' => $this->subject,
                'contentDescription' => $this->description,
                'contentText' => $this->text,
                'enableBBCodes' => $this->enableBBCodes,
                'enableHtml' => $this->enableHtml,
                'enableSmilies' => $this->enableSmilies
            ),
            'categories' => $this->categoryIDs
        );
        
        $action = new ContentAction(array(), 'create', $parameters);
        $action->execute();
        
        $this->saved();
        
        UltimateCore::getTPL()->assign('success', true);
        
        //showing empty form
        $this->subject = $this->description = $this->text = '';
        $this->categoryID = 0;
    }
    
    /**
     * @see \wcf\form\IForm::readData()
     */
    public function readData() {
        $cache = 'category';
        $cacheBuilderClass = '\ultimate\system\cache\builder\UltimateCategoryCacheBuilder';
        $file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
        CacheHandler::getInstance()->addResource($cache, $file, $cacheBuilderClass);
        $cacheOutput = CacheHandler::getInstance()->get($cache);
        $this->categories = $cacheOutput['categories'];
        $this->categoryIDs = $cacheOutput['categoryIDs'];
        
        parent::readData();
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        UltimateCore::getTPL()->assign(array(
            'description' => $this->description,
            'action' => 'add',
            'categoryIDs' => $this->categoryIDs
        ));
    }
    
    /**
     * @see \wcf\page\IPage::show()
     */
    public function show() {
        if (!empty($this->activeMenuItem)) {
			ACPMenu::getInstance()->setActiveMenuItem($this->activeMenuItem);
		}
		parent::show();
    }
    
}
