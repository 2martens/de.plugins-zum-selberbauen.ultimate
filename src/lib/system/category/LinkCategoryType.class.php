<?php
namespace ultimate\system\category;
use wcf\system\category\AbstractCategoryType;

/**
 * Manages the link category type.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.category
 * @category	Ultimate CMS
 */
class LinkCategoryType extends AbstractCategoryType {
	/**
	 * @see \wcf\system\category\AbstractCategoryType::$forceDescription
	 */
	protected $forceDescription = false;
	
	/**
	 * @see \wcf\system\category\AbstractCategoryType::$hasDescription
	 */
	protected $hasDescription = false;
	
	/**
	 * language category which contains the language variables of i18n values
	 * @var	string
	 */
	protected $i18nLangVarCategory = 'ultimate.link';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.category.AbstractCategoryType.html#$langVarPrefix
	 */
	protected $langVarPrefix = 'wcf.acp.ultimate.linkCategory';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.category.AbstractCategoryType.html#$permissionPrefix
	 */
	protected $permissionPrefix = 'admin.content.ultimate';
	
	/**
	 * @var string[]
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.category.AbstractCategoryType.html#$objectTypes
	 */
	protected $objectTypes = array(
		'com.woltlab.wcf.clipboardItem' => 'de.plugins-zum-selberbauen.ultimate.link'
	);
}
