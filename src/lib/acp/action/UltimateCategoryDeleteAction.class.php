<?php
namespace ultimate\acp\action;
use ultimate\data\category\CategoryAction;
use wcf\action\AbstractSecureAction;
use wcf\util\HeaderUtil;

/**
 * Deleted the specified category.
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
        'admin.content.ultimate.canDeleteCategory'
    );

    /**
     * Contains the category id.
     * @var int
     */
    public $categoryID = 0;
    
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
        if (isset($_GET['id'])) $this->categoryID = intval($_GET['id']);
        if (isset($_GET['url'])) $this->url = trim($_GET['url']);
    }
    
    /**
     * @see \wcf\action\IAction::execute()
     */
    public function execute() {
        parent::execute();
        $categoryAction = new CategoryAction(array($this->categoryID), 'delete');
        $categoryAction->executeAction();
        $this->executed();
        
        //redirecting back to referrer
        HeaderUtil::redirect($this->url);
        exit;
    }
}
