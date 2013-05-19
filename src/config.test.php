<?php
/**
 * This file is just used for testing purposes and won't be included in release versions.
 * During the installation a config file will be created automatically.
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

// de.plugins-zum-selberbauen.ultimate (packageID 24)
if (!defined('ULTIMATE_DIR')) define('ULTIMATE_DIR', dirname(__FILE__).'/');
if (!defined('RELATIVE_ULTIMATE_DIR')) define('RELATIVE_ULTIMATE_DIR', '');

// general info
if (!defined('RELATIVE_WCF_DIR')) define('RELATIVE_WCF_DIR', RELATIVE_ULTIMATE_DIR.
	'Path/to/installed/WCF');
if (!defined('PACKAGE_ID')) define('PACKAGE_ID', 24); // insert instead of 24 actual id of your Ultimate CMS installation
if (!defined('PACKAGE_NAME')) define('PACKAGE_NAME', 'Ultimate CMS');
if (!defined('PACKAGE_VERSION')) define('PACKAGE_VERSION', '1.0.0 Alpha 1');