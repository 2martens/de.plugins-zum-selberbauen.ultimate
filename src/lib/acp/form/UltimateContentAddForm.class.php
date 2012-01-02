<?php
namespace ultimate\acp\form;
use ultimate\data\content\ContentAction;
use wcf\acp\form\ACPForm;
use wcf\system\exception\UserInputException;

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
class UltimateContentAddForm extends ACPForm {
    
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
     * Contains the title of the content.
     * @var string
     */
    protected $title = '';
    
    /**
     * Contains the description of the content.
     * @var string
     */
    protected $description = '';
    
    /**
     * Contains the text of the content.
     * @var string
     */
    protected $text = '';
    
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
        if (isset($_POST['title'])) $this->title = trim($_POST['title']);
        if (isset($_POST['description'])) $this->title = trim($_POST['description']);
        if (isset($_POST['text'])) $this->text = trim($_POST['text']);
    }
    
    /**
     * @see \wcf\form\IForm::validate()
     */
    public function validate() {
        parent::validate();
        $this->validateTitle();
        $this->validateDescription();
        $this->validateText();
    }
    
    /**
     * Validates content title.
     * @throws UserInputException
     */
    protected function validateTitle() {
        if (empty($this->title)) {
            throw new UserInputException('title');
        }
        if (strlen($this->title) < 4) {
            throw new UserInputException('title', 'tooShort');
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
     * Validates content text.
     * @throws UserInputException
     */
    protected function validateText() {
        if (empty($this->text)) {
            throw new UserInputException('text');
        }
        
        //check text length
        if ($this->maxTextLength > 0 && strlen($this->text) > $this->maxTextLength) {
            throw new UserInputException('text', 'tooLong');
        }
    }
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        parent::save();
        
        $parameters = array(
            'data' => array(
            	'contentTitle' => $this->title,
                'contentDescription' => $this->description,
            	'contentText' => $this->text
            )
        );
        
        $action = new ContentAction(array(), 'create', $parameters);
        $action->execute();
        $this->saved();
        
        UltimateCore::getTPL()->assign('success', true);
        
        //showing empty form
        $this->title = $this->description = $this->text = '';
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        UltimateCore::getTPL()->assign(array(
            'title' => $this->title,
            'description' => $this->description,
            'text' => $this->text,
            'action' => 'add'
        ));
    }
    
}
