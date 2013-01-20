<?php
/**
 * Contains the Category test class.
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
namespace ultimate\data\category;
use ultimate\data\content\Content;
use ultimate\tests\AbstractUltimateTestCase;

require_once(__DIR__.'/../../../../AbstractUltimateTestCase.class.php');

/**
 * Tests the Category class.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 */
class CategoryTest extends AbstractUltimateTestCase {
	/**
	 * @covers ultimate\data\category\Category::getTitle
	 */
	public function testGetTitle() {
		// gets a category object of default entry
		$category = new Category(1);
		$title = $category->getTitle();
		
		self::assertEquals('ultimate.category.1.categoryTitle', $title, 'The given title doesn\'t equal the expected.');
	}
	
	/**
	 * @covers ultimate\data\category\Category::getChildCategories
	 */
	public function testGetChildCategories() {
		// gets a category object of default entry
		$category = new Category(1);
		$childCategories = $category->__get('childCategories');
		
		self::assertTrue(empty($childCategories), 'The child categories are not empty, although the category has no child categories.');
	}
	
	/**
	 * @covers ultimate\data\category\Category::getContents
	 */
	public function testGetContents() {
		// gets a category object of default entry
		$category = new Category(1);
		$contents = $category->__get('contents');
		
		$size = count($contents);
		self::assertEquals(2, $size, 'The returned array has more or less entries than expected.');
		
		$content1 = $contents[1];
		self::assertInstanceOf('ultimate\data\content\Content', $content1, 'The returned content #1 is not of the expected type.');
	}
}
