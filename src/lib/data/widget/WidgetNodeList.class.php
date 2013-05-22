<?php
/**
 * Contains the widget data model node list class.
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
use wcf\system\exception\SystemException;

/**
 * Represents a widget node list.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.widget
 * @category	Ultimate CMS
 */
class WidgetNodeList extends \RecursiveIteratorIterator implements \Countable {
	/**
	 * Contains the number of (real) widget nodes in this list.
	 * @var	integer
	 */
	protected $count = null;
	
	/**
	 * Contains the name of the widget node class.
	 * @var	string
	 */
	protected $nodeClassName = '\ultimate\data\widget\WidgetNode';
	
	/**
	 * Contains the id of the parent widget.
	 * @var	integer
	 */
	protected $parentWidgetID = 0;
	
	/**
	 * Contains the widget area id.
	 * @var	integer
	 */
	protected $widgetAreaID = 0;
	
	/**
	 * Creates a new WidgetNodeList object.
	 *
	 * @param	integer		$widgetAreaID
	 * @param	boolean		$includeDisabledWidgets
	 * @param	integer[]	$excludedWidgetIDs
	 */
	public function __construct($widgetAreaID = 0, $includeDisabledWidgets = false, array $excludedWidgetIDs = array()) {
		// widgets do not have hierarchy
		$this->parentWidgetID = 0;
		$this->widgetAreaID = intval($widgetAreaID);
		$parentWidget = null;
		// get parent widget
		if (!$this->parentWidgetID) {
			// empty node
			$parentWidget = new Widget(null, array(
				'widgetID' => 0,
				'widgetAreaID' => $this->widgetAreaID,
				'widgetName' => '',
				'widgetParent' => '',
				'showOrder' => 0,
				'isDisabled' => false,
				'additionalData' => ''
			));
		}
		// since we don't support hierarchy there is no necessity for this code
		else {}
	
		parent::__construct(new $this->nodeClassName($parentWidget, $includeDisabledWidgets, $excludedWidgetIDs), \RecursiveIteratorIterator::SELF_FIRST);
	}
	
	/**
	 * @see	\Countable::count()
	 */
	public function count() {
		if ($this->count === null) {
			$this->count = 0;
			foreach ($this as $widgetNode) {
				$this->count++;
			}
		}
	
		return $this->count;
	}
}
