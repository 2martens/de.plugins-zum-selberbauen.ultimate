<?php
namespace ultimate\acp\form;
use ultimate\data\content\ContentEditor;
use wcf\acp\form\ACPForm;
use wcf\system\exception\UserInputException;


/**
 * Show the ContentAdd form.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.form
 * @category Ultimate CMS
 */
class ContentAddForm extends ACPForm {
    
    /**
     * @see wcf\acp\form\ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.acp.menu.item.link.ultimate.contents.add';
    
    /**
     * @see wcf\page\AbstractPage::$neededPermissions
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
     * @see wcf\form\AbstractForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_POST['title'])) $this->title = trim($_POST['title']);
        if (isset($_POST['text'])) $this->text = trim($_POST['text']);
    }
    
    /**
     * @see wcf\form\AbstractForm::validate()
     */
    public function validate() {
        parent::validate();
        $this->validateTitle();
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
     * @see wcf\form\AbstractForm::save()
     */
    public function save() {
        parent::save();
        
        $parameters = array(
            'title' => $this->title,
            'text' => $this->text
        );
        //TODO: Should it be made to a public variable to allow el's manipulation?
        $content = ContentEditor::create($parameters);
        $this->saved();
    }
    
    /**
     * @see wcf\form\AbstractForm::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        UltimateCore::getTPL()->assign(array(
            'title' => $this->title,
            'text' => $this->text
        ));
    }
    
}
