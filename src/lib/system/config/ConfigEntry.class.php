<?php
namespace ultimate\system\config;
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
class ConfigEntry {
    
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
     * Contains the output of the entry.
     * @var string
     */
    protected $output = '';
    
    /**
     * Creates a new ConfigEntry object.
     *
     * @param int $componentID
     * @param int $contentID
     */
    public function __construct($componentID, $contentID) {
        $this->componentID = intval($componentID);
        $this->contentID = intval($contentID);
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
     * Generates the output.
     */
    protected function generateOutput() {
        $component = new Component($this->componentID);
        $this->output = new $component->className($this->contentID);
    }
    
    /**
     * Returns the content of the entry.
     */
    public function getContent() {
        return $this->output;
    }
    
}
