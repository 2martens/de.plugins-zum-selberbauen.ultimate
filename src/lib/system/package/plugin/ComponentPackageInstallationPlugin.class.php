<?php
namespace ultimate\system\package\plugin;
use wcf\system\exception\SystemException;
use wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin;
use wcf\system\WCF;

/**
 * This PIP installes, updates or deletes components.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.package.plugin
 * @category Ultimate CMS
 */
class ComponentPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
    /**
     * @see \wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::$className
     */
    public $className = '\ultimate\data\component\ComponentEditor';
    
    /**
     * @see	\wcf\system\package\plugin\AbstractPackageInstallationPlugin::$tableName
     */
    public $tableName = 'component';
    
    /**
     * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::$tagName
     */
    public $tagName = 'component';
    
    /**
     * @see \wcf\system\package\plugin\AbstractPackageInstallationPlugin::__construct()
     */
    public function __construct(PackageInstallationDispatcher $installation, $instruction = array()) {
        parent::__construct();
        
        // We're installing the CMS itself.
        if (!defined('ULTIMATE_N')) {
            $packageID = $this->installation->getPackageID();
            $sql = 'SELECT packageDir
            		FROM wcf'.WCF_N.'_package
            		WHERE packageID = ?';
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute(array($packageID));
            $row = $statement->fetchArray();
            $packageDir = $row['packageDir'];
            require_once($packageDir.'config.inc.php');
        }
    }
    
    /**
     * @see \wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::handleDelete()
     */
    protected function handleDelete(array $items) {
        $sql = 'DELETE FROM ultimate'.ULTIMATE_N.'_'.$this->tableName.'
        		WHERE className = ?';
        $statement = WCF::getDB()->prepareStatement($sql);
        
        WCF::getDB()->beginTransaction();
        foreach ($items as $component) {
            $statement->executeUnbuffered(array($component['elements']['classname']));
        }
        WCF::getDB()->commitTransaction();
    }
    
    /**
     * @see \wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::prepareImport()
     */
    protected function prepareImport(array $data) {
        $mapped = array(
            'className' => $data['elements']['classname'],
            'title' => $data['elements']['title']
        );
        return $mapped;
    }
    
    /**
     * @see \wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::findExistingItem()
     */
    protected function findExistingItem(array $data) {
        //You can't update a component with an xml file.
        //To update the component, simply update its class file.
        return null;
    }
    
    /**
     * @see \wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::validateImport()
     * @throws \wcf\system\exception\SystemException
     */
    protected function validateImport(array $data) {
        if (!isset($data['className']) || !isset($data['title'])) {
            throw new SystemException('The array given doesn\'t fit the form needed by the object editor class.');
        }
        
        if (empty($data['className'])) {
            throw new SystemException('The given class name can\'t be empty.');
        }
        
        if (empty($data['title'])) {
            throw new SystemException('The given title can\'t be empty.');
        }
        
        if (!strpos($data['className'], '\\')) {
            throw new SystemException('The class name has to contain at least one namespace.');
        }
        
        $namespaces = explode('\\', $data['className']);
        if ($namespaces[0] != 'ultimate') {
            throw new SystemException('The component class has to lie in the ultimate\\* namespace.');
        }
    }
}
