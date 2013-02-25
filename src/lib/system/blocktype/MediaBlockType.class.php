<?php
/**
 * Contains the media block type class.
 * 
 * LICENSE:
 * This file is part of the Ultimate CMS.
 *
 * The Ultimate CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * The Ultimate CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
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
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
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
		'video',
		'photo'
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
		$mediaSource = $this->block->__get('mediaSource');
		if ($mediaSource === null) {
			$mediaSource = '';
		}
		$this->mediaSource = StringUtil::trim($mediaSource);
		if (!empty($this->mediaSource) && !LinkUtil::isValidURL($this->mediaSource)) {
			throw new SystemException('invalid media source', 0, 'The given media source is an invalid URL.');
		}
		
		$mimeType = $this->block->__get('mimeType');
		if ($mimeType === null) {
			$mimeType = 'video/mpeg';
		}
		$mimeType = StringUtil::trim($mimeType);
		if (isset($this->objects[$mimeType])) {
			$this->mediaMimeType = $this->objects[$mimeType];
		}
		
		$parameters = $this->block->__get('parameters');
		if (empty($parameters)) {
			$parameters = '0, 0';
		}
		
		$dimensions = explode(',', $parameters['dimensions']);
		$this->mediaHeight = intval($dimensions[1]);
		$this->mediaWidth = intval($dimensions[0]);
		
		$mediaType = $this->block->__get('mediaType');
		if ($mediaType === null) {
			$mediaType = 'video';
		}
		$this->mediaType = StringUtil::trim($mediaType);
		
		$this->determineSourceType();
	}
	
	/**
	 * @see \ultimate\system\blocktype\IBlockType::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		// gathering dimensions and position
		$dimensions = $this->block->__get('dimensions');
		if ($dimensions === null) {
			$dimensions = '0, 0';
		}
		$dimensions = explode(',', $dimensions);
		$position = $this->block->__get('position');
		if ($position === null) {
			$position = '0, 0';
		}
		$position = explode(',', $position);
		
		WCF::getTPL()->assign(array(
			'mediaType' => $this->mediaType,
			'mediaSourceType' => $this->mediaSourceType,
			'width' => $dimensions[0],
			'height' => $dimensions[1],
			'left' => $position[0],
			'top' => $position[1]
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
	 * @see \ultimate\system\blocktype\IBlockType::getOptionsHTML()
	 */
	public function getOptionsHTML() {
		$returnValues = parent::getOptionsHTML();
		$defaults = array(
			'mediaType' => 'audio',
			'mimeType' => 'audio/basic'
		);
		$options = $this->block->__get('additionalData');
		$options = array_merge_recursive($defaults, $options);
		
		$optionsSelect = array(
			'mediaType',
			'mimeType'
		);
		WCF::getTPL()->assign('mimeTypes', $this->objects);
		// assigning values
		foreach ($options as $optionName => $optionValue) {
			if (in_array($optionName, $optionsSelect)) {
				WCF::getTPL()->assign($optionName.'Selected', $optionValue);
			}
			else {
				WCF::getTPL()->assign($optionName, $optionValue);
			}
		}
		
		// prevent exception while accessing template in ACP
		try {
			$returnValues[1] = WCF::getTPL()->fetch('mediaBlockOptions', 'ultimate');
		}
		catch (SystemException $se) {
			WCF::getTPL()->addApplication('ultimate', 'templates/');
			$returnValues[1] = WCF::getTPL()->fetch('mediaBlockOptions', 'ultimate');
			WCF::getTPL()->addApplication('ultimate', 'acp/templates');
		}
		return $returnValues;
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
		if ($this->mediaType == 'photo') {
			$this->mediaSourceType = 'file';
			return;
		}
		$url = LinkUtil::parseURL($this->mediaSource);
		$host = parse_url($url, PHP_URL_HOST);		
		
		/* @var $mediaProvider \ultimate\system\media\provider\IMediaProvider */
		$mediaProvider = MediaProviderHandler::getInstance()->getMediaProvider($host);
		if ($mediaProvider === null) {
			// assume source is a file
			$this->mediaSourceType = 'file';
		}
		else {
			$this->mediaSourceType = 'provider';
			$this->mediaHTML = $mediaProvider->getHTML($this->mediaSource, $this->mediaWidth, $this->mediaHeight);
		}
	}
}
