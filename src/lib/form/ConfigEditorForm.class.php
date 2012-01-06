<?php
namespace ultimate\form;
use wcf\system\io\File;

use ultimate\data\content\ContentList;
use ultimate\data\component\ComponentList;
use ultimate\data\config\Config;
use ultimate\data\config\ConfigAction;
use ultimate\system\config\ConfigEntry;
use ultimate\system\config\storage\ConfigStorage;
use ultimate\system\UltimateCore;
use ultimate\util\ConfigUtil;
use wcf\form\AbstractSecureForm;
use wcf\system\exception\UserInputException;
use wcf\util\JSON;
use wcf\util\StringUtil;

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
    
    // general values - start
    
    /**
     * By default the action is add.
     * @see \wcf\page\AbstractPage::$action
     */
    public $action = 'add';
    
    /**
     * If true, the form was sended via ajax.
     * @var boolean
     */
    protected $viaAjax = false;
    
    /**
     * Specifies from which form the submit came.
     * @var string
     */
    protected $form = 'main';
    
    // general values - end
    
    // values of addEntry form - start
    /**
     * Contains all available components.
     * @var array
     */
    protected $components = array();
    
    /**
     * Contains all available contents.
     * @var array
     */
    protected $contents = array();
    
    /**
     * Contains the component id of the added entry.
     * @var int
     */
    protected $componentID = 0;
    
    /**
     * Contains the content id of the added entry.
     * @var int
     */
    protected $contentID = 0;
    
    /**
     * Contains the column of the added entry.
     * @var string
     */
    protected $column = '';
    
    // values of addEntry - end
    
    // values of main form - start
    
    /**
     * Contains the maximal length for meta description and keywords.
     * @var int
     */
    protected $maxLength = 255;
    
    /**
     * Contains the config id.
     * @var int
     */
    protected $configID = 0;
    
    /**
     * Contains the config title.
     * @var string
     */
    protected $configTitle = '';
    
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
    
    // values of main form - end
    
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
        //reading components
        $componentList = new ComponentList();
        $componentList->readObjects();
        $this->components = $componentList->getObjects();
            
        //reading contents
        $contentList = new ContentList();
        $contentList->readObjects();
        $this->contents = $contentList->getObjects();
            
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
        //reading general parameters
        if (isset($_POST['formular'])) $this->form = trim($_POST['formular']);
        if (isset($_POST['ajax'])) $this->viaAjax = (boolean) intval($_POST['ajax']);
                
        //reading parameters of addEntry form
        if (isset($_POST['componentID'])) $this->componentID = intval($_POST['componentID']);
        if (isset($_POST['contentID'])) $this->contentID = intval($_POST['contentID']);
        if (isset($_POST['c'])) $this->column = lcfirst(trim($_POST['c']));
        
        //reading parameters of main form
        if (isset($_POST['metaDescription'])) $this->metaDescription = trim($_POST['metaDescription']);
        if (isset($_POST['metaKeywords'])) $this->metaKeywords = trim($_POST['metaKeywords']);
        if (isset($_POST['configTitle'])) $this->configTitle = trim($_POST['configTitle']);
        if (isset($_POST['entries'])) $this->entries = JSON::decode(urldecode($_POST['entries']));
        
    }
    
    /**
     * @see \wcf\form\IForm::validate()
     */
    public function validate() {
        parent::validate();
        if ($this->form == 'addEntry') {
            //does nothing (for now)
        }
        else {
            $this->validateTitle();
            $this->validateDescription();
            $this->validateKeywords();
        }
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
        if (strlen($this->metaKeywords) > $this->maxLength) {
            throw new UserInputException('metaKeywords', 'tooLong');
        }
    }
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        parent::save();
        
        if ($this->form == 'addEntry' && $this->viaAjax) {
            $entry = new ConfigEntry($this->componentID, $this->contentID);
            $echoContent = '<div id="'.$this->column.'-'.$this->componentID.'-'.$this->contentID.'">'.
                $entry->getContent().'</div>';
            echo $echoContent;
            exit;
        }
        
        if ($this->action == 'edit') $this->configStorage = new ConfigStorage();
        
        $leftColumn = $centerColumn = $rightColumn = false;
        
        //filling ConfigStorage with entries
        foreach ($this->entries['left'] as $index => $id) {
            $idArray = explode('-', $id);
            $componentID = $idArray[1];
            $contentID = $idArray[2];
            $entry = new ConfigEntry($componentID, $contentID);
            $this->configStorage->addEntry($entry, 'left');
            $leftColumn = true;
        }
        
        foreach ($this->entries['center'] as $index => $id) {
            $idArray = explode('-', $id);
            $componentID = $idArray[1];
            $contentID = $idArray[2];
            $entry = new ConfigEntry($componentID, $contentID);
            $this->configStorage->addEntry($entry, 'center');
            $centerColumn = true;
        }
    
        foreach ($this->entries['right'] as $index => $id) {
            $idArray = explode('-', $id);
            $componentID = $idArray[1];
            $contentID = $idArray[2];
            $entry = new ConfigEntry($componentID, $contentID);
            $this->configStorage->addEntry($entry, 'right');
            $rightColumn = true;
        }
        
        //create template for this config
        $templateName = substr(StringUtil::getHash($this->configTitle), 0, 10);
        
        $this->writeTemplate($templateName, array(
            'leftColumn' => $leftColumn,
            'centerColumn' => $centerColumn,
            'rightColumn' => $rightColumn
        ));
        
        //save config data in database
        $parameters = array(
            'data' => array(
                'configTitle' => $this->configTitle,
                'metaDescription' => $this->metaDescription,
                'metaKeywords' => $this->metaKeywords,
                'templateName' => $templateName,
                'storage' => serialize($this->configStorage)
            )
        );
        if ($this->action == 'add') {
            $action = new ConfigAction(array(), 'create', $parameters);
            $action->executeAction();
        } elseif ($this->action = 'edit') {
            $action = new ConfigAction(array($this->configID), 'update', $parameters);
            $action->executeAction();
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
     * Writes the template of the option.
     *
     * @param string $templateName
     * @param array $options
     */
    protected function writeTemplate($templateName, array $options) {
        $templateDir = ULTIMATE_DIR.'templates/';
        $file = new File($templateDir.$templateName.'.tpl');
        $entries = $this->configStorage->getEntries();
                
        $localOptions = array(
            'metaDescription' => $this->metaDescription,
            'metaKeywords' => $this->metaKeywords,
            'configTitle' => $this->configTitle,
            'entriesLeft' => $entries['left'],
            'entriesCenter' => $entries['center'],
            'entriesRight' => $entries['right']
        );
        $finalOptions = array_merge($localOptions, $options);
        $output = UltimateCore::getTPL()->fetch('generalCMSTemplate', $finalOptions);
        $file->write($output);
        $file->close();
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
            'metaKeywords' => $this->metaKeywords,
            'components' => $this->components,
            'contents' => $this->contents
        ));
    }
}
