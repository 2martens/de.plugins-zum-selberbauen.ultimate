<?php
namespace ultimate\data\block;
use ultimate\data\blocktype\BlockType;
use ultimate\data\AbstractUltimateDatabaseObject;

/**
 * Represents a block entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimateCore
 * @subpackage	data.ultimate.block
 * @category	Ultimate CMS
 */
class Block extends AbstractUltimateDatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'block';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'blockID';
	
	/**
	 * @see \wcf\data\DatabaseObject::__get()
	 */
	public function __get($name) {
		$value = parent::__get($name);
		// makes additional data adressable like normal variables
		if ($value === null) {
			if (isset($this->data['additionalData'][$name])) {
				return $this->data['additionalData'][$name];
			}
		}
		return $value;
	}
	
	/**
	 * @see	\wcf\data\DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		$data['parameters'] = unserialize($data['parameters']);
		$data['blockType'] = new BlockType($data['blockTypeID']);
		$data['additionalData'] = unserialize($data['additionalData']);
		parent::handleData($data);
	}
}
