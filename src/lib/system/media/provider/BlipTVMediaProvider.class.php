<?php
/**
 * Contains the BlipTVMediaProvider class.
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
use wcf\util\JSON;
use wcf\util\StringUtil;

/**
 * Represents blipTV as media provider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
class BlipTVMediaProvider extends AbstractMediaProvider {
	/**
	 * @see \ultimate\system\media\provider\AbstractMediaProvider::$hosts
	 */
	protected $hosts = array(
		'blip.tv'
	);
	
	/**
	 * @see \ultimate\system\media\provider\IMediaProvider::getHTML()
	 */
	public function getHTML($source, $width, $height) {
		$sourceArray = explode('|' , $this->getEmbedInformation(StringUtil::trim($source), intval($width), intval($height)));
		$source = $sourceArray[0];
		$width = $sourceArray[1];
		$height = $sourceArray[2];
		
		$html = '<iframe';
		$html .= ' '.$this->getAttributeHTML('src', 'http://blip.tv/play/'.$source.'.html?p=1');
		$html .= ' '.$this->getAttributeHTML('width', $width);
		$html .= ' '.$this->getAttributeHTML('height', $height);
		$html .= '></iframe>';
		
		$html .= '<embed';
		$html .= ' '.$this->getAttributeHTML('type', 'application/x-shockwave-flash');
		$html .= ' '.$this->getAttributeHTML('src', 'http://a.blip.tv/api.swf#'.$source);
		$html .= ' '.$this->getAttributeHTML('style', 'display: none;');
		$html .= ' />';
		
		return $html;
	}
	
	protected function getEmbedInformation($source, $maxwidth = 0, $maxheight = 0) {
		$regex = '^http://blip\.tv/[\w\d-]+/[\w\d-]+-(\d+)$';
		$regexObj = new Regex($regex);
		if (!$regexObj->match($source)) {
			throw new SystemException('invalid source', 0, 'The given source URL is not a valid blip.tv share link.');
		}
		
		// if this ini value is set to off, the following code cannot be executed
		if (ini_get('allow_url_fopen') == '0') {
			throw new SystemException('allow_url_fopen deactivated', 0, 'To parse blip.tv links, the PHP ini value \'allow_url_fopen\' has to be activated.');
		}
		
		// get embed code
		$opts = array(
			'http' => array(
				'user_agent' => 'PHP JSON agent'
			)
		);
		// bugfix to avoid SERVER ERROR due to missing user agent
		$context = stream_context_create($opts);
		$queryParts = array(
			'maxwidth' => $maxwidth,
			'maxheight' => $maxheight
		);
		$query = http_build_query($queryParts, '', '&');
		
		$jsonResponse = file_get_contents('http://blip.tv/oembed/?url='.urlencode($source).$query, 0, $context);
		$jsonData = JSON::decode($jsonResponse);
		$width = $jsonData['width'];
		$height = $jsonData['height'];
		$embedCode = $jsonData['html'];
		
		// get new video id
		$regex = '^<iframe src="http://blip\.tv/play/(\w+)\.html\?p=1"';
		$regexObj = new Regex($regex);
		$regexObj->match($embedCode);
		
		$matches = $regexObj->getMatches();
		$videoID = $matches[1][0];
		
		$returnArray = array(
			$videoID,
			$width,
			$height
		);
		return implode('|', $returnArray);
	}
}
