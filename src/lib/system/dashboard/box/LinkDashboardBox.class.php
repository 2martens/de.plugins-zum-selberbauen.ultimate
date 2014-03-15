<?php
/**
 * Contains the LinkDashboardBox class.
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
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.dashboard.box
 * @category	Ultimate CMS
 */
namespace ultimate\system\dashboard\box;
use ultimate\system\cache\builder\LinkCategoryCacheBuilder;
use wcf\data\dashboard\box\DashboardBox;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Dashboard sidebar box for links.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.dashboard.box
 * @category	Ultimate CMS
 */
class LinkDashboardBox extends AbstractSidebarDashboardBox {
	/**
	 * The links.
	 * @var \ultimate\data\link\Link[]
	 */
	public $links = array();
	
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
		
		$linkCategoryID = ULTIMATE_LINKS_CATEGORY;
		if ($linkCategoryID) {
			$links = LinkCategoryCacheBuilder::getInstance()->getData(array(), 'linksToCategoryID');
			$this->links = $links[$linkCategoryID];
		}
		
		$remainingLinks = array();
		$items = ULTIMATE_LINKS_ITEMS;
		$i = 0;
		foreach ($this->links as $linkID => $link) {
			if ($i >= $items) {
				break;
			}
			$remainingLinks[$linkID] = $link;
			$i++;
		}
		$this->links = $remainingLinks;
		
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
		if (empty($this->links)) {
			return '';
		}
		
		WCF::getTPL()->assign(array(
			'links' => $this->links
		));
		
		return WCF::getTPL()->fetch('dashboardBoxLinks', 'ultimate');
	}
}
