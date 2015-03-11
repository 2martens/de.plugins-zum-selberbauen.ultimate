<?php
/**
 * Contains the YoutubeMediaProvider class.
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
 * Represents youtube as media provider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
class YoutubeMediaProvider extends AbstractMediaProvider {
	/**
	 * The accepted hosts.
	 * @var	string[]
	 */
	protected $hosts = array(
		'www.youtube.com',
		'www.youtube-nocookie.com',
		'youtu.be'
	);
	
	/**
	 * Returns the HTML for this provider.
	 * 
	 * {@inheritdoc}
	 */
	public function getHTML($source, $width, $height) {
		$source = $this->getEmbedInformation(StringUtil::trim($source));
		return parent::getHTML($source, $width, $height);
	}
	
	
	/**
	 * Returns embed information.
	 * 
	 * {@inheritdoc}
	 */
	protected function getEmbedInformation($source, $maxwidth = 0, $maxheight = 0) {
		// we have to let the escaping out as that is done in Regex itself, if we escape here, the result will be a wrong one
		$regex = '^https?://(?:www\.youtube\.com|youtu\.be)/(?:watch\?v=)?([\w-_]+)((?:\?|&|&amp;)\w+=[\w\d]+(?:(?:\?|&|&amp;)\w+=[\w\d]+)*)?';
		$regexObj = new Regex($regex);
		if (!$regexObj->match($source, true)) {
			throw new SystemException('invalid source', 0, 'The given source URL is not a valid Youtube share link.');
		}
		$matches = $regexObj->getMatches();
		// under 1 we have all videoID matches in an array, as we just have one, we have to take the first element of that array
		// under 2 we have all query matches in an array, as we just have one, we have to take the first element of that array
		$videoID = $matches[1][0];
		$query = $matches[2][0];
		
		$splitRegex = new Regex('(\?|&|&amp;)');
		$queryParts = $splitRegex->split($query, Regex::SPLIT_NON_EMPTY_ONLY);
		
		// support only official share values
		$allowedQueryParts = array(
			'hd', 
			't'
		);
		$realQueryParts = array();
		foreach ($queryParts as $part) {
			$partArray = explode('=', $part);
			if (!in_array($partArray[0], $allowedQueryParts)) continue;
			$realQueryParts[$partArray[0]] = $partArray[1];
		}
		// prevent showing other videos
		$realQueryParts['rel'] = 0;
		$realQuery = '?' . http_build_query($realQueryParts, '', '&');
		
		$embedSource = 'https://www.youtube-nocookie.com/embed/'.$videoID.$realQuery;
		return $embedSource;
	}
}
