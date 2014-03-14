<?php
/**
 * Contains the UltimateHtmlOptionsFunctionTemplatePlugin class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.template.plugin
 * @category	Ultimate CMS
 */
namespace wcf\system\template\plugin;
use ultimate\data\category\Category;
use ultimate\data\content\TaggedContent;
use ultimate\data\page\Page;

/**
 * Extends the WCF HtmlOptions function template plugin with Ultimate CMS specific needs.
 * 
 * Usage (the same options as the WCF version):
 * * {ultimateHtmlOptions options=$array}
 * * {ultimateHtmlOptions options=$array selected=$foo}
 * * {ultimateHtmlOptions options=$array name="x"}
 * * {ultimateHtmlOptions output=$outputArray}
 * * {ultimateHtmlOptions output=$outputArray values=$valueArray}
 * * {ultimateHtmlOptions object=$databaseObjectList}
 * * {ultimateHtmlOptions object=$databaseObjectList selected=$foo}
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.template.plugin
 * @category	Ultimate CMS
 */
class UltimateHtmlOptionsFunctionTemplatePlugin extends HtmlOptionsFunctionTemplatePlugin {
	/**
	 * Makes the HTML code for an option.
	 *
	 * @param	string	$key
	 * @param	\ultimate\data\category\Category|\ultimate\data\content\TaggedContent|\ultimate\data\page\Page|string	$value string means either a plain string or an object with __toString implemented
	 * @return	string
	 */
	protected function makeOption($key, $value) {
		$value = $this->encodeHTML(($value instanceof Category || $value instanceof TaggedContent || $value instanceof Page) ? $value->getLangTitle() : $value);
		return '<option label="'.$value.'" value="'.$this->encodeHTML($key).'"'.(in_array($key, $this->selected) ? ' selected="selected"' : '').'>'.$value."</option>\n";
	}
}
