<?php
namespace ultimate\acp\form;
use ultimate\data\config\ConfigList;
use ultimate\data\page\PageAction;
use ultimate\system\UltimateCore;
use ultimate\util\PageUtil;
use wcf\acp\form\ACPForm;
use wcf\system\exception\UserInputException;

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
     * Contains the config id.
     * @var int
     */
    protected $configID = 0;
    
    /**
     * Contains an array of config options.
     * @var array
     */
    protected $configOptions = array();
    
    /**
     * Contains the page slug.
     * @var string
     */
    protected $pageSlug = '';
    
    /**
     * @see \wcf\form\IForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_POST['configID'])) $this->configID = intval($_POST['configID']);
        if (isset($_POST['slug'])) $this->slug = trim($_POST['slug']);
    }
    
    /**
     * @see \wcf\form\IForm::validate()
     */
    public function validate() {
        parent::validate();
        $this->validateConfigID();
        $this->validateSlug();
    }
    
    /**
     * Validates link configID.
     *
     * @throws UserInputException
     */
    protected function validateConfigID() {
        if (!$this->configID) {
            throw new UserInputException('configID');
        }
    }
    
    /**
     * Validates link slug.
     *
     * @throws UserInputException
     */
    protected function validateSlug() {
        if (empty($this->slug)) {
            throw new UserInputException('slug');
        }
        
        if (!PageUtil::isAvailableSlug($this->slug)) {
            throw new UserInputException('slug', 'notUnique');
        }
    }
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        parent::save();
        
        $parameters = array(
            'data' => array(
                'configID' => $this->configID,
                'linkSlug' => $this->slug
            )
        );
        
        $action = new LinkAction(array(), 'create', $parameters);
        $action->executeAction();
        
        $this->saved();
        
        UltimateCore::getTPL()->assign('success', true);
        
        //showing empty form
        $this->configID = 0;
        $this->slug = '';
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        if (!count($_POST)) {
            $configList = new ConfigList();
            $configList->readObjects();
            $objects = $configList->getObjects();
            foreach ($objects as $object) {
                $this->configOptions[$object->configID] = $object->configTitle;
            }
        }
        parent::readData();
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        UltimateCore::getTPL()->assign(array(
            'configID' => $this->configID,
            'configOptions' => $this->configOptions,
            'slug' => $this->slug,
            'action' => 'add'
        ));
    }
}
