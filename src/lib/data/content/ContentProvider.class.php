<?php
namespace ultimate\data\content;
use wcf\data\object\type\AbstractObjectTypeProvider;

/**
 * Provides contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class ContentProvider extends AbstractObjectTypeProvider {
	/**
	 * @see	\wcf\data\object\type\AbstractObjectTypeProvider::$className
	 */
	public $className = '\ultimate\data\content\Content';
	
	/**
	 * @see	\wcf\data\object\type\AbstractObjectTypeProvider::$listClassName
	 */
	public $listClassName = '\ultimate\data\content\ContentList';
}
