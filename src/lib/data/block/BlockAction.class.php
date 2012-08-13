<?php
namespace ultimate\data\block;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes block-related actions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimateCore
 * @subpackage	data.ultimate.block
 * @category	Ultimate CMS
 */
class BlockAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	public $className = '\ultimate\data\block\BlockEditor';
}
