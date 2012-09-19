<?php
namespace ultimate\system\media\provider;

/**
 * Interface for MediaProvider. 
 * 
 * Instead of implementing this interface directly, you should extend on AbstractMediaProvider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
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