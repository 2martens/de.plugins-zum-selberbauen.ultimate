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
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @category	Ultimate CMS
 */
namespace ultimate\acp;
use ultimate\data\blocktype\BlockTypeAction;
use ultimate\data\category\language\CategoryLanguageEntryEditor;
use wcf\system\WCF;

/**
 * Is called during installation of Ultimate CMS.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
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
		$this->addLanguageEntries();
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
				'packageID' => $this->packageID,
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
	
	/**
	 * Adds the language entries for the first two categories.
	 */
	protected function addLanguageEntries() {
		// workaround for installation
		require_once(WCF_DIR.'lib/data/ILanguageEntry.class.php');
		require_once(WCF_DIR.'lib/data/ILanguageEntryEditor.class.php');
		require_once(WCF_DIR.'lib/data/AbstractLanguageEntry.class.php');
		require_once(WCF_DIR.'lib/data/AbstractLanguageEntryCache.class.php');
		require_once(WCF_DIR.'lib/data/AbstractLanguageEntryEditor.class.php');
		require_once(ULTIMATE_DIR.'lib/data/category/language/CategoryLanguageEntry.class.php');
		require_once(ULTIMATE_DIR.'lib/data/category/language/CategoryLanguageEntryCache.class.php');
		require_once(ULTIMATE_DIR.'lib/data/category/language/CategoryLanguageEntryEditor.class.php');
		
		$languages = WCF::getLanguage()->getLanguages();
		$entryDataCategory1 = array();
		$entryDataCategory2 = array();
		foreach ($languages as $languageID => $language) {
			/* @var $language \wcf\data\language\Language */
			if ($language->__get('languageCode') == 'de') {
				$entryDataCategory1[$languageID] = array(
					'categoryTitle' => 'Nicht kategorisiert'
				);
				$entryDataCategory2[$languageID] = array(
					'categoryTitle' => 'Seiten',
					'categoryDescription' => "Alle Inhalte, die als Seiten fungieren, sollten direkt oder indirekt in dieser Kategorie sein. Alle Inhalte, die in dieser Kategorie sind, werden nicht bei dem Erstellen von den 'Letzten Aktivitäten' berücksichtigt."
				);
			}
			else if ($language->__get('languageCode') == 'de-informal') {
				$entryDataCategory1[$languageID] = array(
					'categoryTitle' => 'Nicht kategorisiert'
				);
				$entryDataCategory2[$languageID] = array(
					'categoryTitle' => 'Seiten',
					'categoryDescription' => "Alle Inhalte, die als Seiten fungieren, sollten direkt oder indirekt in dieser Kategorie sein. Alle Inhalte, die in dieser Kategorie sind, werden nicht bei dem Erstellen von den 'Letzten Aktivitäten' berücksichtigt."
				);
			}
			else if ($language->__get('languageCode') == 'en') {
				$entryDataCategory1[$languageID] = array(
					'categoryTitle' => 'Uncategorized'
				);
				$entryDataCategory2[$languageID] = array(
					'categoryTitle' => 'Pages',
					'categoryDescription' => "All contents that are used by pages should be in this category, be it directly or indirectly. All contents that are in this category directly or indirectly, are not used for 'Recent Activities'."
				);
			}
		}
		// if languages are installed that are not supported, use english version
		// 0 stands for neutral item
		$entryDataCategory1[0] = array(
			'categoryTitle' => 'Uncategorized'
		);
		$entryDataCategory2[0] = array(
			'categoryTitle' => 'Uncategorized',
			'categoryDescription' => "All contents that are used by pages should be in this category, be it directly or indirectly. All contents that are in this category directly or indirectly, are not used for 'Recent Activities'."
		);
		
		// the first category has always ID 1
		CategoryLanguageEntryEditor::createEntries(1, $entryDataCategory1);
		// the second category has always ID 2
		CategoryLanguageEntryEditor::createEntries(2, $entryDataCategory2);
	}
	
}
new \ultimate\acp\InstallUltimateCMS();
