<?php
namespace ultimate\system\cache\builder;
use ultimate\data\blockType\BlockTypeList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the blockTypes.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimateCore
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class UltimateBlockTypeCacheBuilder implements ICacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\ICacheBuilder::getData()
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'blockTypes' => array(),
			'blockTypeIDs' => array()
		);
		
		$blockTypeList = new BlockTypeList();
		
		$blockTypeList->readObjects();
		$blockTypes = $blockTypeList->getObjects();
		if (!count($blockTypes)) return $data;
		
		$data['blockTypes'] = $blockTypes;
		$data['blockTypeIDs'] = array_keys($blockTypes);
		
		return $data;
	}
}
