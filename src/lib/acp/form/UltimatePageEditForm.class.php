<?php
namespace ultimate\acp\form;
use ultimate\acp\form\UltimatePageAddForm;
use ultimate\data\page\Page;
use ultimate\data\page\PageAction;
use ultimate\system\UltimateCore;

/**
 * Shows the UltimatePageEdit form.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.form
 * @category Ultimate CMS
 */
class UltimatePageEditForm extends UltimatePageAddForm {
    
    /**
     * @see \wcf\acp\form\ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.acp.menu.link.ultimate.page';
    
    /**
     * @see \wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'admin.content.ultimate.canEditPage'
    );
    
    /**
     * Contains the page id.
     * @var int
     */
    protected $pageID = 0;
    
    /**
     * @see \wcf\page\IPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_REQUEST['id'])) $this->pageID = intval($_REQUEST['id']);
    }
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        AbstractForm::save();
        
        $parameters = array(
            'data' => array(
                'configID' => $this->configID,
                'pageSlug' => $this->pageSlug,
            )
        );
        
        $action = new PageAction(array($this->pageID), 'update', $parameters);
        $action->executeAction();
        
        $this->saved();
        
        UltimateCore::getTPL()->assign('success', true);
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        if (!count($_POST)) {
            $link = new Page($this->pageID);
            $this->configID = $link->configID;
            $this->pageSlug = $page->pageSlug;
        }
        parent::readData();
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        UltimateCore::getTPL()->assign(array(
            'pageID' => $this->pageID,
            'action' => 'edit'
        ));
    }
}
