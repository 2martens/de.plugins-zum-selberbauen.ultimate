<?php
/**
 * Contains the LatestContentsContentDashboardBox class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.dashboard.box
 * @category	Ultimate CMS
 */
namespace ultimate\system\dashboard\box;
use ultimate\system\cache\builder\LatestContentsCacheBuilder;
use wcf\data\dashboard\box\DashboardBox;
use wcf\data\user\UserProfile;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractContentDashboardBox;
use wcf\system\WCF;

/**
 * Dashboard content box for the latest contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.dashboard.box
 * @category	Ultimate CMS
 */
class LatestContentsContentDashboardBox extends AbstractContentDashboardBox {
	/**
	 * Contains the latest contents.
	 * @var	\ultimate\data\content\TaggedContent[]
	 */
	public $contents = array();
	
	/**
	 * @see	\wcf\system\dashboard\box\IDashboardBox::init()
	 */
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);
		
		$this->contents = LatestContentsCacheBuilder::getInstance()->getData(array(), 'contents');
		foreach ($this->contents as $content) {
			$content->authorProfile = new UserProfile($content->__get('author'));
		}
		
		$this->fetched();
	}
	
	/**
	 * @see	\wcf\system\dashboard\box\AbstractContentDashboardBox::render()
	 */
	protected function render() {
		if (!count($this->contents)) return '';
		
		WCF::getTPL()->assign(array(
			'contents' => $this->contents
		));
		
		return WCF::getTPL()->fetch('dashboardBoxContentLatestContents', 'ultimate');
	}
}
