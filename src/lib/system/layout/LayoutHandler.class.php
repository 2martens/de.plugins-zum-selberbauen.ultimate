<?php
/**
 * Contains the LayoutHandler class.
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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.layout
 * @category	Ultimate CMS
 */
namespace ultimate\system\layout;
use wcf\system\cache\CacheHandler;
use wcf\system\SingletonFactory;
use wcf\util\StringUtil;

/**
 * Handles layouts.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.layout
 * @category	Ultimate CMS
 */
class LayoutHandler extends SingletonFactory {
	/**
	 * Represents the layout type of the index.
	 * @var integer
	 */
	const INDEX = 1;
	
	/**
	 * Represents the layout type of contents.
	 * @var integer
	 */
	const CONTENT = 2;
	
	/**
	 * Represents the layout type of pages.
	 * @var integer
	 */
	const PAGE = 3;
	
	/**
	 * Represents the layout type of categories.
	 * @var integer
	 */
	const CATEGORY = 4;
	
	/**
	 * Contains the default layouts.
	 * @var string[]
	 */
	protected $defaultLayouts = array(
		self::INDEX => 'ultimate.layout.index', 
		self::CONTENT => 'ultimate.layout.content', 
		self::PAGE => 'ultimate.layout.page', 
		self::CATEGORY => 'ultimate.layout.category'
	);
	
	/**
	 * Contains the read layouts.
	 * @var \ultimate\data\layout\Layout[]
	 */
	protected $layouts = array();
	
	/**
	 * Contains the read layouts with the names as key.
	 * @var \ultimate\data\layout\Layout[]
	 */
	protected $layoutsToLayoutName = array();
	
	/**
	 * Contains the read templates.
	 * @var \ultimate\data\template\Template[]
	 */
	protected $templatesToLayoutID = array();
	
	/**
	 * Returns the layout or null if there is no such layout.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$layoutName
	 * @return	\ultimate\data\layout\Layout|NULL
	 */
	public function getLayoutFromName($layoutName) {
		$layoutName = StringUtil::trim($layoutName);
		if (isset($this->layoutsToLayoutName[$layoutName])) {
			return $this->layoutsToLayoutName[$layoutName];
		}
		
		return null;
	}
	
	/**
	 * Returns the layout for the given layoutID or null if there is no such layout.
	 * 
	 * @since	1.0.0
	 * 
	 * @param	integer	$layoutID
	 * @return	\ultimate\data\layout\Layout|NULL
	 */
	public function getLayout($layoutID) {
		$layoutID = intval($layoutID);
		if (isset($this->layouts[$layoutID])) {
			return $this->layouts[$layoutID];
		}
		
		return null;
	}
	
	/**
	 * Returns a template for the given layout name or null if there is no such layout.
	 * 
	 * @since	1.0.0
	 * 
	 * @param	string	$layoutName
	 * @param	integer	$layoutType
	 * @throws	\wcf\system\exception\SystemException	on invalid layout type
	 * @return	\ultimate\data\template\Template|NULL
	 */
	public function getTemplateFromLayoutName($layoutName, $layoutType) {
		$layout = $this->getLayoutFromLayoutName($layoutName);
		if ($layout !== null) {
			$layoutID = $layout->__get('layoutID');
			if (isset($this->templatesToLayoutID[$layoutID])) {
				return $this->templatesToLayoutID[$layoutID];
			}
			else {
				if (!isset($this->defaultLayouts[integer($layoutType)])) {
					throw new SystemException('Invalid layout type', 0, 'You have to use a valid layout type (1, 2, 3 or 4).');
				}
				$layoutName = $this->defaultLayouts[integer($layoutType)];
				return $this->getLayoutFromTemplateName($layoutName, $layoutType);
			}
		}
		return null;
	}
	
	/**
	 * @since	1.0.0
	 * @see \wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->loadCache();
	}
	
	/**
	 * Loads the cache.
	 * 
	 * @since	1.0.0
	 */
	protected function loadCache() {
		$cacheName = 'layout';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\LayoutCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->layouts = CacheHandler::getInstance()->get($cacheName, 'layouts');
		$this->layoutsToLayoutName = CacheHandler::getInstance()->get($cacheName, 'layoutsToLayoutName');
		$this->templatesToLayoutID = CacheHandler::getInstance()->get($cacheName, 'templatesToLayoutID');
	}
}
