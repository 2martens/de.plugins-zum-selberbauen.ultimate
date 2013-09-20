<?php
/**
 * Contains the block data model editor class.
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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.block
 * @category	Ultimate CMS
 */
namespace ultimate\data\block;
use ultimate\system\cache\builder\BlockCacheBuilder;
use ultimate\system\cache\builder\TemplateCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\WCF;

/**
 * Provides functions to edit blocks.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.ultimate.block
 * @category	Ultimate CMS
 */
class BlockEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * The base class.
	 * @var string
	 */
	protected static $baseClass = '\ultimate\data\block\Block';
	
	/**
	 * Adds the block to the specified template.
	 * 
	 * @param	integer	$templateID
	 */
	public function addToTemplate($templateID) {
		$sql = "SELECT   COUNT(*) AS count
		        FROM     ultimate".WCF_N."_block_to_template
		        WHERE    blockID  = ?
		        AND      templateID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->__get('blockID'),
			$templateID
		));
		$row = $statement->fetchArray();
		
		if (!$row['count']) {
			$sql = "INSERT INTO	ultimate".WCF_N."_block_to_template
			               (blockID, templateID)
			        VALUES (?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$this->__get('blockID'),
				$templateID
			));
		}
	}
	
	/**
	 * Removes the block from the specified template.
	 *
	 * @param	integer	$templateID
	 */
	public function removeFromTemplate($templateID) {
		$sql = "DELETE FROM	ultimate".WCF_N."_block_to_template
		        WHERE       blockID  = ?
		        AND         templateID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->__get('blockID'),
			$template
		));
	}
	
	/**
	 * Returns show order for a new block.
	 *
	 * @param	integer		$showOrder
	 * @param	integer		$templateID
	 * @return	integer
	 */
	public static function getShowOrder($showOrder, $templateID) {
		if ($showOrder == 0) {
			// get next number in row
			$sql = 'SELECT    MAX(showOrder) AS showOrder
			        FROM      ultimate'.WCF_N.'_block block
			        LEFT JOIN ultimate'.WCF_N.'_block_to_template blockToTemplate
			        ON        (block.blockID = blockToTemplate.blockID)
			        WHERE     blockToTemplate.templateID         = ?';
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				intval($templateID)
			));
			$row = $statement->fetchArray();
			if (!empty($row)) $showOrder = intval($row['showOrder']) + 1;
			else $showOrder = 1;
		}
		else {
			$sql = 'UPDATE    ultimate'.WCF_N.'_block block
			        SET       showOrder = showOrder + 1
			        LEFT JOIN ultimate'.WCF_N.'_block_to_template blockToTemplate
			        ON        (block.blockID = blockToTemplate.blockID)
			        WHERE     blockToTemplate.templateID = ?
			        AND       block.showOrder >= ?';
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				intval($templateID),
				$showOrder
			));
		}
	
		return $showOrder;
	}
	/**
	 * Resets the cache.
	 */
	public static function resetCache() {
		BlockCacheBuilder::getInstance()->reset();
		TemplateCacheBuilder::getInstance()->reset();
	}
}
