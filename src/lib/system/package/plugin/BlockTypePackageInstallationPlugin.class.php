<?php
namespace ultimate\system\package\plugin;
use wcf\system\event\EventHandler;
use wcf\system\exception\SystemException;
use wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin;
use wcf\system\WCF;
use wcf\util\ClassUtil;

/**
 * This PIP installes, updates or deletes blockTypes.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimateCore
 * @subpackage system.package.plugin
 * @category Ultimate CMS
 */
class BlockTypePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
    /**
     * @see \wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::$className
     */
    public $className = '\ultimate\data\blocktype\BlockTypeEditor';
    
    /**
     * @see	\wcf\system\package\plugin\AbstractPackageInstallationPlugin::$tableName
     */
    public $tableName = 'blocktype';
    
    /**
     * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::$tagName
     */
    public $tagName = 'blockType';
    
    /**
     * @see \wcf\system\package\plugin\IPackageInstallationPlugin::hasUninstall()
     */
    public function hasUninstall() {
        // call hasUninstall event
        EventHandler::getInstance()->fireAction($this, 'hasUninstall');
        
        $sql = "SELECT	COUNT(*) AS count
			FROM	ultimate".ULTIMATE_N."_".$this->tableName."
			WHERE	packageID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute(array($this->installation->getPackageID()));
        $installationCount = $statement->fetchArray();
        return $installationCount['count'];
    }
    
    /**
     * @see	wcf\system\package\plugin\IPackageInstallationPlugin::uninstall()
     */
    public function uninstall() {
        // call uninstall event
        EventHandler::getInstance()->fireAction($this, 'uninstall');
    
        $sql = "DELETE FROM	ultimate".ULTIMATE_N."_".$this->tableName."
			WHERE		packageID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute(array($this->installation->getPackageID()));
    }
    
    /**
     * @see \wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::handleDelete()
     */
    protected function handleDelete(array $items) {
        $sql = 'DELETE FROM ultimate'.ULTIMATE_N.'_'.$this->tableName.'
        		WHERE  packageID     = ?
                AND    blockTypeName = ?';
        $statement = WCF::getDB()->prepareStatement($sql);
        
        WCF::getDB()->beginTransaction();
        foreach ($items as $blockType) {
            $statement->executeUnbuffered(array(
                $this->installation->getPackageID(),
                $blockType['attributes']['name']
            ));
        }
        WCF::getDB()->commitTransaction();
    }
    
    /**
     * @see \wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::prepareImport()
     */
    protected function prepareImport(array $data) {
        $mapped = array(
            'packageID' => $this->installation->getPackageID(),
            'blockTypeName' => $data['attributes']['name'],
            'bockTypeClassName' => $data['elements']['className']
        );
        return $mapped;
    }
    
    /**
     * @see \wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::findExistingItem()
     * @return null
     */
    protected function findExistingItem(array $data) {
        // You can't update a blockType with an xml file.
        // To update the blockType, simply update its class file.
        return null;
    }
    
    /**
     * @see \wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::validateImport()
     * @throws \wcf\system\exception\SystemException
     */
    protected function validateImport(array $data) {
        if (!isset($data['blockTypeName']) || !isset($data['blockTypeClassName'])) {
            throw new SystemException('The array given doesn\'t fit the form needed by the object editor class.');
        }
        
        if (empty($data['blockTypeName'])) {
            throw new SystemException('The given name can\'t be empty.');
        }
        
        if (empty($data['blockTypeClassName'])) {
            throw new SystemException('The given class name can\'t be empty.');
        }
        
        if (!strpos($data['blockTypeClassName'], '\\')) {
            throw new SystemException('The class name has to contain at least one namespace.');
        }
        
        if (!ClassUtil::isInstanceOf($data['blockTypeClassName'], '\ultimate\system\blocktype\IBlockType')) {
            throw new SystemException('The class belonging to the class name has to implement IBlockType.');
        }
    }
    
    /**
     * @see \wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::getShowOrder()
     */
    protected function getShowOrder($showOrder, $parentName = null, $columnName = null, $tableNameExtension = '') {
        if ($showOrder === null) {
            // get greatest showOrder value
            $conditions = new PreparedStatementConditionBuilder();
            if ($columnName !== null) $conditions->add($columnName." = ?", array($parentName));
            	
            $sql = "SELECT	MAX(showOrder) AS showOrder
			  	FROM	ultimate".ULTIMATE_N."_".$this->tableName.$tableNameExtension."
				".$conditions;
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute($conditions->getParameters());
            $maxShowOrder = $statement->fetchArray();
            return (!$maxShowOrder) ? 1 : ($maxShowOrder['showOrder'] + 1);
        }
        else {
            // increase all showOrder values which are >= $showOrder
            $sql = "UPDATE	ultimate".ULTIMATE_N."_".$this->tableName.$tableNameExtension."
				SET	showOrder = showOrder + 1
				WHERE	showOrder >= ?
				".($columnName !== null ? "AND ".$columnName." = ?" : "");
            $statement = WCF::getDB()->prepareStatement($sql);
            	
            $data = array($showOrder);
            if ($columnName !== null) $data[] = $parentName;
            	
            $statement->execute($data);
            	
            // return the wanted showOrder level
            return $showOrder;
        }
    }
}
