<?php
/**
 * Contains the MediaMimetypeCacheBuilder class.
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
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\media\mimetype\MediaMimetypeList;
use wcf\system\cache\builder\AbstractCacheBuilder;

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
class MediaMimetypeCacheBuilder implements AbstractCacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.AbstractCacheBuilder.html#rebuild
	 */
	protected function rebuild(array $parameters) {
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
