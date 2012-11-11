<?php
namespace ultimate\system\cache\builder;
use ultimate\data\template\TemplateList;
use wcf\system\cache\builder\ICacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

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
			'templateIDs' => array(),
			'templatesToLayoutID' => array()
		);
		
		$templateList = new TemplateList();
		
		$templateList->readObjects();
		$templates = $templateList->getObjects();
		$templateIDs = $templateList->getObjectIDs();
		if (empty($templates)) return $data;
		
		$data['templates'] = $templates;
		$data['templateIDs'] = $templateIDs;
		
		// read layout ids
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('templateID IN (?)', array($templateIDs));
		
		$sql = 'SELECT layoutID, templateID
		        FROM   ultimate'.ULTIMATE_N.'_template_to_layout
		        '.$conditionBuilder->__toString();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		
		while ($row = $statement->fetchArray()) {
			$data['templatesToLayoutID'][intval($row['layoutID'])] = $templates[intval($row['templateID'])];
		}
		
		return $data;
	}
}
