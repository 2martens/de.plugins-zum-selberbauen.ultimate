<?php
namespace ultimate\system\cache\builder;
use ultimate\data\block\BlockList;
use wcf\system\cache\builder\ICacheBuilder;
use wcf\system\WCF;

/**
 * Caches the blocks.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class BlockCacheBuilder implements ICacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.ICacheBuilder.html#getData
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'blocks' => array(),
			'blockIDs' => array(),
			'cachedQueryToBlockID' => array()
		);
		
		$blockList = new BlockList();
		
		$blockList->readObjects();
		$blocks = $blockList->getObjects();
		$blockIDs = $blockList->getObjectIDs();
		if (empty($blocks)) return $data;
		
		$data['blocks'] = $blocks;
		$data['blockIDs'] = $blockIDs;
		
		foreach ($data['blocks'] as $blockID => $block) {
			/* @var $block \wcf\data\ultimate\block\Block */
			
			// cache custom queries
			$sql = $block->__get('query');
			if (empty($sql)) continue;
			$parameters = $block->__get('parameters');
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute($parameters);
			
			$cachedQuery = array();
			while ($row = $statement->fetchArray()) {
				$cachedQuery[] = $row;
			}
			$data['cachedQueryToBlockID'][$blockID] = $cachedQuery;
		}
		return $data;
	}
}
