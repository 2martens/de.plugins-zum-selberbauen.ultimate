<?php
namespace ultimate\system\cache\builder;
use ultimate\data\blockType\BlockTypeList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the blockTypes.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class BlockTypeCacheBuilder implements ICacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.ICacheBuilder.html#getData
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'blockTypes' => array(),
			'blockTypeIDs' => array(),
			'blockTypesToName' => array()
		);
		
		$blockTypeList = new BlockTypeList();
		
		$blockTypeList->readObjects();
		$blockTypes = $blockTypeList->getObjects();
		$blockTypeIDs = $blockTypeList->getObjectIDs();
		if (empty($blockTypes)) return $data;
		
		foreach ($blockTypes as $blockType) {
			$data['blockTypesToName'][$blockType->__get('cssIdentifier')] = $blockType;
		}
		
		$data['blockTypes'] = $blockTypes;
		$data['blockTypeIDs'] = $blockTypeIDs;
		
		return $data;
	}
}
