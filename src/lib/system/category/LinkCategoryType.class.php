<?php
namespace ultimate\system\category;
use wcf\system\category\AbstractCategoryType;

/**
 * Manages the link category type.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
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
	protected $i18nLangVarCategory = 'wcf.acp.ultimate';
	
	/**
	 * @see	\wcf\system\category\AbstractCategoryType::$langVarPrefix
	 */
	protected $langVarPrefix = 'wcf.acp.ultimate.link';
	
	/**
	 * @see	\wcf\system\category\AbstractCategoryType::$permissionPrefix
	 */
	protected $permissionPrefix = 'admin.content.ultimate';
	
	/**
	 * @var string[]
	 * @see	\wcf\system\category\AbstractCategoryType::$objectTypes
	 */
	protected $objectTypes = array(
		'com.woltlab.wcf.clipboardItem' => 'de.plugins-zum-selberbauen.ultimate.link'
	);
}
