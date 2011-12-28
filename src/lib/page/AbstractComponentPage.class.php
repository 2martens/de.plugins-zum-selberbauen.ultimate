<?php
namespace ultimate\page;
use ultimate\system\UltimateCMS;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\page\AbstractPage;

/**
 * Every ComponentPage should extend this class.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.cms
 * @subpackage page
 * @category Ultimate CMS
 */
abstract class AbstractComponentPage extends AbstractPage {
    
    /**
     * @see wcf\page\AbstractPage::$useTemplate
     */
    public $useTemplate = false;
    
    /**
     * Contains the name of the attached content.
     * @var string
     */
    protected $contentName = '';
    
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
    public function __construct($contentName) {
        $this->contentName = StringUtil::trim($contentName);
        parent::__construct();
        //returns the output
        return $this->output();
    }
    
    /**
     * @see wcf\page\AbstractPage::readData()
     */
    public function readData() {
        parent::readData();
        
        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add('contentTitle = ?', array($this->contentName));
        $sql = 'SELECT contentID, contentTitle, contentText
        		FROM ultimate'.ULTIMATE_N.'_'.$this->databaseTable.'
        		'.$conditions.'
        		LIMIT 1';
        $statement = UltimateCMS::getDB()->prepareStatement($sql);
        $statement->execute($conditions->getParameters());
        $row = $statement->fetchArray();
        $this->displayContent = array(
            'contentID' => intval($row['contentID']),
            'contentTitle' => StringUtil::trim($row['contentTitle']),
            'contentText' => StringUtil::trim($row['contentText'])
        );
    }
    
    /**
     * @see wcf\page\AbstractPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        UltimateCMS::getTPL()->assign(array(
            'contentID' => $this->displayContent['contentID'],
            'contentTitle' => $this->displayContent['contentTitle'],
            'contentText' => $this->displayContent['contentText']
        ));
    }
    
    /**
     * @see wcf\page\AbstractPage::show()
     */
    public function show() {
        parent::show();
        $this->output = UltimateCMS::getTPL()->fetch($this->templateName);
    }
}
