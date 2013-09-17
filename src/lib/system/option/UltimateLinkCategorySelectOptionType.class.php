<?php
/**
 * Contains the UltimateLinkCategorySelectOptionType class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.option
 * @category	Ultimate CMS
 */
namespace ultimate\system\option;
use ultimate\system\cache\builder\LinkCategoryCacheBuilder;
use wcf\data\option\Option;
use wcf\system\exception\UserInputException;
use wcf\system\option\AbstractOptionType;
use wcf\system\WCF;

/**
 * Option type implementation for link category selection.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.option
 * @category	Ultimate CMS
 */
class UltimateLinkCategorySelectOptionType extends AbstractOptionType {
	
	/**
	 * (non-PHPdoc)
	 * @see \wcf\system\option\IOptionType::getFormElement()
	 */
	public function getFormElement(Option $option, $value) {
		$linkCategories = LinkCategoryCacheBuilder::getInstance()->getData(array(), 'linkCategories');
		
		WCF::getTPL()->assign(array(
			'linkCategories' => $linkCategories,
			'option' => $option,
			'value' => $value
		));
		return WCF::getTPL()->fetch('linkCategorySelectOptionType', 'ultimate');
	}
	
	/**
	 * Validates the input for the given option of this option type.
	 * 
	 * @param	\wcf\data\option\Option	$option
	 * @param	string					$newValue
	 * 
	 * @throws	\wcf\system\exception\UserInputException	if the validation fails
	 */
	public function validate(Option $option, $newValue) {
		if (!empty($newValue)) {
			$categoryIDs = LinkCategoryCacheBuilder::getInstance()->getData(array(), 'linkCategoryIDs');
			if (!in_array($newValue, $categoryIDs)) {
				throw new UserInputException($option->__get('optionName'), 'validationFailed');
			}
		}
	}
	
	/**
	 * Returns the value of the given option of this option type which will be saved in the database.
	 * 
	 * @param	\wcf\data\option\Option	$option
	 * @param	string					$newValue
	 * 
	 * @return	integer
	 */
	public function getData(Option $option, $newValue) {
		return intval($newValue);
	}
}
