<?php
/**
 * Contains the Ultimate Test Core class.
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
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 */
namespace ultimate\tests;

/**
 * The core class of the tests.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 */
class UltimateTestCore {
	/**
	 * Called on autoload.
	 * @param string $className
	 */
	public static function autoload($className) {
		$namespaces = explode('\\', $className);
		if (count($namespaces) > 1) {
			$applicationPrefix = array_shift($namespaces);
			$classPath = TEST_DIR . 'src/lib/' . implode('/', $namespaces) . '.class.php';
			if (file_exists($classPath)) {
				require_once($classPath);
			}
		}
	}
}
