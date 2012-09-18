<?php
namespace ultimate\data\block;
use ultimate\data\blocktype\BlockType;
use ultimate\data\AbstractUltimateDatabaseObject;

/**
 * Represents a block entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.ultimate.block
 * @category	Ultimate CMS
 */
class Block extends AbstractUltimateDatabaseObject {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableName
	 */
	protected static $databaseTableName = 'block';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'blockID';
	
	/**
	 * @see http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#__get
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
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#handleData
	 */
	protected function handleData($data) {
		$data['parameters'] = unserialize($data['parameters']);
		$data['blockType'] = new BlockType($data['blockTypeID']);
		$data['additionalData'] = unserialize($data['additionalData']);
		parent::handleData($data);
	}
}
