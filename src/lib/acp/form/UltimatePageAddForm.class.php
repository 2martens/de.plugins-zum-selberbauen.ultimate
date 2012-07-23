<?php
namespace ultimate\acp\form;
use wcf\system\language\I18nHandler;

use ultimate\data\content\ContentList;
use ultimate\data\page\PageAction;
use ultimate\system\UltimateCore;
use ultimate\util\PageUtil;
use wcf\acp\form\ACPForm;
use wcf\system\cache\CacheHandler;
use wcf\system\event\EventHandler;
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
     * Contains the content id.
     * @var int
     */
    public $contentID = 0;
    
    /**
     * Contains all available contents.
     * @var array<ultimate\data\content\Content>
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
     * If true, the I18n feature will be used.
     * @var boolean
     */
    protected $supportI18n = true;
    
    /**
     * @see \wcf\form\IForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_POST['content'])) $this->contentID = intval($_POST['content']);
        if (isset($_POST['pageTitle'])) $this->pageTitle = trim($_POST['pageTitle']);
        if (isset($_POST['pageSlug'])) $this->pageSlug = trim($_POST['pageSlug']);
        
        if ($this->supportI18n) {
            // testing I18n
            I18nHandler::getInstance()->register('pageTitle');
            I18nHandler::getInstance()->setOptions('pageTitle', PACKAGE_ID, '', '');
            I18nHandler::getInstance()->readValues();
        }
    }
    
    /**
     * @see \wcf\form\IForm::validate()
     */
    public function validate() {
        parent::validate();
        $this->validateContentID();
        $this->validateTitle();
        $this->validateSlug();
    }
    
    /**
     * Validates the contentID.
     *
     * @throws UserInputException
     */
    protected function validateContentID() {
        if (!$this->contentID) {
            throw new UserInputException('content', 'notSelected');
        }
        if (!array_key_exists($this->contentID, $this->contents)) {
            throw new UserInputException('content', 'notAvailable');
        }
    }
    
    /**
     * Validates the page title.
     *
     * @throws UserInputException
     */
    protected function validateTitle() {
        if ($this->supportI18n) {
            if (!I18nHandler::getInstance()->validateValue('pageTitle')) {
                throw new UserInputException('pageTitle');
            }
        }
        else {
            if (empty($this->pageTitle)) {
                throw new UserInputException('pageTitle');
            }
        }
    }
    
    /**
     * Validates page slug.
     *
     * @throws UserInputException
     */
    protected function validateSlug() {
        if (empty($this->slug)) {
            throw new UserInputException('pageSlug');
        }
        
        if (!PageUtil::isAvailableSlug($this->pageSlug)) {
            throw new UserInputException('pageSlug', 'notUnique');
        }
    }
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        parent::save();
        
        $parameters = array(
            'data' => array(
                'authorID' => UltimateCore::getUser()->userID,
                'contentID' => $this->contentID,
                'pageTitle' => $this->pageTitle,
                'pageSlug' => $this->pageSlug,
                'lastModified' => TIME_NOW
            )
        );
        
        if ($this->supportI18n) {
            I18nHandler::getInstance()->save('pageTitle', 'ultimate.page.'.$this->slug, 'ultimate.page', PACKAGE_ID);
        }
        
        $action = new PageAction(array(), 'create', $parameters);
        $action->executeAction();
        
        $this->saved();
        
        UltimateCore::getTPL()->assign(
            'success', true
        );
        
        //showing empty form
        $this->contentID = 0;
        $this->pageSlug = '';
        $this->contentIDs = $this->contents = array();
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        if (!count($_POST)) {
            $this->loadCache();
            foreach ($this->contents as $contentID => $content) {
                if (!count($content->getCategories())) continue;
                unset($this->contents[$contentID]);
            }
        }
        parent::readData();
    }
    
    /**
     * Loads the cache.
     */
    public function loadCache() {
        // fire event
        EventHandler::getInstance()->fireEvent($this, 'loadCache');
        
        $cache = 'content';
        $cacheBuilderClass = '\ultimate\system\cache\builder\UltimateContentCacheBuilder';
        $file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
        CacheHandler::getInstance()->addResource($cache, $file, $cacheBuilderClass);
        $result = CacheHandler::getInstance()->get($cache);
        $this->contents = $result['contents'];
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        
        if ($this->supportI18n) {
            $useRequestData = (count($_POST)) ? true : false;
            I18nHandler::getInstance()->assignVariables($useRequestData);
        }
        UltimateCore::getTPL()->assign(array(
            'contentID' => $this->contentID,
            'contents' => $this->contents,
            'pageTitle' => $this->pageTitle,
            'pageSlug' => $this->pageSlug,
            'supportI18n' => $this->supportI18n,
            'action' => 'add'
        ));
    }
}
