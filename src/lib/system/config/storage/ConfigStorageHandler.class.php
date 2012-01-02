<?php
namespace ultimate\system\config\storage;
use ultimate\data\config\Config;
use wcf\system\SingletonFactory;

/**
 * Handles the ConfigStorage.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.config.storage
 * @category Ultimate CMS
 */
class ConfigStorageHandler extends SingletonFactory {
    
    /**
     * Contains a ConfigStorage object.
     * @var ConfigStorage
     */
    protected $configStorage = null;
    
    /**
     * Unserialize the configStorage object if configID is given, otherwise creates new object.
     *
     * @see \wcf\system\SingletonFactory::init()
     */
    protected function init($configID = 0) {
        parent::init();
        $configID = intval($configID);
        if (!$configID) $this->configStorage = new ConfigStorage();
        else {
            $config = new Config($configID);
            $this->configStorage = unserialize($config->storage);
        }
    }
    
    /**
     * Initializes the ConfigStorage object with given configID.
     *
     * @param int $configID
     */
    public function addConfigID($configID) {
        $this->init($configID);
    }
    
    /**
     * Returns the ConfigStorage object.
     *
     * @return ConfigStorage
     */
    public function getConfigStorage() {
        return $this->configStorage;
    }
    
}
