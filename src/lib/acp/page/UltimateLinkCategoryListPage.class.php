<?php
namespace ultimate\acp\page;
use wcf\acp\page\AbstractCategoryListPage;
use wcf\system\WCF;

/**
 * Shows the UltimateLinkCategoryList page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
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
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.AbstractPage.html#$templateName
	 */
	public $templateName = 'ultimateLinkCategoryList';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		require(ULTIMATE_DIR.'acp/config.inc.php');
		WCF::getTPL()->assign('defaultLinkCategoryID', $categoryID);
	}
}
