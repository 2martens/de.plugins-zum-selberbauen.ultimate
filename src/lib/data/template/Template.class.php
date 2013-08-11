<?php
/**
 * Contains the template data model class.
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.template
 * @category	Ultimate CMS
 */
namespace ultimate\data\template;
use ultimate\data\block\Block;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;

/**
 * Represents a template entry.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.ultimate.template
 * @category	Ultimate CMS
 * 
 * @property-read	integer										$templateID
 * @property-read	string										$templateName
 * @property-read	string										$widgetAreaSide	('left', 'right')
 * @property-read	boolean										$showWidgetArea
 * @property-read	\ultimate\data\block\Block[]				$blocks	(blockID => block)
 * @property-read	\ultimate\data\menu\Menu|NULL				$menu
 * @property-read	\ultimate\data\widget\area\WidgetArea|NULL	$widgetArea
 */
class Template extends AbstractUltimateDatabaseObject implements ITitledObject {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableName
	 */
	protected static $databaseTableName = 'template';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'templateID';
	
	/**
	 * Returns the title of this template.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->get($this->templateName);
	}
	
	/**
	 * Returns the title of this template without language interpretation.
	 * 
	 * @return	string
	 */
	public function getTitle() {
		return $this->templateName;
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#handleData
	 */
	protected function handleData($data) {
		$data['templateID'] = intval($data['templateID']);
		$data['showWidgetArea'] = (boolean) intval($data['showWidgetArea']); 
		parent::handleData($data);
		$this->data['blocks'] = $this->getBlocks();
		$this->data['menu'] = $this->getMenu();
		$this->data['widgetArea'] = $this->getWidgetArea(); 
	}
	
	/**
	 * Returns all blocks associated with this template.
	 *
	 * @return	\wcf\data\ultimate\block\Block[]
	 */
	protected function getBlocks() {
		$sql = 'SELECT    block.*
		        FROM      ultimate'.WCF_N.'_block_to_template blockToTemplate
		        LEFT JOIN ultimate'.WCF_N.'_block block
		        ON        (block.blockID = blockToTemplate.blockID)
		        WHERE     blockToTemplate.templateID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->templateID));
		$blocks = array();
		while ($block = $statement->fetchObject('\ultimate\data\block\Block')) {
			$blocks[$block->__get('blockID')] = $block;
		}
		return $blocks;
	}
	
	/**
	 * Returns the custom menu connected with this template or null if there is no such menu.
	 * 
	 * @return \ultimate\data\menu\Menu|null
	 */
	protected function getMenu() {
		$sql = 'SELECT    menu.*
		        FROM      ultimate'.WCF_N.'_menu_to_template menuToTemplate
		        LEFT JOIN ultimate'.WCF_N.'_menu menu
		        ON        (menu.menuID = menuToTemplate.menuID)
		        WHERE     menuToTemplate.templateID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->templateID));
		$menu = $statement->fetchObject('\ultimate\data\menu\Menu');
		return $menu;
	}
	
	/**
	 * Returns the widget area connected with this template or null if there is no such widget area.
	 *
	 * @return \ultimate\data\widget\area\WidgetArea|null
	 */
	protected function getWidgetArea() {
		$sql = 'SELECT    widgetArea.*
		        FROM      ultimate'.WCF_N.'_widget_area_to_template widgetAreaToTemplate
		        LEFT JOIN ultimate'.WCF_N.'_widget_area widgetArea
		        ON        (widgetArea.widgetAreaID = widgetAreaToTemplate.widgetAreaID)
		        WHERE     widgetAreaToTemplate.templateID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->templateID));
		$widgetArea = $statement->fetchObject('\ultimate\data\widget\area\WidgetArea');
		return $widgetArea;
	}
}
