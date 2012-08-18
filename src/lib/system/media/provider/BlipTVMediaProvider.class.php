<?php
namespace ultimate\system\media\provider;
use wcf\system\exception\SystemException;
use wcf\system\Regex;
use wcf\util\StringUtil;
use wcf\util\XML;

/**
 * Represents blipTV as media provider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
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
		$source = $this->getEmbedSource(StringUtil::trim($source));
		
		$html = '<iframe';
		$html .= ' '.$this->getAttributeHTML('src', 'http://blip.tv/play/'.$source.'.html?p=1');
		$html .= ' '.$this->getAttributeHTML('width', integer($width));
		$html .= ' '.$this->getAttributeHTML('height', integer($height));
		$html .= '></iframe>';
		
		$html .= '<embed';
		$html .= ' '.$this->getAttributeHTML('type', 'application/x-shockwave-flash');
		$html .= ' '.$this->getAttributeHTML('src', 'http://a.blip.tv/api.swf#'.$source);
		$html .= ' '.$this->getAttributeHTML('style', 'display: none;');
		$html .= '></embed>';
		
		return $html;
	}
	
	protected function getEmbedSource($source) {
		$regex = '^http:\/\/blip\.tv\/[\w\d-]+\/[\w\d-]+-(\d+)$';
		$regexObj = new Regex($regex);
		if (!$regexObj->match($source)) {
			throw new SystemException('invalid source', 0, 'The given source URL is not a valid blip.tv share link.');
		}
		
		$matches = $regexObj->getMatches();
		$videoID = $matches[1];
		
		// if this ini value is set to off, the following code cannot be executed
		if (ini_get('allow_url_fopen') == '0') {
			throw new SystemException('allow_url_fopen deactivated', 0, 'To parse blip.tv links, the PHP ini value \'allow_url_fopen\' has to be activated.');
		}
		
		// get embed code
		$opts = array(
			'http' => array(
				'user_agent' => 'PHP libxml agent'
			)
		);
		// bugfix to avoid SERVER ERROR due to missing user agent
		$context = stream_context_create($opts);
		
		$xml = new XML();
		try {
			libxml_set_streams_context($context);
			$xml->load('http://blip.tv/rss/view/'.$videoID);
		}
		catch (\Exception $e) { // bugfix to avoid file caching problems
			libxml_set_streams_context($context);
			$xml->load('http://blip.tv/rss/view/'.$videoID);
		}
		
		// parse xml
		$xpath = $xml->xpath();
		$mediaPlayer = $xpath->query('/rss/item/media:player')->item(0);
		$embedCode = $mediaPlayer->nodeValue;
		
		// get new video id
		$regex = '^<iframe src="http:\/\/blip\.tv\/play\/(\w+)\.html\?p=1"';
		$regexObj = new Regex($regex);
		$regexObj->match($embedCode);
		
		$matches = $regexObj->getMatches();
		$videoID = $matches[1];
		
		return $videoID;
	}
}
