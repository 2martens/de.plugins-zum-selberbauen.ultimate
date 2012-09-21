<?php
namespace ultimate\system\event\listener;
use ultimate\system\exception\DebugException;
use wcf\data\category\CategoryAction;
use wcf\system\cache\CacheHandler;
use wcf\system\event\IEventListener;
use wcf\system\io\File;

/**
 * Executes some functions on the first start of the ACP after installation.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
class IndexFirstTimeListener implements IEventListener {
	/**
	 * Contains the name of the config file.
	 * @var	string
	 */
	const CONFIG_FILE = 'config.inc.php';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.event.IEventListener.html#execute
	 */
	public function execute($eventObj, $className, $eventName) {
		// adds default link category
		require_once(ULTIMATE_DIR.'acp/'.self::CONFIG_FILE);
		if (!$initiatedDefaultLinkCategory) {
			$this->createDefaultLinkCategory();
			$this->updateConfigFile();
		}
		
		// debug
		try {
			throw new DebugException('testCall');
		}
		catch (DebugException $e) {
			die('logID => '.$e->getLogID());
		}
	}
	
	/**
	 * Creates the default link category.
	 */
	protected function createDefaultLinkCategory() {
		CacheHandler::getInstance()->clear(WCF_DIR.'cache/', 'cache.objectType-*.php');
		$objectType = CategoryHandler::getInstance()->getObjectTypeByName('de.plugins-zum-selberbauen.ultimate.linkCategory');
		// until it's working, we have to do this
		if ($objectType === null) return;
		if (!isset($objectType->objectTypeID)) exit;
		
		$parameters = array(
			'data' => array(
				'objectTypeID' => $objectType->objectTypeID,
				'parentCategoryID' => 0,
				'showOrder' => 0,
				'title' => 'ultimate.link.category.title.category1',
				'time' => TIME_NOW
			)
		);
		$action = new CategoryAction(array(), 'create', $parameters);
		$action->executeAction();
	}
	
	/**
	 * Rewrites the config file.
	 */
	protected function updateConfigFile() {
		$file = new File(ULTIMATE_DIR.'acp/'.self::CONFIG_FILE);
		$content = '<?php'."\n";
		$content .= '/*'."\n".' * This file was automatically generated. To not modify it.'."\n".' */'."\n\n";
		$content .= '$initiatedDefaultLinkCategory = true;'."\n";
		$file->write($content);
		$file->close();
	}
}
