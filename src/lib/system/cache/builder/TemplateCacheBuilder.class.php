<?php
/**
 * Contains the TemplateCacheBuilder class.
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
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\template\TemplateList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caches the templates.
 * 
 * Provides three variables:
 * * \ultimate\data\template\Template[] templates (templateID => template)
 * * integer[] templateIDs
 * * \ultimate\data\template\Template[] templatesToLayoutID (layoutID => template)
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class TemplateCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache.
	 * 
	 * @param	array	$parameters
	 * 
	 * @return	array
	 */
	protected function rebuild(array $parameters) {
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
		        FROM   ultimate'.WCF_N.'_template_to_layout
		        '.$conditionBuilder->__toString();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		
		while ($row = $statement->fetchArray()) {
			$data['templatesToLayoutID'][intval($row['layoutID'])] = $templates[intval($row['templateID'])];
		}
		
		return $data;
	}
}
