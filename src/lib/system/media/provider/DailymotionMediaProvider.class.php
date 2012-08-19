<?php
namespace ultimate\system\media\provider;
use wcf\system\exception\SystemException;
use wcf\system\Regex;
use wcf\util\StringUtil;

/**
 * Represents Dailymotion as media provider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
class DailymotionMediaProvider extends AbstractMediaProvider {
	/**
	 * @see \ultimate\system\media\provider\AbstractMediaProvider::$hosts
	 */
	protected $hosts = array(
		'www.dailymotion.com'
	);
	
	/**
	 * @see \ultimate\system\media\provider\IMediaProvider::getHTML()
	 */
	public function getHTML($source, $width, $height) {
		$source = $this->getEmbedInformation(StringUtil::trim($source));
		return parent::getHTML($source, $width, $height);
	}
	
	protected function getEmbedInformation($source) {
		$regex = '^http:\/\/(?:www\.dailymotion\.com)\/video\/(\w{6,})';
		$regexObj = new Regex($regex);
		if (!$regexObj->match($source)) {
			throw new SystemException('invalid source', 0, 'The given source URL is not a valid Dailymotion share link.');
		}
		
		$matches = $regexObj->getMatches();
		$videoID = $matches[1];
		
		$embedSource = 'http://www.dailymotion.com/embed/video/'.$videoID.'?logo=0';
		return $embedSource;
	}
}
