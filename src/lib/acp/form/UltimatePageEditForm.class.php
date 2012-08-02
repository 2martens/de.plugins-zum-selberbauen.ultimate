<?php
namespace ultimate\acp\form;
use ultimate\acp\form\UltimatePageAddForm;
use ultimate\data\page\Page;
use ultimate\data\page\PageAction;
use ultimate\data\page\PageEditor;
use ultimate\system\UltimateCore;
use ultimate\util\PageUtil;
use wcf\form\AbstractForm;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;

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
    public $pageID = 0;
    
    /**
     * Contains the Page object of this page.
     * @var \ultimate\data\page\Page
     */
    public $page = null;
    
    /**
     * Contains the language output for the save button.
     * @var string
     */
    protected $saveButtonLang = '';
    
    /**
     * Contains the language output for the publish button.
     * @var string
     */
    protected $publishButtonLang = '';
    
    /**
     * @see \wcf\page\IPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_REQUEST['id'])) $this->pageID = intval($_REQUEST['id']);
        $page = new Page($this->pageID);
        if (!$page->__get('pageID')) {
            throw new IllegalLinkException();
        }
        
        $this->page = $page;
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        if (!count($_POST)) {
            $this->contentID = $this->page->getContent();
            $this->pageTitle = $this->page->__get('pageTitle');
            $this->pageSlug = $this->page->__get('pageSlug');
            $this->pageParent = $this->page->__get('pageParent');
            $this->lastModified = $this->page->__get('lastModified');
            $this->contents = PageUtil::getAvailableContents($this->pageID);
            $this->pages = PageUtil::getAvailablePages($this->pageID);
            I18nHandler::getInstance()->setOptions('pageTitle', PACKAGE_ID, $this->pageTitle, 'ultimate.page.\d+.pageTitle');
            
            // reading cache
            $cacheName = 'usergroups';
            $cacheBuilderClassName = '\wcf\system\cache\builder\UserGroupCacheBuilder';
            $file = WCF_DIR.'cache/cache.'.$cacheName.'.php';
            CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
            $this->groups = CacheHandler::getInstance()->get($cacheName, 'groups');
            
            /* @var $dateTime \DateTime */
            $dateTime = $this->page->__get('publishDateObject');
            $date = UltimateCore::getLanguage()->getDynamicVariable(
                'ultimate.date.dateFormat',
                array(
                    'britishEnglish' => ULTIMATE_GENERAL_ENGLISHLANGUAGE
                )
            );
            $time = UltimateCore::getLanguage()->get('wcf.date.timeFormat');
            $format = str_replace(
                '%time%',
                $time,
                str_replace(
                    '%date',
                    $date,
                    UltimateCore::getLanguage()->get('ultimate.date.dateTimeFormat')
                )
            );
            $this->publishDate = $dateTime->format($format);
            $this->publishDateTimestamp = $dateTime->getTimestamp();
            
            // get status data
            $this->statusID = $this->page->__get('status');
            $this->statusOptions = array(
                0 => UltimateCore::getLanguage()->get('wcf.acp.ultimate.status.draft'),
                1 => UltimateCore::getLanguage()->get('wcf.acp.ultimate.status.pendingReview'),
            );
            
            // fill publish button with fitting language
            $this->publishButtonLang = UltimateCore::getLanguage()->get('ultimate.button.publish');
            if ($this->statusID == 2) {
                $this->statusOptions[2] = UltimateCore::getLanguage()->get('wcf.acp.ultimate.status.scheduled');
                $this->publishButtonLang = UltimateCore::getLanguage()->get('ultimate.button.update');
            } elseif ($this->statusID == 3) {
                $this->statusOptions[3] = UltimateCore::getLanguage()->get('wcf.acp.ultimate.status.published');
                $this->publishButtonLang = UltimateCore::getLanguage()->get('ultimate.button.update');
            }
            
            // fill save button with fitting language
            $saveButtonLangArray = array(
                0 => UltimateCore::getLanguage()->get('ultimate.button.saveAsDraft'),
                1 => UltimateCore::getLanguage()->get('ultimate.button.saveAsPending'),
                2 => '',
                3 => ''
            );
            $this->saveButtonLang = $saveButtonLangArray[$this->statusID];
            
            // get visibility data
            $this->visibility = $this->page->__get('visibility');
            $this->groupIDs = array_keys($this->page->__get('groups'));
        }
        AbstractForm::readData();
    }
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        AbstractForm::save();
        
        if ($this->supportI18n) {
            $this->pageTitle = 'ultimate.page.'.$this->pageID.'.pageTitle';
            if (I18nHandler::getInstance()->isPlainValue('pageTitle')) {
                I18nHandler::getInstance()->remove($this->pageTitle, PACKAGE_ID);
                $this->pageTitle = I18nHandler::getInstance()->getValue('pageTitle');
            } else {
                I18nHandler::getInstance()->save('pageTitle', $this->pageTitle, 'ultimate.page', PACKAGE_ID);
            }
        }
        
        // change status to planned or publish
        if ($this->saveType == 'publish') {
            if ($this->publishDateTimestamp > TIME_NOW) {
                $this->statusID = 2; // scheduled
            } elseif ($this->publishDateTimestamp < TIME_NOW) {
                $this->statusID = 3; // published
            }
        }
        
        $parameters = array(
            'data' => array(
                'authorID' => UltimateCore::getUser()->userID,
                'pageParent' => $this->pageParent,
                'pageTitle' => $this->pageTitle,
                'pageSlug' => $this->pageSlug,
                'publishDate' => $this->publishDateTimestamp,
                'lastModified' => TIME_NOW,
                'status' => $this->statusID,
                'visibility' => $this->visibility
            ),
            'contentID' => $this->contentID
        );
        
        if ($this->visibility == 'protected') {
            $parameters['userGroupIDs'] = $this->groupIDs;
        }
        
        $this->objectAction = new PageAction(array($this->pageID), 'update', $parameters);
        $this->objectAction->executeAction();
        
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
            'pageID' => $this->pageID,
            'publishButtonLang' => $this->publishButtonLang,
            'action' => 'edit'
        ));
        
        // hide the save button if you edit a page which is already scheduled or published
        if (!empty($this->saveButtonLang)) {
            // status id == (0|1)
            UltimateCore::getTPL()->assign('saveButtonLang', $this->saveButtonLang);
        }
        else {
            // status id == (2|3)
            UltimateCore::getTPL()->assign('disableSaveButton', true);
        }
    }
    
}
