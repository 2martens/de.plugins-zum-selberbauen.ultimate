<?php
/**
 * Contains the Page importer class.
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
 * @subpackage	system.importer
 * @category	Ultimate CMS
 */
namespace ultimate\system\importer;
use ultimate\data\page\Page;
use ultimate\data\page\PageAction;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;

/**
 * Imports pages.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.importer
 * @category	Ultimate CMS
 */
class PageImporter extends AbstractImporter {
	/**
	 * @see \wcf\system\importer\AbstractImporter::$className
	 */
	protected $className = 'ultimate\data\page\Page';
	
	/**
	 * @see \wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = array()) {
		if ($data['pageParent'] !== null) $data['pageParent'] = ImportHandler::getInstance()->getNewID('de.plugins-zum-selberbauen.ultimate.page', $data['pageParent']);
		
		// check old id
		if (is_numeric($oldID)) {
			$page = new Page($oldID);
			if (!$page->pageID) $data['pageID'] = $oldID;
		}
		
		$action = new PageAction(array(), 'create', array(
			'data' => $data
		));
		$returnValues = $action->executeAction();
		$newID = $returnValues['returnValues']->pageID;
		
		ImportHandler::getInstance()->saveNewID('de.plugins-zum-selberbauen.ultimate.page', $oldID, $newID);
		
		return $newID;
	}
}
