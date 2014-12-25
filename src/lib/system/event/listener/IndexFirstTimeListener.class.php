<?php
/**
 * Contains the IndexFirstTimeListener class.
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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
namespace ultimate\system\event\listener;
use wcf\data\category\CategoryAction;
use wcf\system\cache\builder\ObjectTypeCacheBuilder;
use wcf\system\category\CategoryHandler;
use wcf\system\event\IParameterizedEventListener;
use wcf\system\io\File;

/**
 * Executes some functions on the first start of the ACP after installation.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
class IndexFirstTimeListener implements IParameterizedEventListener {
	/**
	 * The name of the config file.
	 * @var	string
	 */
	const CONFIG_FILE = 'config.inc.php';
	
	/**
	 * Executes this listener.
	 * 
	 * @param	object	$eventObj
	 * @param	string	$className
	 * @param	string	$eventName
	 */
	public function execute($eventObj, $className, $eventName) {
		// adds default link category
		require_once(ULTIMATE_DIR.'acp/'.self::CONFIG_FILE);
		if (!$initiatedDefaultLinkCategory) {
			$success = $this->createDefaultLinkCategory();
			if ($success) {
				$this->updateConfigFile();
			}
		}
	}
	
	/**
	 * Creates the default link category.
	 * 
	 * @return	boolean	true on success, false otherwise
	 */
	protected function createDefaultLinkCategory() {
		ObjectTypeCacheBuilder::getInstance()->reset();
		$objectType = CategoryHandler::getInstance()->getObjectTypeByName('de.plugins-zum-selberbauen.ultimate.linkCategory');
		if ($objectType === null) return false;
		if (!isset($objectType->objectTypeID)) return false;
		
		$parameters = array(
			'data' => array(
				'objectTypeID' => $objectType->objectTypeID,
				'parentCategoryID' => 0,
				'showOrder' => 0,
				'title' => 'ultimate.link.category.title.category1',
				'time' => TIME_NOW
			)
		);
		$action = new CategoryAction(array(), 'create', $parameters);
		$action->executeAction();
		
		return true;
	}
	
	/**
	 * Rewrites the config file.
	 */
	protected function updateConfigFile() {
		$file = new File(ULTIMATE_DIR.'acp/'.self::CONFIG_FILE);
		$content = '<?php'."\n";
		$content .= '/*'."\n".' * This file was automatically generated. To not modify it.'."\n".' */'."\n\n";
		$content .= '$initiatedDefaultLinkCategory = true;'."\n";
		$file->write($content);
		$file->close();
	}
}
