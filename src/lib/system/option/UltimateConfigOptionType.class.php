<?php
namespace ultimate\system\option;
use ultimate\data\config\ConfigList;

use wcf\data\option\Option;
use wcf\system\option\AbstractOptionType;
use wcf\system\WCF;

/**
 * UltimateConfigOptionType is an implementation of IOptionType for Ultimate Config select lists.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.option
 * @category Ultimate CMS
 */
class UltimateConfigOptionType extends AbstractOptionType {
    
    /**
     * name of the template that contains the form element of this option type
     * @var string
     */
    public $templateName = 'ultimateConfigOptionType';
    
    /**
     * @see \wcf\system\option\IOptionType::getFormElement()
     */
    public function getFormElement(Option $option, $value) {
        $configList = new ConfigList();
        $configList->readObjects();
        $objects = $configList->getObjects();
        $configs = array();
        
        foreach ($objects as $config) {
            $configs[$config->configID] = $config->configTitle;
        }
        
        WCF::getTPL()->assign(array(
			'option' => $option,
			'selectOptions' => $configs,
			'value' => $value
		));
		return WCF::getTPL()->fetch($this->templateName);
    }
}
