<?php
namespace ultimate\system\blocktype;

/**
 * Interface for all BlockType classes.
 * 
 * This interface provides the basic methods for BlockType classes. Instead
 * of implementing this interface directly, you should inherit from AbstractBlockType.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimateCore
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
interface IBlockType {
	/**
	 * Initializes the blockType.
	 * 
	 * @since	1.0.0
	 * 
	 * @param	string											$requestType
	 * @param	\ultimate\data\AbstractUltimateDatabaseObject	$requestObject
	 * @param	integer											$blockID
	 */
	public function run($requestType, \ultimate\data\AbstractUltimateDatabaseObject $requestObject, $blockID);
	
	/**
	 * Reads the necessary data.
	 * 
	 * Use this method to load data from cache or, if not possible otherwise, from database.
	 * 
	 * @since	1.0.0
	 */
	public function readData();
	
	/**
	 * Assigns template variables.
	 * 
	 * @since	1.0.0
	 */
	public function assignVariables();
	
	/**
	 * Returns the HTML for this blockType.
	 * 
	 * @since	1.0.0
	 * 
	 * @return	string
	 */
	public function getHTML();
}
