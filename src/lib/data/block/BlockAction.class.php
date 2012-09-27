<?php
namespace ultimate\data\block;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\cache\CacheHandler;

/**
 * Executes block-related actions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.ultimate.block
 * @category	Ultimate CMS
 */
class BlockAction extends AbstractDatabaseObjectAction {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$className
	 */
	public $className = '\ultimate\data\block\BlockEditor';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddBlock');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsDelete
	*/
	protected $permissionsDelete = array('admin.content.ultimate.canDeleteBlock');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsUpdate
	*/
	protected $permissionsUpdate = array('admin.content.ultimate.canEditBlock');
	
	/**
	 * Returns an available block id.
	 */
	public function getAvailableBlockID() {
		// reading cache
		$cacheName = 'block';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\BlockCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$blockIDs = CacheHandler::getInstance()->get($cacheName, 'blockIDs');
		// determine next available id
		$blackList = $this->parameters['data']['blockIDBlackList'];
		$realBlackList = array_merge($blockIDs, $blackList);
		$lastID = max($realBlackList);
		
		$nextAvailableID = $lastID++;
		return $nextAvailableID;
	}
	
	public function validateGetAvailableBlockID() {
		// no permissions required
	}
}
