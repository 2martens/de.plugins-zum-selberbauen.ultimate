<?php
/**
 * Contains the CategoryEditorTest class.
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
use ultimate\tests\AbstractUltimateTestCase;

require_once(__DIR__.'/../../../../AbstractUltimateTestCase.class.php');

/**
 * Tests the CategoryEditor class.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 */
class CategoryEditorTest extends AbstractUltimateTestCase {
	/**
	 * @covers ultimate\data\category\CategoryEditor::create
	 */
	public function testCreate() {
		$parameters = array(
			'categoryTitle' => 'Neue Kategorie',
			'categoryParent' => 0,
			'categorySlug' => 'new-category'			
		);
		/* @var $category \ultimate\data\category\Category */
		$category = CategoryEditor::create($parameters);
		
		self::assertInstanceOf('ultimate\data\category\Category', $category, 'The returned category is not of the expected type.');
	}
}
