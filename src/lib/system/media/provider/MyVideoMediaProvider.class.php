<?php
/**
 * Contains the MyVideoMediaProvider class.
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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
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
 * Represents myVideo as media provider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
class MyVideoMediaProvider extends AbstractMediaProvider {
	/**
	 * The accepted hosts.
	 * @var	string[]
	 */
	protected $hosts = array(
		'www.myvideo.de'
	);
	
	/**
	 * Returns the HTML for this provider.
	 * 
	 * {@inheritdoc}
	 */
	public function getHTML($source, $width, $height) {
		$source = $this->getEmbedInformation(StringUtil::trim($source));
		$html = '<iframe';
		$html .= ' '.$this->getAttributeHTML('src', $source);
		$html .= ' '.$this->getAttributeHTML('width', intval($width));
		$html .= ' '.$this->getAttributeHTML('height', intval($height));
		$html .= ' '.$this->getAttributeHTML('class', 'myvideo-video');
		$html .= ' '.$this->getAttributeHTML('style', 
			'width: '.intval($width).'px; height: '.intval($height).'px;'
		);
		$html .= '></iframe>';
		return $html;
	}
	
	
	/**
	 * Returns embed information.
	 * 
	 * {@inheritdoc}
	 */
	protected function getEmbedInformation($source, $maxwidth = 0, $maxheight = 0) {
		$regex = '^http://(?:www\.myvideo\.de)/watch/(\d+)';
		$regexObj = new Regex($regex);
		if (!$regexObj->match($source)) {
			throw new SystemException('invalid source', 0, 'The given source URL is not a valid MyVideo share link.');
		}
		$matches = $regexObj->getMatches();
		$videoID = $matches[1][0];
	
		$embedSource = 'https://www.myvideo.de/embed/'.$videoID;
		return $embedSource;
	}
}
