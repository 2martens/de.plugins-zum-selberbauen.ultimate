<?php
namespace ultimate\system\cache\builder;
use ultimate\data\template\TemplateList;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the templates.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class TemplateCacheBuilder implements ICacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.ICacheBuilder.html#getData
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'templates' => array(),
			'templateIDs' => array()
		);
		
		$templateList = new TemplateList();
		
		$templateList->readObjects();
		$templates = $templateList->getObjects();
		$templateIDs = $templateList->getObjectIDs();
		if (empty($templates)) return $data;
		
		$data['templates'] = $templates;
		$data['templateIDs'] = $templateIDs;
		
		return $data;
	}
}
