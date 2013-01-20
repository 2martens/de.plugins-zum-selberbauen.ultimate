<?php
/**
 * Contains the Ultimate CMS Data TestSuite.
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
namespace ultimate\tests\suits;
use ultimate\data\category\CategoryEditorTest;
use ultimate\data\category\CategoryTest;

require_once(__DIR__.'/../config.inc.php');
require_once('PHPUnit/Framework/Test.php');
require_once('PHPUnit/Framework/TestSuite.php');

/**
 * Tests all data classes.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 */
class UltimateCMSDataTestSuite extends \PHPUnit_Framework_TestSuite {
	/**
	 * Returns a test suite with all data classes.
	 * 
	 * @return \PHPUnit_Framework_TestSuite
	 */
	public static function suite() {
		$suite = new UltimateCMSDataTestSuite('DataTests');
		// CategoryTest
		$suite->addTest(new CategoryTest('testGetTitle'));
		$suite->addTest(new CategoryTest('testGetChildCategories'));
		$suite->addTest(new CategoryTest('testGetContents'));
		// CategoryEditorTest
		$suite->addTest(new CategoryEditorTest('testCreate'));
		return $suite;
	}
	
}
