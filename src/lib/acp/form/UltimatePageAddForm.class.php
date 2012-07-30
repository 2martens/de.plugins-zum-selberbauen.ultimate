<?php
namespace ultimate\acp\form;
use wcf\util\DateUtil;

use ultimate\data\content\ContentList;
use ultimate\data\page\PageAction;
use ultimate\data\page\PageEditor;
use ultimate\system\UltimateCore;
use ultimate\util\PageUtil;
use wcf\acp\form\ACPForm;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\UserInputException;
use wcf\system\exception\SystemException;
use wcf\system\language\I18nHandler;
use wcf\system\Regex;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

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
     * @var string[]
     * @see \wcf\acp\form\ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.acp.menu.link.ultimate.page.add';
    
    /**
     * @see \wcf\page\AbstractPage::$templateName
     */
    public $templateName = 'ultimatePageAdd';
    
    /**
     * @var string[]
     * @see \wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'admin.content.ultimate.canAddPage'
    );
    
    /**
     * Contains the content id.
     * @var integer
     */
    public $contentID = 0;
    
    /**
     * Contains all available contents.
     * @var \ultimate\data\content\Content[]
     */
    public $contents = array();
    
    /**
     * Contains the title of the page.
     * @var string
     */
    public $pageTitle = '';
    
    /**
     * Contains the page slug.
     * @var string
     */
    public $pageSlug = '';
    
    /**
     * Contains all possible parent pages.
     * @var \ultimate\data\page\Page[]
     */
    public $pages = array();
    
    /**
     * Contains the parent page id.
     * @var integer
     */
    public $pageParent = 0;
    
    /**
     * Contains the visibility.
     * @var string
     */
    public $visibility = 'public';
    
    /**
     * Contains the chosen groupIDs.
     * @var integer[]
     */
    public $groupIDs = array();
    
    /**
     * Contains the publish date.
     * @var string
     */
    public $publishDate = '';
    
    /**
     * Contains the publish date as timestamp.
     * @var integer
     */
    public $publishDateTimestamp = TIME_NOW;
    
    /**
     * Contains all status options.
     * @var string[]
     */
    public $statusOptions = array();
    
    /**
     * Contains the status id.
     * @var integer
     */
    public $statusID = 0;
    
    /**
     * jQuery datepicker date format.
     * @var string
     */
    protected $dateFormat = 'yy-mm-dd';
    
    
    /**
     * @see \wcf\page\IPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        I18nHandler::getInstance()->register('pageTitle');
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        $this->contents = PageUtil::getAvailableContents();
        $this->pages = PageUtil::getAvailablePages();
        $cacheName = 'usergroups';
        $cacheBuilderClassName = '\wcf\system\cache\builder\UserGroupCacheBuilder';
        $file = WCF_DIR.'cache/cache.'.$cacheName.'.php';
        CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
        $this->groups = CacheHandler::getInstance()->get($cacheName, 'groups');
        
        // fill status options
        $this->statusOptions = array(
            0 => UltimateCore::getLanguage()->get('wcf.acp.ultimate.status.draft'),
            1 => UltimateCore::getLanguage()->get('wcf.acp.ultimate.status.pendingReview'),
            2 => UltimateCore::getLanguage()->get('wcf.acp.ultimate.status.published')
        );
        
        // fill publishDate with default value (today)
        $dateTime = DateUtil::getDateTimeByTimestamp(TIME_NOW);
        $dateTime->setTimezone(UltimateCore::getUser()->getTimezone());
        $format = UltimateCore::getLanguage()->getDynamicVariable(
                'ultimate.date.dateFormat',
                array(
                    'britishEnglish' => ULTIMATE_GENERAL_ENGLISHLANGUAGE
                )
        );
        $this->publishDate = $dateTime->format($format);
        
        parent::readData();
    }
    
    /**
     * @see \wcf\form\IForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        
        I18nHandler::getInstance()->readValues();
        I18nHandler::getInstance()->enableAssignValueVariables();
        if (I18nHandler::getInstance()->isPlainValue('pageTitle')) $this->pageTitle = StringUtil::trim(I18nHandler::getInstance()->getValue('pageTitle'));
        if (isset($_POST['pageParent'])) $this->pageParent = intval($_POST['pageParent']);
        if (isset($_POST['content'])) $this->contentID = intval($_POST['content']);
        if (isset($_POST['pageSlug'])) $this->pageSlug = StringUtil::trim($_POST['pageSlug']);
        if (isset($_POST['visibility'])) $this->visibility = StringUtil::trim($_POST['visibility']);
        if (isset($_POST['groupIDs'])) $this->groupIDs = ArrayUtil::toIntegerArray($_POST['groupIDs']);
        if (isset($_POST['publishDate'])) $this->publishDate = StringUtil::trim($_POST['publishDate']);
        if (isset($_POST['dateFormat'])) $this->dateFormat = StringUtil::trim($_POST['dateFormat']);
    }
    
    /**
     * @see \wcf\form\IForm::validate()
     */
    public function validate() {
        parent::validate();
        $this->validateContentID();
        $this->validatePageParent();
        $this->validateTitle();
        $this->validateSlug();
        $this->validateVisibility();
        $this->validatePublishDate();
    }
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        parent::save();
        
        $parameters = array(
            'data' => array(
                'authorID' => UltimateCore::getUser()->userID,
                'pageParent' => $this->pageParent,
                'pageTitle' => $this->pageTitle,
                'pageSlug' => $this->pageSlug,
                'publishDate' => $this->publishDateTimestamp,
                'lastModified' => TIME_NOW
            ),
            'contentID' => $this->contentID
        );
        
        $this->objectAction = new PageAction(array(), 'create', $parameters);
        $this->objectAction->executeAction();
        
        if (!I18nHandler::getInstance()->isPlainValue('pageTitle')) {
            $returnValues = $this->objectAction->getReturnValues();
            $pageID = $returnValues['returnValues']->pageID;
            I18nHandler::getInstance()->save('pageTitle', 'ultimate.page.'.$pageID.'.pageTitle', 'ultimate.page', PACKAGE_ID);
        
            $pageEditor = new PageEditor($returnValues['returnValues']);
            $pageEditor->update(array(
                'pageTitle' => 'ultimate.page.'.$pageID.'.pageTitle'
            ));
        }
        
        $this->saved();
        
        UltimateCore::getTPL()->assign(
            'success', true
        );
        
        //showing empty form
        $this->contentID = $this->pageParent = $this->publishDateTimestamp = 0;
        $this->pageTitle = $this->pageSlug = $this->publishDate = '';
        I18nHandler::getInstance()->disableAssignValueVariables();
        $this->contents = $this->pages = $this->groupIDs = array();
    }
    
    
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        
        I18nHandler::getInstance()->assignVariables();
        UltimateCore::getTPL()->assign(array(
            'contentID' => $this->contentID,
            'contents' => $this->contents,
            'pages' => $this->pages,
            'pageParent' => $this->pageParent,
            'pageTitle' => $this->pageTitle,
            'pageSlug' => $this->pageSlug,
            'groups' => $this->groups,
            'groupIDs' => $this->groupIDs,
            'action' => 'add'
        ));
    }
    
    /**
     * Validates the contentID.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateContentID() {
        if (!$this->contentID) {
            throw new UserInputException('content', 'notSelected');
        }
        if (!array_key_exists($this->contentID, $this->contents)) {
            throw new UserInputException('content', 'notValid');
        }
    }
    
    /**
     * Validates the parent page.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validatePageParent() {
        if ($this->pageParent != 0 && !array_key_exists($this->pageParent, $this->pages)) {
            throw new UserInputException('pageParent', 'notValid');
        }
    }
    
    /**
     * Validates the page title.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateTitle() {
        if (!I18nHandler::getInstance()->isPlainValue('pageTitle')) {
            if (!I18nHandler::getInstance()->validateValue('pageTitle')) {
                throw new UserInputException('pageTitle');
            }
        }
        else {
            if (empty($this->pageTitle)) {
                throw new UserInputException('pageTitle');
            }
            if (!PageUtil::isAvailableTitle($this->pageTitle, $this->pageParent)) {
                throw new UserInputException('pageTitle', 'notUnique');
            }
        }
    }
    
    /**
     * Validates page slug.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateSlug() {
        if (empty($this->pageSlug)) {
            throw new UserInputException('pageSlug');
        }
    
        if (!PageUtil::isAvailableSlug($this->pageSlug, $this->pageParent)) {
            throw new UserInputException('pageSlug', 'notUnique');
        }
    }
    
    /**
     * Validates visibility.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateVisibility() {
        $allowedValues = array(
            'public',
            'protected',
            'private'
        );
        if (!in_array($this->visibility, $allowedValues)) {
            throw new UserInputException('visibility', 'notValid');
        }
        
        // validate groupIDs, only important for protected
        if ($this->visibility != 'protected') return;
        
        foreach ($this->groupIDs as $groupID) {
            if (array_key_exists($groupID, $this->groups)) continue;
            throw new UserInputException('groupIDs', 'notValid');
            break;
        }
    }
    
    /**
     * Validates the publish date.
     *
     * @throws \wcf\system\exception\SystemException
     */
    protected function validatePublishDate() {
        $pattern = '\d{4}-\d{2}-\d{2}';
        $regex = new Regex($pattern);
        $dateTimeNow = new \DateTime('@'.TIME_NOW, UltimateCore::getUser()->getTimezone());
        if ($regex->match($this->publishDate)) {
            // the browser has implemented the input type date
            // or (more likely) the user hasn't changed the jQuery code
            // that means we get the date in the right order for processing
            $dateTime = \DateTime::createFromFormat(
                    'Y-m-d',
                    $this->publishDate,
                    UltimateCore::getUser()->getTimezone()
            );
            if ($dateTime->format('Y-m-d') == $dateTimeNow->format('Y-m-d')) {
                $this->publishDateTimestamp = $dateTimeNow->getTimestamp();
            }
            else {
                $this->publishDateTimestamp = $dateTime->getTimestamp();
            }
            return;
        }
        // for the very unlikely reason that the date is not in the format
        // Y-m-d, we have to make it that way
        $phpDateFormat = '';
        switch ($this->dateFormat) {
            case 'mm/dd/yy':
                $phpDateFormat = 'm/d/Y';
                break;
            case 'd M, y':
                $phpDateFormat = 'j M, y';
                break;
            case 'd MM, y':
                $phpDateFormat = 'j F, y';
                break;
            case 'DD, d MM, yy':
                $phpDateFormat = 'l, j F, Y';
                break;
        }
            
        $dateTime = \DateTime::createFromFormat(
            $phpDateFormat,
            $this->publishDate,
            UltimateCore::getUser()->getTimezone()
        );
        if ($dateTime->format('Y-m-d') == $dateTimeNow->format('Y-m-d')) {
            $this->publishDateTimestamp = $dateTimeNow->getTimestamp();
        }
        else {
            $this->publishDateTimestamp = $dateTime->getTimestamp();
        }
    }
}
