<?php
/**
 * Contains the ContentListPage class.
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
 * @subpackage	page
 * @category	Ultimate CMS
 */
namespace ultimate\page;
use wcf\page\AbstractCachedListPage;
use wcf\system\WCF;

/**
 * Provides a list of contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	page
 * @category	Ultimate CMS
 */
class ContentListPage extends AbstractCachedListPage implements IEditSuitePage {
	/**
	 * name of the template for the called page
	 * @var	string
	 */
	public $templateName = 'editSuite';
	
	/**
	 * indicates if you need to be logged in to access this page
	 * @var	boolean
	 */
	public $loginRequired = true;
	
	/**
	 * enables template usage
	 * @var	string
	 */
	public $useTemplate = true;
	
	/**
	 * The object list class name.
	 * @var	string
	 */
	public $objectListClassName = '\ultimate\data\content\ContentList';
	
	/**
	 * Array of valid sort fields.
	 * @var	string[]
	 */
	public $validSortFields = array(
		'contentID',
		'contentTitle',
		'contentAuthor',
		'publishDate',
		'lastModified'
	);
	
	/**
	 * The default sort order.
	 * @var	string
	*/
	public $defaultSortOrder = ULTIMATE_SORT_CONTENT_SORTORDER;
	
	/**
	 * The default sort field.
	 * @var	string
	 */
	public $defaultSortField = ULTIMATE_SORT_CONTENT_SORTFIELD;
	
	/**
	 * Contains the fully qualified name of the CacheBuilder.
	 * @var string
	 */
	public $cacheBuilderClassName = '\ultimate\system\cache\builder\ContentCacheBuilder';
	
	/**
	 * The cache index.
	 * @see \wcf\page\AbstractCachedListPage::$cacheIndex
	 */
	public $cacheIndex = 'contents';
	
	/**
	 * The object decorator class name.
	 * @var string
	 */
	public $objectDecoratorClass = '\ultimate\data\content\TaggedContent';
	
	/**
	 * A list of active EditSuite menu items.
	 * @var string[]
	 */
	protected $activeMenuItems = array(
		'ContentListPage',
		'ultimate.edit.contents'
	);
	
	/**
	 * @see \ultimate\page\IEditSuitePage::getActiveMenuItems()
	 */
	public function getActiveMenuItems() {
		return $this->activeMenuItems;
	}
	
	/**
	 * @see \wcf\page\AbstractCachedListPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'activeMenuItems' => $this->activeMenuItems,
			'pageContent' => WCF::getTPL()->fetch('__editSuite.ContentListPage', 'ultimate')
		));
	}
	
	/**
	 * Shows the requested page.
	 */
	public function show() {
		parent::show();
		if (!$this->useTemplate) {
			WCF::getTPL()->display($this->templateName, 'ultimate', false);
		}
	}
}
