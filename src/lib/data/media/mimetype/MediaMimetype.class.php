<?php
namespace ultimate\data\media\mimetype;
use ultimate\data\AbstractUltimateDatabaseObject;

/**
 * Represents a MIME type.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.media.mimetype
 * @category	Ultimate CMS
 */
class MediaMimetype extends AbstractUltimateDatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'media_mimetype';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'mimeTypeID';
	
	/**
	 * @see \wcf\data\DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		$data['mimeTypeID'] = intval($data['mimeTypeID']);
		parent::handleData($data);
	}
}
