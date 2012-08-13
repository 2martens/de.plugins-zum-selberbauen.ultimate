<?php
namespace ultimate\data\blocktype;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\CacheHandler;

/**
 * Provides functions to edit blockTypes.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimateCore
 * @subpackage	data.ultimate.blockType
 * @category	Ultimate CMS
 */
class BlockTypeEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = '\ultimate\data\blocktype\BlockType';
	
	/**
	 * @see	\wcf\data\IEditableCachedObject::resetCache()
	 */
	public function resetCache() {
		CacheHandler::getInstance()->clear(ULTIMATE_DIR.'cache/', 'cache.blocktype.php');
	}
}
