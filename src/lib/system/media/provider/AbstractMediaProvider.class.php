<?php
namespace ultimate\system\media\provider;
use wcf\system\event\EventHandler;
use wcf\util\StringUtil;

/**
 * Abstract class for every MediaProvider.
 * 
 * All MediaProviders should extend on this class.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.media.provider
 * @category	Ultimate CMS
 */
abstract class AbstractMediaProvider implements IMediaProvider {
	/**
	 * Contains the accepted hosts.
	 * @var string[]
	 */
	protected $hosts = array();
	
	/**
	 * @internal Returns basic iframe HTML. For more specific HTML you have to override this method.
	 * 
	 * @see \ultimate\system\media\provider\IMediaProvider::getHTML()
	 */
	public function getHTML($source, $width, $height) {
		// fire event
		EventHandler::fireAction($this, 'getHTML');
		$html = '<iframe ';
		$html .= ' '.$this->getAttributeHTML('src', StringUtil::trim($source));
		$html .= ' '.$this->getAttributeHTML('width', integer($width));
		$html .= ' '.$this->getAttributeHTML('height', integer($height));
		$html .= '></iframe>';
		return $html;
	}
	
	/**
	 * @see \ultimate\system\media\provider\IMediaProvider::canHandle()
	 */
	public function canHandle($host) {
		// fire event
		EventHandler::fireAction($this, 'canHandle');
		$host = StringUtil::trim($host);
		return in_array($host, $this->hosts);
	}
	
	/**
	 * Returns the HTML for the attribute with the given name and value.
	 *
	 * @since	1.0.0
	 *
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	string
	 */
	protected function getAttributeHTML($name, $value) {
		return $name.'="'.$value.'"';
	}
}
