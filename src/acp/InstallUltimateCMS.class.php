<?php
/**
 * Contains the installation script.
 * 
 * LICENSE:
 * This file is part of the Ultimate CMS.
 *
 * Foobar is free software: you can redistribute it and/or modify
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @category	Ultimate CMS
 */
namespace ultimate\acp;
use ultimate\data\blocktype\BlockTypeAction;
use wcf\data\page\menu\item\PageMenuItemAction;
use wcf\system\cache\builder\EventListenerCacheBuilder;
use wcf\system\event\EventHandler;
use wcf\system\io\File;
use wcf\system\Regex;

/**
 * Is called during installation of Ultimate CMS.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @category	Ultimate CMS
 */
final class InstallUltimateCMS {
	protected $packageID = 0;
	
	/**
	 * Creates a new InstallUltimateCMS object.
	 */
	public function __construct() {
		$this->install();
	}
	
	/**
	 * Installs important things.
	 */
	protected function install() {
		require_once(dirname(dirname(__FILE__)).'/config.inc.php');
		// workaround for standalone installation (PACKAGE_ID is 0)
		preg_match('/packageID (\d+)/', file_get_contents(dirname(dirname(__FILE__)).'/config.inc.php'), $matches);
		$this->packageID = $matches[1];
		$this->addDefaultBlockTypes();
		$this->deactivateMenuItem();
	}
	
	/**
	 * Adds the default block types.
	 */
	protected function addDefaultBlockTypes() {
		// insert default block types
		$parameters = array(
			'data' => array(
				'packageID' => $this->packageID,
				'blockTypeName' => 'ultimate.blocktype.content',
				'blockTypeClassName' => 'ultimate\system\blocktype\ContentBlockType'
			)
		);
		// workaround for installation
		require_once(ULTIMATE_DIR.'lib/data/IUltimateData.class.php');
		require_once(ULTIMATE_DIR.'lib/data/AbstractUltimateDatabaseObject.class.php');
		require_once(ULTIMATE_DIR.'lib/data/blocktype/BlockType.class.php');
		require_once(ULTIMATE_DIR.'lib/data/blocktype/BlockTypeAction.class.php');
		require_once(ULTIMATE_DIR.'lib/data/blocktype/BlockTypeEditor.class.php');
		require_once(ULTIMATE_DIR.'lib/system/cache/builder/BlockTypeCacheBuilder.class.php');
		$objectAction = new BlockTypeAction(array(), 'create', $parameters);
		$objectAction->executeAction();
		
		$parameters = array(
			'data' => array(
				'packageID' => $packageID,
				'blockTypeName' => 'ultimate.blocktype.media',
				'blockTypeClassName' => 'ultimate\system\blocktype\MediaBlockType'
			)
		);
		$objectAction = new BlockTypeAction(array(), 'create', $parameters);
		$objectAction->executeAction();
	}
	
	/**
	 * Deactivates the created menu item.
	 */
	protected function deactivateMenuItem() {
		$sql = 'UPDATE wcf'.WCF_N.'_page_menu_item
		        SET    isDisabled = ?
		        WHERE  menuItem = ?
		        AND    packageID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			1,
			'ultimate.header.menu.index',
			$this->packageID
		));
	}
	
}
new InstallUltimateCMS();
