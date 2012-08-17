<?php
namespace ultimate\data\media\mimetype;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes MIME type related functions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.media.mimetype
 * @category	Ultimate CMS
 */
class MediaMimetypeAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	public $className = '\ultimate\data\media\mimetype\MimetypeEditor';
}
