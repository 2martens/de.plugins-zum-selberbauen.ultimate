<?php
namespace ultimate\system\cache\builder;
use ultimate\data\block\BlockList;
use wcf\system\cache\builder\ICacheBuilder;
use wcf\system\WCF;

/**
 * Caches the blocks.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimateCore
 * @subpackage system.cache.builder
 * @category Ultimate CMS
 */
class UltimateBlockCacheBuilder implements ICacheBuilder {
    
    /**
     * @see \wcf\system\cache\builder\ICacheBuilder::getData()
     */
    public function getData(array $cacheResource) {
        $data = array(
            'blocks' => array(),
            'blockIDs' => array(),
            'cachedQueryToBlockID' => array()
        );
        
        $blockList = new BlockList();
        
        $blockList->readObjectIDs();
        $blockList->readObjects();
        $blockIDs = $blockList->getObjectIDs();
        $blocks = $blockList->getObjects();
        if (!count($blockIDs) || !count($blocks)) return $data;
        
        $data['blocks'] = array_combine($blockIDs, $blocks);
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
