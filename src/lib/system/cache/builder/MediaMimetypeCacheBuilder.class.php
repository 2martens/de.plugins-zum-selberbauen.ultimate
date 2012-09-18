<?php
namespace ultimate\system\cache\builder;
use ultimate\data\media\mimetype\MediaMimetypeList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the MIME types.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class MediaMimetypeCacheBuilder implements ICacheBuilder {
	/**
	 * @see \wcf\system\cache\builder\ICacheBuilder::getData()
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'mimeTypes' => array(),
			'mimeTypeIDs' => array(),
			'mimeTypeToMIME' => array()
		);
		
		$mimeTypeList = new MediaMimetypeList();
		$mimeTypeList->readObjects();
		$mimeTypes = $mimeTypeList->getObjects();
		$mimeTypeIDs = $mimeTypeList->getObjectIDs();
		
		if (empty($mimeTypes)) return $data;
		
		$data['mimeTypes'] = $mimeTypes;
		$data['mimeTypeIDs'] = $mimeTypeIDs;
		
		foreach ($data['mimeTypes'] as $mimeType) {
			/* @var $mimeType \ultimate\data\media\mimetype\MediaMimetype */
			$data['mimeTypesToMIME'][$mimeType->__get('mimeType')] = $mimeType;
		}
		
		return $data;
	}
}
