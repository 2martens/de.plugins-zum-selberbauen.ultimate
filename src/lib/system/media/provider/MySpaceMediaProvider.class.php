<?php
/**
 * Contains the MySpaceMediaProvider class.
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
use wcf\system\exception\SystemException;
use wcf\system\Regex;
use wcf\util\StringUtil;

/**
 * Represents MySpace as media provider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
class MySpaceMediaProvider extends AbstractMediaProvider {
	/**
	 * @see \ultimate\system\media\provider\AbstractMediaProvider::$hosts
	 */
	protected $hosts = array(
		'www.myspace.com'
	);
	
	/**
	 * @see \ultimate\system\media\provider\IMediaProvider::getHTML()
	 */
	public function getHTML($source, $width, $height) {
		$source = $this->getEmbedInformation(StringUtil::trim($source));
		
		$html = '<object';
		$html .= ' '.$this->getAttributeHTML('width', intval($width));
		$html .= ' '.$this->getAttributeHTML('height', intval($height));
		$html .= ' '.$this->getAttributeHTML('type', 'application/x-shockwave-flash');
		$html .= ' '.$this->getAttributeHTML('data', $source);
		$html .= '>';
		// param allowFullScreen
		$html .= '<param';
		$html .= ' '.$this->getAttributeHTML('name', 'allowFullScreen');
		$html .= ' '.$this->getAttributeHTML('value', 'true');
		$html .= ' />';
		// param wmode
		$html .= '<param';
		$html .= ' '.$this->getAttributeHTML('name', 'wmode');
		$html .= ' '.$this->getAttributeHTML('value', 'transparent');
		$html .= ' />';
		// param movie
		$html .= '<param';
		$html .= ' '.$this->getAttributeHTML('name', 'movie');
		$html .= ' '.$this->getAttributeHTML('value', $source);
		$html .= ' />';
		
		// embed
		$html .= '<embed';
		$html .= ' '.$this->getAttributeHTML('src', $source);
		$html .= ' '.$this->getAttributeHTML('width', intval($width));
		$html .= ' '.$this->getAttributeHTML('height', intval($height));
		$html .= ' '.$this->getAttributeHTML('type', 'application/x-shockwave-flash');
		$html .= ' />';
		
		$html .= '</object>';
		return $html;
	}
	
	protected function getEmbedInformation($source, $maxwidth = 0, $maxheight = 0) {
		$regex = '^http://(?:www\.myspace\.com)/video/[\w\d-]+/[\w\d-]+/(\d+)';
		$regexObj = new Regex($regex);
		if (!$regexObj->match($source)) {
			throw new SystemException('invalid source', 0, 'The given source URL is not a valid MySpace share link.');
		}
		
		$matches = $regexOj->getMatches();
		$videoID = $matches[1][0];
		
		$embedSource = 'http://mediaservices.myspace.com/services/media/embed.aspx/m='.$videoID.',t=1,mt=video';
		return $embedSource;
	}
}
