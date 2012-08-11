<?php
namespace ultimate\data\blocktype;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes blockType-related actions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimateCore
 * @subpackage	data.ultimate.blockType
 * @category	Ultimate CMS
 */
class BlockTypeAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	public $className = '\ultimate\data\blocktype\BlockTypeEditor';
}
