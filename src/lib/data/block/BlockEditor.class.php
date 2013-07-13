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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.block
 * @category	Ultimate CMS
 */
namespace ultimate\data\block;
use ultimate\system\cache\builder\TemplateCacheBuilder;

use ultimate\system\cache\builder\BlockCacheBuilder;
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
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObjectDecorator.html#$baseClass
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
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.IEditableCachedObject.html#resetCache
	 */
	public static function resetCache() {
		BlockCacheBuilder::getInstance()->reset();
		TemplateCacheBuilder::getInstance()->reset();
	}
}
