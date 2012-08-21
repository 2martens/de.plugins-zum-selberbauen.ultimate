<?php
namespace ultimate\data\blocktype;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\system\WCF;

/**
 * Represents a blockType entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimateCore
 * @subpackage	data.ultimate.blockType
 * @category	Ultimate CMS
 */
class BlockType extends AbstractUltimateDatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'blocktype';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'blockTypeID';
	
	/**
	 * Returns the title of this blockType.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->get($this->blockTypeName);
	}
	
	/**
	 * @see \wcf\data\DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		$data['cssIdentifier'] = strtolower($data['blockTypeName']);
		parent::handleData($data);
	}
}
