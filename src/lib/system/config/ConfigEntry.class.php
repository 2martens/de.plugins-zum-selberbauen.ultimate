<?php
namespace ultimate\system\config;
use wcf\util\StringUtil;

use ultimate\data\component\Component;

/**
 * Represents a config entry.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.config
 * @category Ultimate CMS
 */
class ConfigEntry implements \Serializable {
    
    /**
     * Contains the component id.
     * @var int
     */
    protected $componentID = 0;
    
    /**
     * Contains the content id.
     * @var int
     */
    protected $contentID = 0;
    
    /**
     * Contains the category id.
     * @var int
     */
    protected $categoryID = 0;
    
    /**
     * Contains the output of the entry.
     * @var string
     */
    protected $output = '';
    
    /**
     * Creates a new ConfigEntry object.
     *
     * @param int $componentID
     * @param int $contentID
     * @param int $categoryID
     */
    public function __construct($componentID, $contentID, $categoryID = 0) {
        $this->componentID = intval($componentID);
        $this->contentID = intval($contentID);
        $this->categoryID = intval($categoryID);
        $this->generateOutput();
    }
    
    /**
     * Changes the content id of this entry.
     *
     * @param int $newID
     */
    public function changeContentID($newID) {
        $this->contentID = intval($newID);
        $this->generateOutput();
    }
    
    /**
     * Changes the component id of this entry.
     *
     * @param int $newID
     */
    public function changeComponentID($newID) {
        $this->componentID = intval($newID);
        $this->generateOutput();
    }
    
    /**
     * Changes the category id of this entry.
     *
     * @param int $newID
     */
    public function changeCategoryID($newID) {
        $this->categoryID = intval($newID);
        $this->generateOutput();
    }
    
    /**
     * Updates the current saved content.
     */
    public function refreshContent() {
        $this->generateOutput();
    }
    
    /**
     * Generates the output.
     */
    protected function generateOutput() {
        $component = new Component($this->componentID);
        $obj = new $component->className($this->contentID, $this->categoryID);
        $this->output = $obj->getOutput();
    }
    
    /**
     * Returns the content of the entry.
     */
    public function getContent() {
        return $this->output;
    }
    
    /**
     * Returns the component id.
     *
     * @return int
     */
    public function getComponentID() {
        return $this->componentID;
    }
    
    /**
     * Returns the content id.
     *
     * @return int
     */
    public function getContentID() {
        return $this->contentID;
    }
    
	/**
     * Returns the category id.
     *
     * @return int
     */
    public function getCategoryID() {
        return $this->categoryID;
    }
    
    /**
     * Returns a random id of this entry.
     */
    public function getRandomID() {
        $salt = StringUtil::getRandomID();
        $value = $this->componentID.$this->contentID.$this->categoryID;
        return StringUtil::getDoubleSaltedHash($value, $salt);
    }
    
    /**
     * @see \Serializable::serialize()
     */
    public function serialize() {
        $data = array(
            'compID' => $this->componentID,
            'contentID' => $this->contentID,
            'categoryID' => $this->categoryID,
            'output' => base64_encode($this->output)
        );
        return serialize($data);
    }
    
    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized) {
        $data = unserialize($serialized);
        $this->componentID = $data['compID'];
        $this->contentID = $data['contentID'];
        $this->categoryID = $data['categoryID'];
        $this->output = base64_decode($data['output']);
        $this->refreshContent();
    }
    
}
