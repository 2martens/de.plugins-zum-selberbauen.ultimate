<?php
namespace ultimate\form;
use ultimate\system\config\storage\ConfigStorageHandler;

use wcf\form\AbstractSecureForm;

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
     * Contains the config id.
     * @var int
     */
    protected $configID = 0;
    
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
        if (!count($_POST) && $this->action = 'edit') {
            ConfigStorageHandler::getInstance()->addConfigID($configID);
            $this->entries = ConfigStorageHandler::getInstance()->getConfigStorage()->getEntries();
        }
        parent::readData();
    }
    
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        UltimateCore::getTPL()->assign(array(
            'entries' => $this->entries
        ));
    }
}
