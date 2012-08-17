<?php
namespace ultimate\system\blocktype;
use ultimate\system\media\provider\MediaProviderHandler;
use ultimate\util\LinkUtil;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents the media block type.
 * 
 * Blocks of this type can display YouTube videos.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
class MediaBlockType extends AbstractBlockType {
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$templateName
	 */
	protected $templateName = 'mediaBlockType';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheName
	 */
	protected $cacheName = 'mimetype';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheBuilderClassName
	 */
	protected $cacheBuilderClassName = '\ultimate\system\cache\builder\MediaMimetypeCacheBuilder';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheIndex
	 */
	protected $cacheIndex = 'mimeTypesToMIME';
	
	/**
	 * Allowed media types.
	 * @var string[]
	 */
	protected $mediaTypes = array(
		'audio',
		'video'
	);
	
	/**
	 * Contains the media type.
	 * @var string
	 */
	protected $mediaType = '';
	
	/**
	 * URL to the source.
	 * @var string
	 */
	protected $mediaSource = '';
	
	/**
	 * Contains the type of the media source (provider or file).
	 * @var string
	 */
	protected $mediaSourceType = '';
	
	/**
	 * Contains the MIME type.
	 * @var \ultimate\data\media\mimetype\MediaMimetype	a valid MIME type
	 */
	protected $mediaMimeType = null;
	
	/**
	 * Contains the media height in pixels.
	 * @var integer
	 */
	protected $mediaHeight = 0;
	
	/**
	 * Contains the media width in pixels.
	 * @var integer
	 */
	protected $mediaWidth = 0;
	
	/**
	 * Contains the media provider HTML (if any).
	 * @var	string
	 */
	protected $mediaHTML = '';
	
	/**
	 * @see \ultimate\system\blocktype\IBlockType::readData()
	 */
	public function readData() {
		parent::readData();
		// reading additional data
		$this->mediaSource = StringUtil::trim($this->block->__get('mediaSource'));
		if (!LinkUtil::isValidURL($this->mediaSource)) {
			throw new SystemException('invalid media source', 0, 'The given media source is an invalid URL.');
		}
		$mimeType = StringUtil::trim($this->block->__get('mimeType'));
		if (isset($this->objects[$mimeType])) {
			$this->mediaMimeType = $this->objects[$mimeType];
		}
		$this->mediaHeight = intval($this->block->__get('mediaHeight'));
		$this->mediaWidth = intval($this->block->__get('mediaWidth'));
		$this->mediaType = StringUtil::trim($this->block->__get('mediaType'));
		
		$this->determineSourceType();
	}
	
	/**
	 * @see \ultimate\system\blocktype\IBlockType::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'mediaType' => $this->mediaType,
			'mediaSourceType' => $this->mediaSourceType
		));
		
		if ($this->mediaSourceType == 'file') {
			WCF::getTPL()->assign(array(
				'mediaSource' => $this->mediaSource,
				'mediaHeight' => $this->mediaHeight,
				'mediaWidth' => $this->mediaWidth,
				'mediaMimeType' => $this->mediaMimeType
			));
		}
		elseif ($this->mediaSourceType == 'provider') {
			WCF::getTPL()->assign('mediaHTML', $this->mediaHTML);
		}
	}
	
	/**
	 * Determines the type of the source.
	 */
	protected function determineSourceType() {
		// there are no audio providers
		if ($this->mediaType == 'audio') {
			$this->mediaSourceType = 'file'; 
			return;
		}
		
		$url = LinkUtil::parseURL($this->mediaSource);
		$host = parse_url($url, PHP_URL_HOST);		
		
		/* @var $mediaProvider \ultimate\system\media\provider\IMediaProvider */
		$mediaProvider = MediaProviderHandler::getInstance()->getMediaProvider($host);
		if (is_null($mediaProvider)) {
			// assume source is a file
			$this->mediaSourceType = 'file';
		}
		else {
			$this->mediaSourceType = 'provider';
			$this->mediaHTML = $mediaProvider->getHTML($this->mediaSource, $this->mediaWidth, $this->mediaHeight);
		}
	}
}
