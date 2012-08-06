<?php
namespace ultimate\system\cache\builder;
use ultimate\data\template\TemplateList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the templates.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimateCore
 * @subpackage system.cache.builder
 * @category Ultimate CMS
 */
class UltimateTemplateCacheBuilder implements ICacheBuilder {
    
    /**
     * @see \wcf\system\cache\builder\ICacheBuilder::getData()
     */
    public function getData(array $cacheResource) {
        $data = array(
            'templates' => array(),
            'templateIDs' => array()
        );
        
        $templateList = new TemplateList();
        
        $templateList->readObjects();
        $templates = $templateList->getObjects();
        if (!count($templates)) return $data;
        
        $data['templates'] = $templates;
        $data['templateIDs'] = array_keys($templates);
        
        return $data;
    }
}
