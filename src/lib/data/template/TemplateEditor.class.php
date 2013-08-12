<?php
/**
 * Contains the template data model editor class.
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
use ultimate\system\cache\builder\LayoutCacheBuilder;
use ultimate\system\cache\builder\MenuTemplateCacheBuilder;
use ultimate\system\cache\builder\TemplateCacheBuilder;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit templates.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.ultimate.template
 * @category	Ultimate CMS
 */
class TemplateEditor extends DatabaseObjectEditor {
	/**
	 * The base class.
	 * @var	string
	 */
	protected static $baseClass = '\ultimate\data\template\Template';
	
	/**
	 * Resets the cache.
	 */
	public static function resetCache() {
		TemplateCacheBuilder::getInstance()->reset();
		MenuTemplateCacheBuilder::getInstance()->reset();
		LayoutCacheBuilder::getInstance()->reset();
	}
}
