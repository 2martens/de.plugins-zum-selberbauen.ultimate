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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @category	Ultimate CMS
 */
namespace ultimate\acp;
use ultimate\data\blocktype\BlockTypeAction;
use wcf\system\io\File;
use wcf\system\WCF;

/**
 * Is called during installation of Ultimate CMS.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @category	Ultimate CMS
 */
final class InstallUltimateCMS {
	
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
		//$this->createHtaccess(); until further notice, deactivated
		$this->addDefaultBlockTypes();
	}
	
	/**
	 * Creates a htaccess file.
	 */
	protected function createHtaccess() {
		WCF::getTPL()->addApplication('ultimate', PACKAGE_ID, ULTIMATE_DIR.'acp/templates/');
		
		$output = WCF::getTPL()->fetch('htaccess', 'ultimate');
		$file = new File(ULTIMATE_DIR.'.htaccess');
		$file->write($output);
		$file->close();
	}
	
	/**
	 * Adds the default block types.
	 */
	protected function addDefaultBlockTypes() {
		// insert default block types
		$parameters = array(
			'data' => array(
				'packageID' => PACKAGE_ID,
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
				'packageID' => PACKAGE_ID,
				'blockTypeName' => 'ultimate.blocktype.media',
				'blockTypeClassName' => 'ultimate\system\blocktype\MediaBlockType'
			)
		);
		$objectAction = new BlockTypeAction(array(), 'create', $parameters);
		$objectAction->executeAction();
	}
	
}
new InstallUltimateCMS();
