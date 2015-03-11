<?php
/**
 * 
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
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.importer
 * @category	Ultimate CMS
 */
namespace ultimate\system\importer;
use ultimate\data\category\Category;
use ultimate\data\category\CategoryAction;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;

/**
 * Imports categories.
 *
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.importer
 * @category	Ultimate CMS
 */
class CategoryImporter extends AbstractImporter {
	/**
	 * @see \wcf\system\importer\AbstractImporter::$className
	 */
	protected $className = 'ultimate\data\category\Category';
	
	/**
	 * @see \wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = array()) {
		// check old id
		if (is_numeric($oldID)) {
			$category = new Category($oldID);
			if (!$category->categoryID) $data['categoryID'] = $oldID;
		}
		
		$action = new CategoryAction(array(), 'create', array(
			'data' => $data
		));
		$returnValues = $action->executeAction();
		$category = $returnValues['returnValues'];
		
		ImportHandler::getInstance()->saveNewID('de.plugins-zum-selberbauen.ultimate.category', $oldID, $category->__get('categoryID'));
		
		return $category->__get('categoryID');
	}
}
