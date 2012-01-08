<?php
namespace ultimate\acp\form;
use ultimate\data\content\ContentAction;
use ultimate\system\UltimateCore;
use wcf\form\MessageForm;
use wcf\form\RecaptchaForm;
use wcf\system\exception\UserInputException;
use wcf\system\menu\acp\ACPMenu;

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
    public $activeMenuItem = 'wcf.acp.menu.item.link.ultimate.contents.add';
    
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
    }
    
    /**
     * @see \wcf\form\IForm::validate()
     */
    public function validate() {
        RecaptchaForm::validate();
        $this->validateSubject();
        $this->validateDescription();
        $this->validateText();
    }
    
    /**
     * Validates content subject.
     * @throws UserInputException
     */
    protected function validateSubject() {
        parent::validateSubject();
        if (strlen($this->subject) < 4) {
            throw new UserInputException('subject', 'tooShort');
        }
    }
    
    /**
     * Validates content description.
     * @throws UserInputException
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
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        parent::save();
        
        $parameters = array(
            'data' => array(
            	'contentTitle' => $this->subject,
                'contentDescription' => $this->description,
            	'contentText' => $this->text
            )
        );
        
        $action = new ContentAction(array(), 'create', $parameters);
        $action->execute();
        $this->saved();
        
        UltimateCore::getTPL()->assign('success', true);
        
        //showing empty form
        $this->subject = $this->description = $this->text = '';
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        UltimateCore::getTPL()->assign(array(
            'description' => $this->description,
            'action' => 'add'
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
