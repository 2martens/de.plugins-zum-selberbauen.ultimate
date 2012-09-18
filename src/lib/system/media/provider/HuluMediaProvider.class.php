<?php
namespace ultimate\system\media\provider;
use wcf\system\exception\SystemException;
use wcf\system\Regex;
use wcf\util\StringUtil;
use wcf\util\XML;

/**
 * Represents Hulu as media provider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
class HuluMediaProvider extends AbstractMediaProvider {
	/**
	 * @see \ultimate\system\media\provider\AbstractMediaProvider::$hosts
	 */
	protected $hosts = array(
		'www.hulu.com'
	);
	
	/**
	 * @see \ultimate\system\media\provider\IMediaProvider::getHTML()
	 */
	public function getHTML($source, $width, $height) {
		$sourceArray = explode('|', $this->getEmbedInformation(StringUtil::trim($source), integer($width), integer($height)));
		$source = $sourceArray[0];
		$width = $sourceArray[1];
		$height = $sourceArray[2];
		
		$html = '<object';
		$html .= ' '.$this->getAttributeHTML('width', $width);
		$html .= ' '.$this->getAttributeHTML('height', $height);
		$html .= ' '.$this->getAttributeHTML('type', 'application/x-shockwave-flash');
		$html .= ' '.$this->getAttributeHTML('data', $source);
		$html .= '>';
		
		$html .= '<param';
		$html .= ' '.$this->getAttributeHTML('name', 'movie');
		$html .= ' '.$this->getAttributeHTML('value', $source);
		$html .= ' />';
		
		$html .= '<param';
		$html .= ' '.$this->getAttributeHTML('name', 'flashvars');
		$html .= ' '.$this->getAttributeHTML('value', 'ap=1');
		$html .= ' />';
		
		$html .= '<embed';
		$html .= ' '.$this->getAttributeHTML('src', $source);
		$html .= ' '.$this->getAttributeHTML('type', 'application/x-shockwave-flash');
		$html .= ' '.$this->getAttributeHTML('width', $width);
		$html .= ' '.$this->getAttributeHTML('height', $height);
		$html .= ' '.$this->getAttributeHTML('flashvars', 'ap=1');
		$html .= ' />';
		
		$html .= '</object>';
		return $html;
	}
	
	protected function getEmbedInformation($source, $maxwidth, $maxheight) {
		$regex = '^http:\/\/www\.hulu\.com\/watch\/([\d]+)';
		$regexObj = new Regex($regex);
		if (!$regexObj->match($source)) {
			throw new SystemException('invalid source', 0, 'The given source URL is not a valid Hulu share link.');
		}
		
		// if this ini value is set to off, the following code cannot be executed
		if (ini_get('allow_url_fopen') == '0') {
			throw new SystemException('allow_url_fopen deactivated', 0, 'To parse Hulu.com links, the PHP ini value \'allow_url_fopen\' has to be activated.');
		}
		
		// get embed code
		$opts = array(
			'http' => array(
				'user_agent' => 'PHP libxml agent'
			)
		);
		// bugfix to avoid SERVER ERROR due to missing user agent
		$context = stream_context_create($opts);
		$queryParts = array(
			'maxwidth' => $maxwidth,
			'maxheight' => $maxheight
		);
		$query = http_build_query($queryParts, '', '&');
		
		$xml = new XML();
		try {
			libxml_set_streams_context($context);
			$xml->load('http://www.hulu.com/api/oembed.xml?url='.urlencode($source).$query);
		}
		catch (\Exception $e) { // bugfix to avoid file caching problems
			libxml_set_streams_context($context);
			$xml->load('http://www.hulu.com/api/oembed.xml?url='.urlencode($source).$query);
		}
		
		// parse xml
		$xpath = $xml->xpath();
		$embedURLNode = $xpath->query('/oembed/embed_url')->item(0);
		$embedURL = $embedURLNode->nodeValue;
		$width = $xpath->query('/oembed/width')->item(0)->nodeValue;
		$height = $xpath->query('/oembed/height')->item(0)->nodeValue;
		
		$returnArray = array(
			$embedURL,
			$width,
			$height
		);
		return implode('|', $returnArray);
	}
}
