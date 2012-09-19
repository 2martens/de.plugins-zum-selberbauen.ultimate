<?php
namespace ultimate\system\media\provider;
use wcf\system\SingletonFactory;
use wcf\util\FileUtil;

/**
 * Handles media provider.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
class MediaProviderHandler extends SingletonFactory {
	/**
	 * Contains the media provider objects.
	 * @var \ultimate\system\media\provider\IMediaProvider[]
	 */
	protected $mediaProviders = array();
	
	/**
	 * Returns the media provider for the given host.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$host
	 * @return	null|\ultimate\system\media\provider\IMediaProvider null if no media provider can handle the host
	 */
	public function getMediaProvider($host) {
		foreach ($this->mediaProviders as $mediaProvider) {
			if (!$mediaProvider->canHandle($host)) continue;
			return $mediaProvider;
			break; // safety measure
		}
		return null;
	}
	
	/**
	 * @see \wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		/* @var $dir \Directory */
		$dir = dir(FileUtil::unifyDirSeperator(dirname(__FILE__)));
		while (false !== ($entry = $dir->read())) {
			// stripping away .class.php {10}
			$className = substr($entry, 0, strlen($entry) - 10);
			// checking for AbstractMediaProvider, IMediaProvider and this file
			if ($className == 'AbstractMediaProvider' 
				|| $className == 'IMediaProvider' 
				|| $className == 'MediaProviderHandler') continue;
			$className = __NAMESPACE__.'\\'.$className;
			$this->mediaProviders[] = new $className();
		}
		$dir->close();
	}
}
