<?php
namespace ultimate\system\media\provider;
use wcf\system\exception\SystemException;
use wcf\system\Regex;
use wcf\util\StringUtil;

/**
 * Represents youtube as media provider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
class YoutubeMediaProvider extends AbstractMediaProvider {
	/**
	 * @see \ultimate\system\media\provider\AbstractMediaProvider::$hosts
	 */
	protected $hosts = array(
		'www.youtube.com',
		'www.youtube-nocookie.com',
		'youtu.be'
	);
	
	/**
	 * @see \ultimate\system\media\provider\IMediaProvider::getHTML()
	 */
	public function getHTML($source, $width, $height) {
		$source = $this->getEmbedSource(StringUtil::trim($source));
		return parent::getHTML($source, $height, $width);
	}
	
	protected function getEmbedSource($source) {
		$regex = '^http:\/\/(?:www\.youtube\.com|youtu\.be)\/(?:watch\?v=)?(\w{11,})([\?&]\w+=[\w\d]+(?:[\?&]\w+=[\w\d]+)*)?';
		$regexObj = new Regex($regex);
		if (!$regexObj->match($source, true)) {
			throw new SystemException('invalid source', 0, 'The given source URL is not a valid Youtube share link.');
		}
		$matches = $regexObj->getMatches();
		$videoID = $matches[1];
		$query = $matches[2];
		$queryParts = preg_split('[&\?]', $query, null, PREG_SPLIT_NO_EMPTY);
		
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
