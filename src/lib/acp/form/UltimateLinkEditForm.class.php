<?php
namespace ultimate\acp\form;
use ultimate\acp\form\UltimateLinkAddForm;
use ultimate\data\link\Link;
use ultimate\data\link\LinkAction;
use wcf\form\AbstractForm;

/**
 * Shows the UltimateLinkEdit form.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.form
 * @category Ultimate CMS
 */
class UltimateLinkEditForm extends UltimateLinkAddForm {
    
    /**
     * @see wcf\acp\form\ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.acp.menu.item.link.ultimate.links';
    
    /**
     * @see wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'admin.content.ultimate.canEditLink'
    );
    
    /**
     * Contains the link id.
     * @var int
     */
    protected $linkID = 0;
    
    /**
     * @see wcf\page\AbstractPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_REQUEST['id'])) $this->linkID = intval($_REQUEST['id']);
    }
    
    /**
     * @see ultimate\acp\form\UltimateLinkAddForm::save()
     */
    public function save() {
        AbstractForm::save();
        
        $parameters = array(
            'data' => array(
                'configID' => $this->configID,
                'linkSlug' => $this->slug,
            )
        );
        
        $action = new LinkAction(array($this->linkID), 'update', $parameters);
        $action->executeAction();
        
        $this->saved();
        
        UltimateCore::getTPL()->assign('success', true);
    }
    
    /**
     * @see ultimate\acp\form.UltimateLinkAddForm::readData()
     */
    public function readData() {
        if (!count($_POST)) {
            $link = new Link($this->linkID);
            $this->configID = $link->configID;
            $this->slug = $link->linkSlug;
        }
        parent::readData();
    }
    
    /**
     * @see ultimate\acp\form\UltimateLinkAddForm::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        UltimateCore::getTPL()->assign(array(
            'linkID' => $this->linkID,
            'action' => 'edit'
        ));
    }
}
