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
use ultimate\system\blocktype\BlockTypeHandler;
use ultimate\system\blocktype\ContentBlockType;
use ultimate\system\cache\builder\LatestContentsCacheBuilder;
use ultimate\system\layout\LayoutHandler;
use ultimate\system\template\TemplateHandler;
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
	 * The latest contents.
	 * @var	\ultimate\data\content\TaggedContent[]
	 */
	public $contents = array();
	
	/**
	 * The block that is used for displaying the contents.
	 * @var \ultimate\data\block\Block
	 */
	public $block = null;
	
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
		
		// retrieve contents for dashboard box
		$layout = LayoutHandler::getInstance()->getLayoutFromObjectData(0, 'index');
		$template = TemplateHandler::getInstance()->getTemplate($layout->__get('layoutID'));
		$blocks = $template->__get('blocks');
		
		foreach ($blocks as $blockID => $block) {
			/* @var $blockTypeDatabase \ultimate\data\blocktype\BlockType */
			$blockTypeID = $block->__get('blockTypeID');
			/* @var $blockType \ultimate\system\blocktype\AbstractBlockType */
			$blockType = BlockTypeHandler::getInstance()->getBlockType($blockTypeID);
			if (!($blockType instanceof ContentBlockType)) {
				continue;
			}
			
			$blockType->init('index', $layout, null, $blockID, null);
			$blockType->readData();
			$this->contents = $blockType->__get('contents');
			$this->block = $block;
			break;
		}
		
		if (empty($this->contents)) {
			$this->contents = LatestContentsCacheBuilder::getInstance()->getData(array(), 'contents');
		}
		
		// apply number of contents
		$remainingContents = array();
		$numberOfItems = ULTIMATE_LATEST_CONTENTS_CONTENT_ITEMS;
		$i = 0;
		foreach ($this->contents as $contentID => $content) {
			if ($i >= $numberOfItems) {
				break;
			}
			
			$remainingContents[$contentID] = $content;
			$i++;
		}
		
		foreach ($this->contents as $content) {
			$content->authorProfile = new UserProfile($content->__get('author'));
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
		if (!count($this->contents)) return '';
		
		WCF::getTPL()->assign(array(
			'contents' => $this->contents,
			'block' => $this->block
		));
		
		return WCF::getTPL()->fetch('dashboardBoxContentLatestContents', 'ultimate');
	}
}
