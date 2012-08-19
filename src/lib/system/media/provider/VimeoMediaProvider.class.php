<?php
namespace ultimate\system\media\provider;
use wcf\system\exception\SystemException;
use wcf\system\Regex;
use wcf\util\StringUtil;

/**
 * Represents Vimeo as media provider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
class VimeoMediaProvider extends AbstractMediaProvider {
	/**
	 * @see \ultimate\system\media\provider\AbstractMediaProvider::$hosts
	 */
	protected $hosts = array(
		'www.vimeo.com'
	);
	
	/**
	 * @see \ultimate\system\media\provider\IMediaProvider::getHTML()
	 */
	public function getHTML($source, $width, $height) {
		$source = $this->getEmbedInformation(StringUtil::trim($source));
		return parent::getHTML($source, $width, $height);
	}
	
	protected function getEmbedInformation($source) {
		$regex = '^http:\/\/(?:(?:www\.)?vimeo\.com)\/(?:channels\/[\w\d-]+\/|groups\/[\w\d-]+\/videos\/)?(\d+)';
		$regexObj = new Regex($regex);
		if (!$regexObj->match($source)) {
			throw new SystemException('invalid source', 0, 'The given source URL is not a valid Dailymotion share link.');
		}
		
		$matches = $regexObj->getMatches();
		$videoID = $matches[1];
		
		$queryParts = array(
			'title' => '0',
			'byline' => '0',
			'portrait' => '0'
		);
		$query = '?'.http_build_query($queryParts, '', '&');
		
		$embedSource = 'http://player.vimeo.com/video/'.$videoID.$query;
		return $embedSource;
	}
}
