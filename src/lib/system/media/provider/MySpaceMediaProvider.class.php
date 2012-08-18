<?php
namespace ultimate\system\media\provider;
use wcf\system\exception\SystemException;
use wcf\system\Regex;
use wcf\util\StringUtil;

/**
 * Represents MySpace as media provider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
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
		$source = $this->getEmbedSource(StringUtil::trim($source));
		
		$html = '<object';
		$html .= ' '.$this->getAttributeHTML('width', intval($width));
		$html .= ' '.$this->getAttributeHTML('height', intval($height));
		$html .= ' '.$this->getAttributeHTML('type', 'application/x-shockwave-flash');
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
		$html .= '></embed>';
		
		$html .= '</object>';
		return $html;
	}
	
	protected function getEmbedSource($source) {
		$regex = '^http:\/\/(?:www\.myspace\.com)\/video\/[\w\d-]+\/[\w\d-]+\/(\d+)';
		$regexObj = new Regex($regex);
		if (!$regexObj->match($source)) {
			throw new SystemException('invalid source', 0, 'The given source URL is not a valid MySpace share link.');
		}
		
		$matches = $regexOj->getMatches();
		$videoID = $matches[0];
		
		$embedSource = 'http://mediaservices.myspace.com/services/media/embed.aspx/m='.$videoID.',t=1,mt=video';
		return $embedSource;
	}
}
