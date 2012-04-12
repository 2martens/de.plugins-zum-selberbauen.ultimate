<?php
namespace ultimate\page;
use ultimate\system\UltimateCore;
use ultimate\data\content\Content;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\page\AbstractPage;
use wcf\system\bbcode\MessageParser;
use wcf\util\StringUtil;

/**
 * Every ComponentPage should extend this class.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage page
 * @category Ultimate CMS
 */
abstract class AbstractComponentPage extends AbstractPage {
    
    /**
     * @see \wcf\page\AbstractPage::$useTemplate
     */
    public $useTemplate = false;
    
    /**
     * Contains the id of the attached content.
     * @var int
     */
    protected $contentID = 0;
    
    /**
     * Contains the id of the attached category.
     * @var int
     */
    protected $categoryID = 0;
    
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
     *
     * @param int $contentID
     * @param int $categoryID
     */
    public function __construct($contentID, $categoryID = 0) {
        $this->contentID = intval($contentID);
        $this->categoryID = intval($categoryID);
        parent::__construct();
    }
    
    /**
     * Returns the output.
     */
    public function getOutput() {
        return $this->output;
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        parent::readData();
        
        $content = new Content($this->contentID);
        $parsedText = MessageParser::getInstance()->parse(
            $content->contentText,
            $content->enableSmilies,
            $content->enableHtml,
            $content->enableBBCodes,
            false
        );
        $this->displayContent = array(
            'contentID' => intval($content->contentID),
            'contentTitle' => StringUtil::trim($content->contentTitle),
            'contentDescription' => StringUtil::trim($content->contentDescription),
            'contentText' => $parsedText
        );
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        UltimateCore::getTPL()->assign(array(
            'contentID' => $this->displayContent['contentID'],
            'categoryID' => $this->categoryID,
            'contentTitle' => $this->displayContent['contentTitle'],
            'contentDescription' => $this->displayContent['contentDescription'],
            'contentText' => $this->displayContent['contentText']
        ));
    }
    
    /**
     * @see \wcf\page\IPage::show()
     */
    public function show() {
        parent::show();
        $this->output = UltimateCore::getTPL()->fetch($this->templateName);
    }
}
