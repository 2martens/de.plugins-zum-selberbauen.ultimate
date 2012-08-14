<?php
namespace ultimate\acp\page;
use wcf\acp\page\AbstractCategoryListPage;

/**
 * Shows the UltimateLinkCategoryList page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
class UltimateLinkCategoryListPage extends AbstractCategoryListPage {
	/**
	 * @see \wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.link.category.list';
	
	/**
	 * @see \wcf\acp\form\AbstractCategoryAddForm::$objectTypeName
	 */
	public $objectTypeName = 'de.plugins-zum-selberbauen.ultimate.linkCategory';
}
