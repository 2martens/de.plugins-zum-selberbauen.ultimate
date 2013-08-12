<?php
/**
 * The IMediaProvider interface.
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
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
namespace ultimate\system\media\provider;

/**
 * Interface for MediaProvider. 
 * 
 * Instead of implementing this interface directly, you should extend on AbstractMediaProvider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
interface IMediaProvider {
	/**
	 * Returns the HTML for this provider.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$source
	 * @param	integer	$width
	 * @param	integer	$height
	 * @return	string
	 */
	public function getHTML($source, $width, $height);
	
	/**
	 * Returns whether this MediaProvider can handle the given URL host.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$host
	 * @return	boolean
	 */
	public function canHandle($host);
}