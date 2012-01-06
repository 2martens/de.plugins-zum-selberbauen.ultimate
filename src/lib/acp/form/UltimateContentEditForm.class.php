<?php
namespace ultimate\acp\form;
use ultimate\acp\form\UltimateContentAddForm;
use ultimate\data\content\Content;
use ultimate\data\content\ContentAction;
use ultimate\system\UltimateCore;
use wcf\form\AbstractForm;

/**
 * Shows the UltimateContentEdit form.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.form
 * @category Ultimate CMS
 */
class UltimateContentEditForm extends UltimateContentAddForm {
    
    /**
     * @see \wcf\acp\form\ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.acp.menu.item.link.ultimate.contents';
    
    /**
     * @see \wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'admin.content.ultimate.canEditContent'
    );
    
    /**
     * Contains the content id.
     * @var int
     */
    protected $contentID = 0;
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        AbstractForm::save();
        $parameters = array(
        	'data' => array(
            	'title' => $this->title,
            	'text' => $this->text
            )
        );
        
        $action = new ContentAction(array($this->contentID), 'update', $parameters);
        $action->executeAction();
        $this->saved();
        
        UltimateCore::getTPL()->assign('success', true);
    }
    
    /**
     * @see \wcf\page\IPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_REQUEST['id'])) $this->contentID = intval($_REQUEST['id']);
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        if (!count($_POST)) {
            $content = new Content($this->contentID);
            $this->title = $content->contentTitle;
            $this->description = $content->contentDescription;
            $this->text = $content->cotentText;
        }
        parent::readData();
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        UltimateCore::getTPL()->assign(array(
        	'contentID' => $this->contentID,
            'action' => 'edit'
        ));
    }
}
