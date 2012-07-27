<?php
namespace ultimate\acp\form;
use ultimate\data\content\ContentAction;
use ultimate\data\content\ContentEditor;
use ultimate\system\UltimateCore;
use wcf\form\MessageForm;
use wcf\form\RecaptchaForm;
use wcf\system\bbcode\URLParser;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\menu\acp\ACPMenu;
use wcf\util\ArrayUtil;
use wcf\util\MessageUtil;

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
     * @see \wcf\form\MessageForm::$enableMultilangualism
     */
    public $enableMultilangualism = true;
                
    /**
     * Contains the description of the content.
     * @var string
     */
    public $description = '';
    
    /**
     * Contains the chosen categories.
     * @var array
     */
    public $categoryIDs = array();
    
    /**
     * Contains all categories.
     * @var array<ultimate\data\category\Category>
     */
    public $categories = array();
       
    /**
     * Contains the maximal length of the text.
     * @var int | 0 means there's no limitation
     */
    public $maxTextLength = 0;
    
    /**
     * @see \wcf\form\IForm::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        
        I18nHandler::getInstance()->register('subject');
        I18nHandler::getInstance()->register('description');
        I18nHandler::getInstance()->register('text');
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
        parent::readData();
    }
    
    /**
     * @see \wcf\form\IForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        
        I18nHandler::getInstance()->readValues();
        if (I18nHandler::getInstance()->isPlainValue('subject')) $this->subject = trim(I18nHandler::getInstance()->getValue('subject'));
        if (I18nHandler::getInstance()->isPlainValue('description')) $this->description = trim(I18nHandler::getInstance()->getValue('description'));
        if (isset($_POST['categoryIDs']) && is_array($_POST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray(($_POST['categoryIDs']));
        if (I18nHandler::getInstance()->isPlainValue('text')) $this->text = MessageUtil::stripCrap(trim(I18nHandler::getInstance()->getValue('text')));
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
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        if (!I18nHandler::getInstance()->isPlainValue('text')) RecaptchaForm::save();
        else parent::save();
        $parameters = array(
            'data' => array(
                'authorID' => UltimateCore::getUser()->userID,
            	'contentTitle' => $this->subject,
                'contentDescription' => $this->description,
                'contentText' => $this->text,
                'enableBBCodes' => $this->enableBBCodes,
                'enableHtml' => $this->enableHtml,
                'enableSmilies' => $this->enableSmilies,
                'lastModified' => TIME_NOW
            ),
            'categories' => $this->categoryIDs
        );
        
        $this->objectAction = new ContentAction(array(), 'create', $parameters);
        $this->objectAction->executeAction();
        
        $returnValues = $this->objectAction->getReturnValues();
        $contentID = $returnValues['returnValues']->contentID;
        $updateEntries = array();
        if (!I18nHandler::getInstance()->isPlainValue('subject')) {
            I18nHandler::getInstance()->save('subject', 'ultimate.content.'.$contentID.'.contentTitle', 'ultimate.content', PACKAGE_ID);
            $updateEntries['contentTitle'] = 'ultimate.content.'.$contentID.'.contentTitle';
        }
        if (!I18nHandler::getInstance()->isPlainValue('description')) {
            I18nHandler::getInstance()->save('description', 'ultimate.content.'.$contentID.'.contentDescription', 'ultimate.content', PACKAGE_ID);
            $updateEntries['contentDescription'] = 'ultimate.content.'.$contentID.'.contentDescription';
        }
        if (!I18nHandler::getInstance()->isPlainValue('text')) {
            I18nHandler::getInstance()->save('text', 'ultimate.content.'.$contentID.'.contentText', 'ultimate.content', PACKAGE_ID);
            $updateEntries['contentText'] = 'ultimate.content.'.$contentID.'.contentText';
            
            // parse URLs
            if ($this->parseURL == 1) {
                $textValues = I18nHandler::getInstance()->getValues('text');
                foreach ($textValues as $languageID => $text) {
                    $textValues[$languageID] = URLParser::getInstance()->parse($text);
                }
                
                // nasty workaround, because you can't change the values of I18nHandler before save
                $sql = 'UPDATE wcf'.WCF_N.'_language_item
                        SET    languageItemValue = ?
                        WHERE  languageID        = ?
                        AND    languageItem      = ?
                        AND    packageID         = ?';
                $statement = UltimateCore::getDB()->prepareStatement($sql);
                UltimateCore::getDB()->beginTransaction();
                foreach ($textValues as $languageID => $text) {
                    $statement->execute(array(
                        $text,
                        $languageID,
                        'ultimate.content.'.$contentID.'.contentText',
                        PACKAGE_ID
                    ));
                }
                UltimateCore::getDB()->commitTransaction();
            }
        }
        if (count($updateEntries)) {
            $contentEditor = new ContentEditor($returnValues['returnValues']);
            $contentEditor->update($updateEntries);
        }
        $this->saved();
        
        UltimateCore::getTPL()->assign('success', true);
        
        //showing empty form
        $this->subject = $this->description = $this->text = '';
        I18nHandler::getInstance()->disableAssignValueVariables();
        $this->categoryIDs = array();
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        
        I18nHandler::getInstance()->assignVariables();
        UltimateCore::getTPL()->assign(array(
            'description' => $this->description,
            'action' => 'add',
            'categoryIDs' => $this->categoryIDs,
            'categories' => $this->categories,
            'languageID' => ($this->languageID ? $this->languageID : 0)
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
    
    /**
     * Validates content subject.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateSubject() {
        if (!I18nHandler::getInstance()->isPlainValue('subject')) {
            if (!I18nHandler::getInstance()->validateValue('subject')) {
                throw new UserInputException('subject');
            }
            $subjectValues = I18nHandler::getInstance()->getValues('subject');
            foreach ($subjectValues as $languageID => $subject) {
                if (strlen($subject) < 4) {
                    throw new UserInputException('subject', 'tooShort');
                }
            }
        } else {
            // checks if subject is empty; we don't have to do it twice
            parent::validateSubject();
    
            if (strlen($this->subject) < 4) {
                throw new UserInputException('subject', 'tooShort');
            }
        }
    }
    
    /**
     * Validates content description.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateDescription() {
        if (!I18nHandler::getInstance()->isPlainValue('description')) {
            if (!I18nHandler::getInstance()->validateValue('description')) {
                throw new UserInputException('description');
            }
            $descriptionValues = I18nHandler::getInstance()->getValues('description');
            foreach ($descriptionValues as $languageID => $description) {
                if (strlen($description) < 4) {
                    throw new UserInputException('description', 'tooShort');
                }
            }
        }
        else {
            if (empty($this->description)) {
                throw new UserInputException('description');
            }
    
            if (strlen($this->description) < 4) {
                throw new UserInputException('description', 'tooShort');
            }
        }
    }
    
    /**
     * Validates content text.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateText() {
        if (!I18nHandler::getInstance()->isPlainValue('text')) {
            if (!I18nHandler::getInstance()->validateValue('text')) {
                throw new UserInputException('text');
            }
            $textValues = I18nHandler::getInstance()->getValues('description');
            foreach ($textValues as $languageID => $text) {
                if ($this->maxTextLength != 0 && strlen($text) > $this->maxTextLength) {
                    throw new UserInputException('text', 'tooLong');
                }
            }
        }
        else {
            parent::validateText();
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
    
}
