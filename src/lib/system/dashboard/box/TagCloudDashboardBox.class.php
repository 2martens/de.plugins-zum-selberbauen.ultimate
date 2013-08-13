<?php
/**
 * Contains the TagCloudDashboardBox class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.dashboard.box
 * @category	Ultimate CMS
 */
namespace ultimate\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\data\object\type\ObjectTypeCache;
use wcf\page\IPage;
use wcf\system\cache\builder\TagObjectCacheBuilder;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\tagging\TagCloud;
use wcf\system\WCF;

/**
 * Dashboard sidebar box for tag cloud of contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.dashboard.box
 * @category	Ultimate CMS
 */
class TagCloudDashboardBox extends AbstractSidebarDashboardBox {
	/**
	 * tag cloud
	 * @var \wcf\system\tagging\TagCloud
	 */
	public $tagCloud = null;
	
	/**
	 * The tagIDs to objectTypeID relation.
	 * @var integer[][]
	 */
	public $tagIDsToObjectTypeID = array();
	
	/**
	 * Initializes this box.
	 * 
	 * @internal
	 * 
	 * @param	\wcf\data\dashboard\box\DashboardBox	$box
	 * @param	\wcf\page\IPage							$page
	 */
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);
	
		if (MODULE_TAGGING) {
			$languageIDs = array();
			$languageIDs = WCF::getUser()->getLanguageIDs();
				
			$this->tagCloud = new TagCloud($languageIDs);
			$this->tagIDsToObjectTypeID = TagObjectCacheBuilder::getInstance()->getData(array(), 'tagIDsToObjectTypeID');
		}
	
		$this->fetched();
	}
	
	/**
	 * Renders box view.
	 * 
	 * @internal
	 * 
	 * @return	string
	 */
	protected function render() {
		if ($this->tagCloud === null) {
			return '';
		}
		
		$tags = $this->tagCloud->getTags();
		$objectTypeToTagID = array();
		foreach ($tags as $tag) {
			$found = 0;
			$latestObjectTypeID = 0;
			foreach ($this->tagIDsToObjectTypeID as $objectTypeID => $tagIDs) {
				if (in_array($tag->__get('tagID'), $tagIDs)) {
					$found++;
					$latestObjectTypeID = $objectTypeID;
				}
			}
			
			if ($found == 1) {
				$objectTypeToTagID[$tag->__get('tagID')] = ObjectTypeCache::getInstance()->getObjectType($latestObjectTypeID);
			}
		}
		
		WCF::getTPL()->assign(array(
			'tags' => $tags,
			'objectTypes' => $objectTypeToTagID
		));
		
		return WCF::getTPL()->fetch('tagCloudBox', 'ultimate');
	}
}
