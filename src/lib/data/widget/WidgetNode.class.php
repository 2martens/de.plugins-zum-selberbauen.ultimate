<?php
/**
 * Contains the widget data model node class.
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
 * @subpackage	data.widget
 * @category	Ultimate CMS
 */
namespace ultimate\data\widget;
use ultimate\system\widget\WidgetHandler;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;

/**
 * Represents a widget node.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.widget
 * @category	Ultimate CMS
 */
class WidgetNode extends DatabaseObjectDecorator implements \RecursiveIterator, \Countable {
	/**
	 * Contains the current index.
	 * @var	integer
	 */
	protected $index = 0;
	
	/**
	 * Contains the child widget nodes.
	 * @var	\ultimate\data\widget\WidgetNode[]
	 */
	protected $childWidgets = array();
	
	/**
	 * Indicates if disabled widgets are included.
	 * @var	boolean
	*/
	protected $includeDisabledWidgets = false;
	
	/**
	 * Contains widget IDs of excluded widgets.
	 * @var	integer[]
	 */
	protected $excludedMenuItemIDs = array();
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObjectDecorator.html#$baseClass
	*/
	protected static $baseClass = '\ultimate\data\widget\Widget';
	
	/**
	 * Creates a new MenuItemWidgetNode object.
	 *
	 * @param	\wcf\data\DatabaseObject $object
	 * @param	boolean					 $includeDisabledWidgets
	 * @param	integer[]				 $excludedWidgets
	 * @see		\wcf\data\DatabaseObjectDecorator::__construct()
	 */
	public function __construct(DatabaseObject $object, $includeDisabledWidgets = false, array $excludedWidgetIDs = array()) {
		parent::__construct($object);
	
		$this->includeDisabledWidgets = $includeDisabledWidgets;
		$this->excludedWidgetIDs = $excludedWidgetIDs;
	
		$className = get_called_class();
		/* @var $widget \ultimate\data\widget\Widget */
		foreach (WidgetHandler::getInstance()->getChildWidgets($this->getDecoratedObject()) as $widget) {
			if ($this->fulfillsConditions($widget)) {
				$this->childWidgets[] = new $className($widget, $includeDisabledWidgets, $excludedWidgetIDs);
			}
		}
	}
	
	/**
	 * @see	\Countable::count()
	 */
	public function count() {
		return count($this->childWidgets);
	}
	
	/**
	 * @see	\Iterator::current()
	 */
	public function current() {
		return $this->childWidgets[$this->index];
	}
	
	/**
	 * Returns true if the given widget fulfills all needed conditions to
	 * be included in the list.
	 *
	 * @param	\ultimate\data\widget\Widget $widget
	 * @return	boolean
	 */
	public function fulfillsConditions(Widget $widget) {
		return !in_array($widget->__get('widgetID'), $this->excludedWidgetIDs) && ($this->includeDisabledWidgets || !$widget->__get('isDisabled'));
	}
	
	/**
	 * @see	\RecursiveIterator::getChildren()
	 */
	public function getChildren() {
		return $this->childWidgets[$this->index];
	}
	
	/**
	 * @see	\RecursiveIterator::getChildren()
	 */
	public function hasChildren() {
		return !empty($this->childWidgets);
	}
	
	/**
	 * @see	\Iterator::key()
	 */
	public function key() {
		return $this->index;
	}
	
	/**
	 * @see	\Iterator::next()
	 */
	public function next() {
		$this->index++;
	}
	
	/**
	 * @see	\Iterator::rewind()
	 */
	public function rewind() {
		$this->index = 0;
	}
	
	/**
	 * @see	\Iterator::valid()
	 */
	public function valid() {
		return isset($this->childWidgets[$this->index]);
	}
}
