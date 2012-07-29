<?php
namespace ultimate\acp\form;
use ultimate\acp\form\UltimateContentAddForm;
use ultimate\data\content\Content;
use ultimate\data\content\ContentAction;
use ultimate\data\content\ContentEditor;
use ultimate\system\UltimateCore;
use wcf\form\MessageForm;
use wcf\form\RecaptchaForm;
use wcf\system\language\I18nHandler;
use wcf\system\menu\acp\ACPMenu;

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
    public $activeMenuItem = 'wcf.acp.menu.link.ultimate.content';
    
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
    public $contentID = 0;
    
    /**
     * Contains the Content object of this content.
     * @var \ultimate\data\content\Content
     */
    public $content = null;
    
    /**
     * @see \wcf\page\IPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        //I18nHandler::getInstance()->disableAssignValueVariables();
        if (isset($_REQUEST['id'])) $this->contentID = intval($_REQUEST['id']);
        $content = new Content($this->contentID);
        if (!$content->contentID) {
            throw new IllegalLinkException();
        }
        
        $this->content = $content;
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        if (!count($_POST)) {
            $this->subject = $this->content->contentTitle;
            $this->description = $this->content->contentDescription;
            $this->text = $this->content->contentText;
            $this->lastModified = $this->content->lastModified;
            $this->categoryIDs = array_keys($this->content->getCategories());
            I18nHandler::getInstance()->setOptions('subject', PACKAGE_ID, $this->subject, 'ultimate.content.\d+.contentTitle');
            I18nHandler::getInstance()->setOptions('description', PACKAGE_ID, $this->description, 'ultimate.content.\d+.contentDescription');
            I18nHandler::getInstance()->setOptions('text', PACKAGE_ID, $this->text, 'ultimate.content.\d+.contentText');
        }
        parent::readData();
    }
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        RecaptchaForm::save();
        
        $this->subject = 'ultimate.content.'.$this->contentID.'.contentTitle';
        if (I18nHandler::getInstance()->isPlainValue('subject')) {
            I18nHandler::getInstance()->remove($this->subject, PACKAGE_ID);
            $this->subject = I18nHandler::getInstance()->getValue('subject');
        } else {
            I18nHandler::getInstance()->save('subject', $this->subject, 'ultimate.content', PACKAGE_ID);
        }
        
        $this->description = 'ultimate.content.'.$this->contentID.'.contentDescription';
        if (I18nHandler::getInstance()->isPlainValue('description')) {
            I18nHandler::getInstance()->remove($this->description, PACKAGE_ID);
            $this->description = I18nHandler::getInstance()->getValue('description');
        } else {
            I18nHandler::getInstance()->save('description', $this->description, 'ultimate.content', PACKAGE_ID);
        }
        
        $text = 'ultimate.content.'.$this->contentID.'.contentText';
        if (I18nHandler::getInstance()->isPlainValue('text')) {
            I18nHandler::getInstance()->remove($text, PACKAGE_ID);
        } else {
            $this->text = $text;
            I18nHandler::getInstance()->save('text', $this->text, 'ultimate.content', PACKAGE_ID);
            // parse URLs
            if ($this->parseURL == 1) {
                $textValues = I18nHandler::getInstance()->getValues('text');
                foreach ($textValues as $languageID => $text) {
                    $textValues[$languageID] = URLParser::getInstance()->parse($text);
                }
            
                // nasty workaround, because you can't change the values of I18nHandler before save
                $sql = 'UPDATE wcf'.WCF_N.'_language_item
                        SET    languageItemValue = ?
                        WHERE  languageID        = ?
                        AND    languageItem      = ?
                        AND    packageID         = ?';
                /* @var $statement \wcf\system\database\statement\PreparedStatement */
                $statement = UltimateCore::getDB()->prepareStatement($sql);
                UltimateCore::getDB()->beginTransaction();
                foreach ($textValues as $languageID => $text) {
                    $statement->executeUnbuffered(array(
                        $text,
                        $languageID,
                        'ultimate.content.'.$contentID.'.contentText',
                        PACKAGE_ID
                    ));
                }
                UltimateCore::getDB()->commitTransaction();
            }
        }
        
        $parameters = array(
        	'data' => array(
        	    'authorID' => UltimateCore::getUser()->userID,
            	'contentTitle' => $this->subject,
                'contentDescription' => $this->description,
            	'contentText' => $this->text,
                'enableBBCodes' => $this->enableBBCodes,
                'enableHtml' => $this->enableHtml,
                'enableSmilies' => $this->enableSmilies,
        	    'lastModified' => TIME_NOW
            ),
            'categories' => $this->categoryIDs
        );
        
        $action = new ContentAction(array($this->contentID), 'update', $parameters);
        $action->executeAction();
        $this->saved();
        
        UltimateCore::getTPL()->assign('success', true);
    }
    
    
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        
        $useRequestData = (count($_POST)) ? true : false;
        I18nHandler::getInstance()->assignVariables($useRequestData);
        
        UltimateCore::getTPL()->assign(array(
        	'contentID' => $this->contentID,
        	'action' => 'edit'
        ));
    }
    
    /**
     * @see \wcf\form\IForm::show()
     */
    public function show() {
        if (!empty($this->activeMenuItem)) {
            ACPMenu::getInstance()->setActiveMenuItem($this->activeMenuItem);
        }
        MessageForm::show();
    }
}
