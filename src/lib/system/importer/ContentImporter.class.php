<?php
/**
 * Contains the Content importer class.
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
use ultimate\data\content\Content;
use ultimate\data\content\ContentAction;
use wcf\system\importer\ImportHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\tagging\TagEngine;

/**
 * Imports contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.importer
 * @category	Ultimate CMS
 */
class ContentImporter extends AbstractImporter {
	/**
	 * @see \wcf\system\importer\AbstractImporter::$className
	 */
	protected $className = 'ultimate\data\content\Content';
	
	/**
	 * @see \wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = array()) {
		$data['authorID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $data['authorID']);
		
		// check old id
		if (is_numeric($oldID)) {
			$content = new Content($oldID);
			if (!$content->contentID) $data['contentID'] = $oldID;
		}
		
		$action = new ContentAction(array(), 'create', array(
			'data' => $data
		));
		$returnValues = $action->executeAction();
		$content = $returnValues['returnValues'];
		
		// save tags
		if (!empty($additionalData['tags'])) {
			$languages = LanguageFactory::getInstance()->getLanguages();
			foreach ($languages as $languageID => $language) {
				TagEngine::getInstance()->addObjectTags('de.plugins-zum-selberbauen.ultimate.content', $content->__get('contentID'), $additionalData['tags'], $languageID);
			}
		}
		
		ImportHandler::getInstance()->saveNewID('de.plugins-zum-selberbauen.ultimate.content', $oldID, $content->__get('contentID'));
		
		return $content->__get('contentID');
	}
}
