<?php
namespace ultimate\system\media\provider;
use wcf\system\exception\SystemException;
use wcf\system\Regex;
use wcf\util\StringUtil;

/**
 * Represents myVideo as media provider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
class MyVideoMediaProvider extends AbstractMediaProvider {
	/**
	 * @see \ultimate\system\media\provider\AbstractMediaProvider::$hosts
	 */
	protected $hosts = array(
		'www.myvideo.de'
	);
	
	/**
	 * @see \ultimate\system\media\provider\IMediaProvider::getHTML()
	 */
	public function getHTML($source, $width, $height) {
		$source = $this->getEmbedInformation(StringUtil::trim($source));
		$html = '<iframe';
		$html .= ' '.$this->getAttributeHTML('src', $source);
		$html .= ' '.$this->getAttributeHTML('width', integer($width));
		$html .= ' '.$this->getAttributeHTML('height', integer($height));
		$html .= ' '.$this->getAttributeHTML('class', 'myvideo-video');
		$html .= ' '.$this->getAttributeHTML('style', 
			'width: '.integer($width).'px; height: '.integer($height).'px;'
		);
		$html .= '></iframe>';
		return $html;
	}
	
	protected function getEmbedInformation($source) {
		$regex = '^http:\/\/(?:www\.myvideo\.de)\/watch\/(\d+)';
		$regexObj = new Regex($regex);
		if (!$regexObj->match($source)) {
			throw new SystemException('invalid source', 0, 'The given source URL is not a valid MyVideo share link.');
		}
		$matches = $regexObj->getMatches();
		$videoID = $matches[1];
	
		$embedSource = 'https://www.myvideo.de/embed/'.$videoID;
		return $embedSource;
	}
}
