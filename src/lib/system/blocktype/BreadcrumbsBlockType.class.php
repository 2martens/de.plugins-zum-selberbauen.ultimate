<?php
namespace ultimate\system\blocktype;
use wcf\system\WCF;

/**
 * Represents the breadcrumbs block type.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
class BreadcrumbsBlockType extends AbstractBlockType {
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$templateName
	 */
	protected $templateName = 'breadcrumbsBlockType';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$useTemplate
	 */
	protected $useTemplate = false;
} 