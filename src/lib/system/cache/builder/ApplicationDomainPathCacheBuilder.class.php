<?php
namespace ultimate\system\cache\builder;
use ultimate\system\UltimateCore;
use wcf\system\cache\builder\ICacheBuilder;
use wcf\util\StringUtil;

/**
 * Caches the domain paths of the applications.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.cache.builder
 * @category Ultimate CMS
 */
class ApplicationDomainPathCacheBuilder implements ICacheBuilder {
    /**
     * Contains the database table name of the application table.
     * @var string
     */
    protected $databaseTable = 'application';
    
    /**
     * @see wcf\system\cache\builder\ICacheBuilder::getData()
     */
    public function getData(array $cacheResource) {
        
        $data = array(
        	'paths' => array()
        );
        
        $sql = 'SELECT packageID, domainPath
        		FROM wcf'.WCF_N.'_application';
        $statement = UltimateCore::getDB()->prepareStatement($sql);
        $statement->execute();
        $paths = array();
        while ($row = $statement->fetchArray()) {
            $paths[intval($row['packageID'])] = StringUtil::trim($row['domainPath']);
        }
        $data['paths'] = $paths;
        return $data;
    }
}
