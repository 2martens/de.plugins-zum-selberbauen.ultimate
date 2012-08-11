<?php
namespace ultimate\data;
use wcf\data\ProcessibleDatabaseObject;

/**
 * Every Ultimate processible data class should implement this.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data
 * @category	Ultimate CMS
 */
abstract class AbstractUltimateProcessibleDatabaseObject extends ProcessibleDatabaseObject {
	/**
	 * @see	\wcf\data\IStorableObject::getDatabaseTableName()
	 */
	public static function getDatabaseTableName() {
		return 'ultimate'.ULTIMATE_N.'_'.static::$databaseTableName;
	}
}
