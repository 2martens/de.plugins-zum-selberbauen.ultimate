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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
namespace ultimate\system\event\listener;
use wcf\data\category\CategoryAction;
use wcf\system\cache\CacheHandler;
use wcf\system\event\IEventListener;
use wcf\system\io\File;

/**
 * Executes some functions on the first start of the ACP after installation.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
class IndexFirstTimeListener implements IEventListener {
	/**
	 * Contains the name of the config file.
	 * @var	string
	 */
	const CONFIG_FILE = 'config.inc.php';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.event.IEventListener.html#execute
	 */
	public function execute($eventObj, $className, $eventName) {
		// adds default link category
		require_once(ULTIMATE_DIR.'acp/'.self::CONFIG_FILE);
		if (!$initiatedDefaultLinkCategory) {
			$this->createDefaultLinkCategory();
			$this->updateConfigFile();
		}
	}
	
	/**
	 * Creates the default link category.
	 */
	protected function createDefaultLinkCategory() {
		CacheHandler::getInstance()->clear(WCF_DIR.'cache/', 'cache.objectType-*.php');
		$objectType = CategoryHandler::getInstance()->getObjectTypeByName('de.plugins-zum-selberbauen.ultimate.linkCategory');
		// until it's working, we have to do this
		if ($objectType === null) return;
		if (!isset($objectType->objectTypeID)) exit;
		
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
