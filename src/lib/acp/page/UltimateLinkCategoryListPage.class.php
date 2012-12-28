<?php
/**
 * Contains the UltimateLinkCategoryList page.
 * 
 * LICENSE:
 * This file is part of the Ultimate CMS.
 *
 * The Ultimate CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * The Ultimate CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
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
