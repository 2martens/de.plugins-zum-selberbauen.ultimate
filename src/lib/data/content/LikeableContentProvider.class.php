<?php
/**
 * Contains the LikeableContentProvider class.
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
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use wcf\data\like\object\ILikeObject;
use wcf\data\like\ILikeObjectTypeProvider;
use wcf\data\object\type\AbstractObjectTypeProvider;

/**
 * Object type provider for likeable contents.
 * 
 * @author		Jim Martens
 * @copyright	2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class LikeableContentProvider extends AbstractObjectTypeProvider implements ILikeObjectTypeProvider {
	/**
	 * @see	\wcf\data\object\type\AbstractObjectTypeProvider::$className
	 */
	public $className = 'wbb\data\post\Post';
	
	/**
	 * @see	\wcf\data\object\type\AbstractObjectTypeProvider::$decoratorClassName
	 */
	public $decoratorClassName = 'wbb\data\post\LikeablePost';
	
	/**
	 * @see	\wcf\data\object\type\AbstractObjectTypeProvider::$listClassName
	 */
	public $listClassName = 'wbb\data\post\PostList';
	
	/**
	 * @see	\wcf\data\like\ILikeObjectTypeProvider::checkPermissions()
	 */
	public function checkPermissions(ILikeObject $content) {
		return $content->canRead();
	}
}
