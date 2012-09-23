<?php
namespace ultimate\system\cache\builder;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\cache\builder\TagCloudCacheBuilder;
use wcf\system\WCF;

/**
 * Caches all content tags.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class ContentTagCloudCacheBuilder extends TagCloudCacheBuilder {
	/**
	 * @see \wcf\system\cache\builder\ICacheBuilder::getData()
	 */
	public function getData(array $cacheResource) {
		parent::getData($cacheResource);
		/* @var $objectType \wcf\data\object\type\ObjectType */
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.tagging.taggableObject', 'de.plugins-zum-selberbauen.ultimate.contentTaggable');
		$this->objectTypeIDs = array(
		    $objectType->__get('objectTypeID')
		);
		// workaround
		$this->languageIDs = array_keys(WCF::getLanguage()->getLanguages());
		$this->getTags();
		
		return $this->tags;
	}
}
