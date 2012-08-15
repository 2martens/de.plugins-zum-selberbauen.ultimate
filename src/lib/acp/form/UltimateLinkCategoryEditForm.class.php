<?php
namespace ultimate\acp\form;
use wcf\acp\form\AbstractCategoryEditForm;

/**
 * Shows the UltimateLinkCategoryEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UlimateLinkCategoryEditForm extends AbstractCategoryEditForm {
	/**
	 * @see \wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.link';
	
	/**
	 * @see \wcf\acp\form\AbstractCategoryAddForm::$objectTypeName
	 */
	public $objectTypeName = 'de.plugins-zum-selberbauen.ultimate.linkCategory';
}
