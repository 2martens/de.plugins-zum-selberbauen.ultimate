<?php
namespace ultimate\data\link;
use ultimate\data\AbstractUltimateDatabaseObject;

/**
 * Represents a link.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.link
 * @category	Ultimate CMS
 */
class Link extends AbstractUltimateDatabaseObject {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableName
	 */
	protected static $databaseTableName = 'link';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'linkID';
	
	/**
	 * Returns the name of this link.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->get($this->linkName);
	}
	
	/**
	 * @see \wcf\data\DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		$data['linkID'] = intval($data['linkID']);
		parent::handleData($data);
	}
}
