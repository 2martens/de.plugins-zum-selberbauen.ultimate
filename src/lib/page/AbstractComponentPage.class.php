<?php
namespace ultimate\page;
use ultimate\data\content\Content;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\page\AbstractPage;
use wcf\system\WCF;

/**
 * Every ComponentPage should extend this class.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage page
 * @category Ultimate CMS
 */
abstract class AbstractComponentPage extends AbstractPage {
    
    /**
     * @see wcf\page\AbstractPage::$useTemplate
     */
    public $useTemplate = false;
    
    /**
     * Contains the id of the attached content.
     * @var int
     */
    protected $contentID = 0;
    
    /**
     * Contains the content which will be displayed.
     * @var array
     */
    protected $displayContent = array();
    
    /**
     * Contains the database table of the content.
     * @var string
     */
    protected $databaseTable = 'content';
    
    /**
     * Contains the output of the template after it is fetched.
     * @var string
     */
    protected $output = '';
    
    /**
     * Creates a new AbstractComponentPage object.
     * @param string $contentName
     */
    public function __construct($contentID) {
        $this->contentID = StringUtil::trim($contentID);
        parent::__construct();
        //returns the output
        return $this->output();
    }
    
    /**
     * @see wcf\page\AbstractPage::readData()
     */
    public function readData() {
        parent::readData();
        
        $content = new Content($this->contentID);
        $this->displayContent = array(
            'contentID' => intval($content->contentID),
            'contentTitle' => StringUtil::trim($content->contentTitle),
            'contentDescription' => StringUtil::trim($content->contentDescription),
            'contentText' => StringUtil::trim($content->contentText)
        );
    }
    
    /**
     * @see wcf\page\AbstractPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign(array(
            'contentID' => $this->displayContent['contentID'],
            'contentTitle' => $this->displayContent['contentTitle'],
            'contentDescription' => $this->displayContent['contentDescription'],
            'contentText' => $this->displayContent['contentText']
        ));
    }
    
    /**
     * @see wcf\page\AbstractPage::show()
     */
    public function show() {
        parent::show();
        $this->output = WCF::getTPL()->fetch($this->templateName);
    }
}
