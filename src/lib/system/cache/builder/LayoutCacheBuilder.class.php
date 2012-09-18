<?php
namespace ultimate\system\cache\builder;
use ultimate\data\layout\LayoutList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the layouts.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.layout
 * @category	Ultimate CMS
 */
class LayoutCacheBuilder implements ICacheBuilder {
	/**
	 * @see \wcf\system\cache\builder\ICacheBuilder::getData()
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'layouts' => array(),
			'layoutIDs' => array(),
			'layoutsToLayoutName' => array(),
			'templatesToLayoutID' => array()
		);
		
		$layoutList = new LayoutList();
		$layoutList->readObjects();
		$layouts = $layoutList->getObjects();
		$layoutIDs = $layoutList->getObjectIDs();
		if (empty($layouts)) return $data;
		
		foreach ($layouts as $layoutID => $layout) {
			/* @var $layout \ultimate\data\layout\Layout */
			$data['layoutsToLayoutName'][$layout->__get('layoutName')] = $layout;
			$data['templatesToLayoutID'][$layoutID] = $layout->__get('template');
		}
		
		$data['layouts'] = $layouts;
		$data['layoutIDs'] = $layoutIDs;
		
		return $data;
	}
}
