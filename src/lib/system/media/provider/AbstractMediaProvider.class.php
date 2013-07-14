<?php
/**
 * Contains the AbstractMediaProvider class.
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
use wcf\system\event\EventHandler;
use wcf\util\StringUtil;

/**
 * Abstract class for every MediaProvider.
 * 
 * All MediaProviders should extend on this class.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
abstract class AbstractMediaProvider implements IMediaProvider {
	/**
	 * Contains the accepted hosts.
	 * @var string[]
	 */
	protected $hosts = array();
	
	/**
	 * @internal Returns basic iframe HTML. For more specific HTML you have to override this method.
	 * 
	 * @see \ultimate\system\media\provider\IMediaProvider::getHTML()
	 */
	public function getHTML($source, $width, $height) {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'getHTML');
		$html = '<iframe ';
		$html .= ' '.$this->getAttributeHTML('src', StringUtil::trim($source));
		$html .= ' '.$this->getAttributeHTML('width', integer($width));
		$html .= ' '.$this->getAttributeHTML('height', integer($height));
		$html .= '></iframe>';
		return $html;
	}
	
	/**
	 * @see \ultimate\system\media\provider\IMediaProvider::canHandle()
	 */
	public function canHandle($host) {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'canHandle');
		$host = StringUtil::trim($host);
		return in_array($host, $this->hosts);
	}
	
	/**
	 * Returns variables.
	 *
	 * @param	string	$name
	 * @return	mixed|null	null if no fitting variable was found
	 */
	public function __get($name) {
		if (isset($this->{$name})) {
			return $this->{$name};
		}
	
		return null;
	}
	
	/**
	 * Returns the HTML for the attribute with the given name and value.
	 *
	 * @since	1.0.0
	 *
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	string
	 */
	protected function getAttributeHTML($name, $value) {
		return $name.'="'.$value.'"';
	}
	
	/**
	 * Returns embed information.
	 * 
	 * This can be simply the embed URL but also an imploded array with the new videoID, width and height.
	 *
	 * @since	1.0.0
	 * @internal Overwrite this method if you need it. If the media provider requires allow_url_fopen (e.g. oembed usage), check it and
	 * throw an exception if it is not set.
	 *
	 * @param	string	$source
	 * @param	integer	$maxwidth	(optional) the maximum of width available
	 * @param	integer	$maxheight	(optional) the maximum of height available
	 * @return	string
	 */
	protected function getEmbedInformation($source, $maxwidth = 0, $maxheight = 0) {}
}
