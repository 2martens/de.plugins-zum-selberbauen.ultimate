<?php
namespace ultimate\system\cronjob;
use ultimate\data\content\ContentAction;
use ultimate\data\page\PageAction;
use wcf\data\cronjob\Cronjob;
use wcf\system\cache\CacheHandler;
use wcf\system\cronjob\AbstractCronjob;

/**
 * Publishes contents and pages.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cronjob
 * @category	Ultimate CMS
 */
class PublishContentPageCronjob extends AbstractCronjob {
	
	/**
	 * @see \wcf\system\cronjob\ICronjob::execute()
	 */
	public function execute(Cronjob $cronjob) {
		// reading cache
		$cacheName = 'content';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\ContentCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$contents = CacheHandler::getInstance()->get($cacheName, 'contents');
		
		$cacheName = 'page';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\PageCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$pages = CacheHandler::getInstance()->get($cacheName, 'pages');
		
		// checking publish dates
		$updateObjects = array();
		foreach ($contents as $contentID => $content) {
			if (!(0 < $content->publishDate < TIME_NOW && $content->status == 2)) continue;
			$updateObjects[] = $content;
		}
		$parameters = array(
		    'data' => array(
			    'status' => 3
		    )
		);
		$action = new ContentAction($updateObjects, 'update', $parameters);
		$action->executeAction();
		
		// pages
		$updateObjects = array();
		foreach ($pages as $pageID => $page) {
			if (!(0 < $page->publishDate <= TIME_NOW && $page->status == 2)) continue;
			$updateObjects[] = $page;
		}
		$parameters = array(
			'data' => array(
				'status' => 3
			)
		);
		$action = new PageAction($updateObjects, 'update', $parameters);
		$action->executeAction();
	}
}
