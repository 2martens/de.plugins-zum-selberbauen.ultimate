<?php
namespace ultimate\acp\action;
use ultimate\data\page\PageAction;
use wcf\action\AbstractSecureAction;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Deleted the specified page.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.action
 * @category Ultimate CMS
 */
class UltimatePageDeleteAction extends AbstractSecureAction {
    
    /**
     * @see \wcf\action\AbstractAction::$neededPermissions
     */
    public $neededPermissions = array(
        'admin.content.ultimate.canDeletePage'
    );

    /**
     * Contains the page id.
     * @var integer
     */
    public $pageID = 0;
    
    /**
     * Contains the redirect url.
     * @var string
     */
    protected $url = '';
    
    /**
     * @see \wcf\action\IAction::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_GET['id'])) $this->pageID = intval($_GET['id']);
        if (isset($_GET['url'])) $this->url = StringUtil::trim($_GET['url']);
    }
    
    /**
     * @see \wcf\action\IAction::execute()
     */
    public function execute() {
        parent::execute();
        $pageAction = new PageAction(array($this->pageID), 'delete');
        $pageAction->executeAction();
        $this->executed();
        
        //redirecting back to referrer
        HeaderUtil::redirect($this->url);
        exit;
    }
}
