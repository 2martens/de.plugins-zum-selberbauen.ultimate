<?php
namespace ultimate\data\template;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of templates.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimateCore
 * @subpackage	data.ultimate.template
 * @category	Ultimate CMS
 */
class TemplateList extends DatabaseObjectList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$className
	 */
	public $className = '\ultimate\data\template\Template';
}
