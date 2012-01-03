<?php
namespace ultimate\form;
use ultimate\data\config\ConfigAction;
use ultimate\system\config\storage\ConfigStorage;
use ultimate\system\UltimateCore;
use ultimate\data\config\Config;
use wcf\form\AbstractSecureForm;
use wcf\system\exception\UserInputException;

/**
 * Shows the ConfigEditor form.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage form
 * @category Ultimate CMS
 */
class ConfigEditorForm extends AbstractSecureForm {
    
    /**
     * @see \wcf\page\AbstractPage::$templateName
     */
    public $templateName = 'configEditor';
    
    /**
     * @see \wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'admin.content.ultimate.canAddConfig',
        'admin.content.ultimate.canEditConfig'
    );
    
    /**
     * By default the action is add.
     * @see \wcf\page\AbstractPage::$action
     */
    public $action = 'add';
    
    /**
     * Contains the config id.
     * @var int
     */
    protected $configID = 0;
    
    /**
     * Contains a ConfigStorage object.
     * @var ConfigStorage
     */
    protected $configStorage = null;
    
    /**
     * Contains all entries.
     * @var array
     */
    protected $entries = array(
        'left' => array(),
        'center' => array(),
        'right' => array()
    );
    
    /**
     * Contains the meta description.
     * @var string
     */
    protected $metaDescription = '';
    
    /**
     * Contains the meta keywords.
     * @var string
     */
    protected $metaKeywords = '';
    
    /**
     * @see \wcf\page\IPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_REQUEST['id'])) $this->configID = intval($_REQUEST['id']);
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        if (!count($_POST)) {
            if ($this->action == 'add') {
                $this->configStorage = new ConfigStorage();
            }
            elseif ($this->action = 'edit') {
                $config = new Config($this->configID);
                $this->configTitle = $config->configTitle;
                $this->metaDescription = $config->metaDescription;
                $this->metaKeywords = $config->metaKeywords;
                $this->configStorage = unserialize($config->storage);
                $this->entries = $this->configStorage->getEntries();
            }
        }
        parent::readData();
    }
    
    /**
     * @see \wcf\form\IForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_POST['metaDescription'])) $this->metaDescription = trim($_POST['metaDescription']);
        if (isset($_POST['metaKeywords'])) $this->metaKeywords = trim($_POST['metaKeywords']);
        if (isset($_POST['configTitle'])) $this->configTitle = trim($_POST['configTitle']);
    }
    
    /**
     * @see \wcf\form\IForm::validate()
     */
    public function validate() {
        parent::validate();
        $this->validateTitle();
        $this->validateDescription();
        $this->validateKeywords();
    }
    
    /**
     * Validates the config title.
     *
     * @throws UserInputException
     */
    protected function validateTitle() {
        if (empty($this->configTitle)) {
            throw new UserInputException('configTitle');
        }
        if (!ConfigUtil::isAvailableTitle($this->configTitle)) {
            throw new UserInputException('configTitle', 'notUnique');
        }
    }
    
    /**
     * Validates the meta description.
     *
     * @throws UserInputException
     */
    protected function validateDescription() {
        if (strlen($this->metaDescription) > $this->maxLength) {
            throw new UserInputException('metaDescription', 'tooLong');
        }
    }
    
    /**
     * Validates the meta keywords.
     */
    protected function validateKeywords() {
        //does nothing
    }
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        parent::save();
        
        $parameters = array(
            'data' => array(
                'configTitle' => $this->configTitle,
                'metaDescription' => $this->metaDescription,
                'metaKeywords' => $this->metaKeywords,
                'storage' => serialize($this->configStorage)
            )
        );
        if ($this->action == 'add') {
            $action = new ConfigAction(array(), 'create', $parameters);
            $action->execute();
        } elseif ($this->action = 'edit') {
            $action = new ConfigAction(array($this->configID), 'update', $parameters);
            $action->execute();
        }
        $this->saved();
        
        UltimateCore::getTPL()->assign('success', true);
        
        //shows a blank form
        if ($this->action == 'add') {
            $this->configTitle = $this->metaDescription = $this->metaKeywords = '';
            $this->entries = array(
                'left' => array(),
                'center' => array(),
                'right' => array()
            );
        }
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        UltimateCore::getTPL()->assign(array(
            'entries' => $this->entries,
            'configTitle' => $this->configTitle,
            'metaDescription' => $this->metaDescription,
            'metaKeywords' => $this->metaKeywords
        ));
    }
}
