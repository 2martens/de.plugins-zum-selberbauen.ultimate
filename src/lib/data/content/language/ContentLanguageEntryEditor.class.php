<?php
/**
 * Contains ContentLanguageEntryEditor class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content.language
 * @category	Ultimate CMS
 */
namespace ultimate\data\content\language;
use wcf\data\AbstractLanguageEntryEditor;

/**
 * Editor for content language entries.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content.language
 * @category	Ultimate CMS
 */
class ContentLanguageEntryEditor extends AbstractLanguageEntryEditor {
	/**
	 * name of the base class
	 * @var	string
	 */
	protected static $baseClass = '\ultimate\data\content\language\ContentLanguageEntry';
	
	/**
	 * Name of cache class (FQCN).
	 * @var string
	 */
	protected static $cacheClass = '\ultimate\data\content\language\ContentLanguageEntryCache';
}
