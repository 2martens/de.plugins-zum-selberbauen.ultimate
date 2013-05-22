<?php
/**
 * Contains the IWidgetType interface.
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
 * @subpackage	system.widgettype
 * @category	Ultimate CMS
 */
namespace ultimate\system\widgettype;

/**
 * Interface for all WidgetType classes.
 * 
 * This interface provides the basic methods for WidgetType classes. Instead 
 * of implementing this interface directly, you should inherit from AbstractWidgetType. 
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.widgettype
 * @category	Ultimate CMS
 */
interface IWidgetType {
	/**
	 * Initializes the widget type.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	integer											$widgetID
	 */
	public function init($widgetID);
	
	/**
	 * Reads the necessary data.
	 * 
	 * @since	1.0.0
	 * @api
	 */
	public function readData();
	
	/**
	 * Assigns template variables.
	 * 
	 * @since	1.0.0
	 * @api
	 */
	public function assignVariables();
	
	/**
	 * Returns the HTML of this widget.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string							$requestType
	 * @param	\ultimate\data\layout\Layout	$layout
	 * @return	string
	 */
	public function getHTML($requestType, \ultimate\data\layout\Layout $layout);
	
	/**
	 * Returns the options HTML for this widget.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @return	string
	 */
	public function getOptionsHTML();
}
