<?php
/**
 * Contains the ContentBlockType class.
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
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
namespace ultimate\system\blocktype;
use wcf\system\comment\CommentHandler;

use ultimate\data\content\Content;
use wcf\data\user\UserList;
use wcf\system\language\I18nHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Represents the content block type.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
class ContentBlockType extends AbstractBlockType {
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$templateName
	 */
	protected $templateName = 'contentBlockType';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheName
	 */
	protected $cacheName = 'content';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheBuilderClassName
	 */
	protected $cacheBuilderClassName = '\ultimate\system\cache\builder\ContentCacheBuilder';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheIndex
	 */
	protected $cacheIndex = 'contents';
	
	/**
	 * Contains all read authors.
	 * @var \wcf\data\user\User[]
	 */
	protected $authors = array();
	
	/**
	 * Contains all read categories.
	 * @var	\ultimate\data\category\Category[]
	 */
	protected $categories = array();
	
	/**
	 * Contains all contents for the current request.
	 * @var \ultimate\data\content\TaggedContent[]
	 */
	protected $contents = array();
	
	/**
	 * Contains all read pages.
	 * @var \ultimate\data\page\Page[]
	 */
	protected $pages = array();
	
	/**
	 * Contains the options of the block.
	 * @var mixed[]
	 */
	protected $options = array();
	
	/**
	 * Contains the object type name for a comment.
	 * @var string
	 */
	protected $objectType = 'de.plugins-zum-selberbauen.ultimate.content.comment';
	
	/**
	 * Contains the id of the object type.
	 * @var integer
	 */
	protected $objectTypeID = 0;
	
	/**
	 * Contains a CommentManager object.
	 * @var \wcf\system\comment\manager\ICommentManager
	 */
	protected $commentManager = null;
	
	/**
	 * Contains a list of CommentList objects.
	 * @var \wcf\data\comment\StructuredCommentList[]
	 */
	protected $commentLists = array();
	
	/**
	 * @see \ultimate\system\blocktype\IBlockType::readData()
	 */
	public function readData() {
		if ($this->requestType != 'content') {
			$this->cacheName = 'contents-to-'.$this->requestType;
			$this->cacheBuilderClassName = '\ultimate\system\cache\builder\Content'.
				ucfirst($this->requestType).
				'CacheBuilder';
			$this->cacheIndex = 'contentsTo'.ucfirst($this->requestType).'ID';
		}
		parent::readData();
		
		// gathering contents which shall be displayed
		switch ($this->requestType) {
			case 'category':
				$this->contents = $this->objects[$this->requestObject->__get('categoryID')];
				break;
			case 'content':
				$this->contents[$this->requestObject->__get('contentID')] = $this->requestObject;
				break;
			case 'page':
				$content = $this->objects[$this->requestObject->__get('pageID')];
				$this->contents[$content->__get('contentID')] = $content;
				break;
		}
		
		// read further cache
		$this->cacheName = 'page';
		$this->cacheBuilderClassName = '\ultimate\system\cache\builder\PageCacheBuilder';
		$this->cacheIndex = 'pages';
		$this->loadCache();
		$this->pages = $this->objects;
		
		$this->cacheName = 'category';
		$this->cacheBuilderClassName = '\ultimate\system\cache\builder\CategoryCacheBuilder';
		$this->cacheIndex = 'categories';
		$this->loadCache();
		$this->categories = $this->objects;
		
		$this->cacheName = 'author';
		$this->cacheBuilderClassName = '\ultimate\system\cache\builder\AuthorCacheBuilder';
		$this->cacheIndex = 'authors';
		$this->loadCache();
		$this->authors = $this->objects;
		
		// default values
		$defaults = array(
			'queryMode' => 'default',
			'fetchPageContent' => 'none',
			'categories' => array(),
			'categoryMode' => 'include',
			'authors' => array(),
			'numberOfContents' => 10,
			'offset' => 0,
			'sortField' => ULTIMATE_SORT_CONTENT_SORTFIELD,
			'sortOrder' => ULTIMATE_SORT_CONTENT_SORTORDER,
			'readMoreText' => ULTIMATE_GENERAL_CONTENT_READMORETEXT,
			'showTitles' => true,
			'contentBodyDisplay' => 'default',
			'showContent' => true,
			'commentsVisibility' => 'auto',
			'featuredContents' => 1,
			'showInlineEdit' => true,
			'contentMetaDisplay' => array(),
			'metaAboveContent' => ULTIMATE_GENERAL_CONTENT_METAABOVECONTENT,
			'metaBelowContent' => ULTIMATE_GENERAL_CONTENT_METABELOWCONTENT
		);
		$options = $this->block->__get('additionalData');
		$useDefaultReadMoreText = (!isset($options['readMoreText']));
		$useDefaultMetaAboveContent = (!isset($options['metaAboveContent']));
		$useDefaultMetaBelowContent = (!isset($options['metaBelowContent']));
		$this->options = array_merge_recursive($defaults, $options);
		
		// multilingual support for readMoreText
		$readMoreText = $this->options['readMoreText'];
		$metaAboveContent = $this->options['metaAboveContent'];
		$metaBelowContent = $this->options['metaBelowContent'];
		I18nHandler::getInstance()->register('readMoreText');
		I18nHandler::getInstance()->register('metaAboveContent');
		I18nHandler::getInstance()->register('metaBelowContent');
		I18nHandler::getInstance()->setOptions('readMoreText', PACKAGE_ID, $readMoreText, ($useDefaultReadMoreText ? 'wcf.acp.option.option.\d' : 'ultimate.block.content.\d+.readMoreText'));
		I18nHandler::getInstance()->setOptions('metaAboveContent', PACKAGE_ID, $metaAboveContent, ($useDefaultMetaAboveContent ? 'wcf.acp.option.option.\d' : 'ultimate.block.content.\d+.metaAboveContent'));
		I18nHandler::getInstance()->setOptions('metaBelowContent', PACKAGE_ID, $metaBelowContent, ($useDefaultMetaBelowContent ? 'wcf.acp.option.option.\d' : 'ultimate.block.content.\d+.metaBelowContent'));
		
		// fetchPageContent
		if ($this->options['fetchPageContent'] != 'none') {
			$pageID = $this->options['fetchPageContent'];
			$this->cacheName = 'page';
			$this->cacheBuilderClassName = '\ultimate\system\cache\builder\ContentPageCacheBuilder';
			$this->cacheIndex = 'contentsToPageID';
			$this->loadCache();
			$content = $this->objects[$pageID];
			$this->contents = array();
			$this->contents[$content->__get('contentID')] = $content;
		}
		
		// include/exclude categories
		if (!empty($this->options['categories'])) {
			$mode = $this->options['categoryMode'];
			$categories = $this->options['categories'];
			$categoriesOut = array();
			foreach ($categories as $categoryID) {
				$categoriesOut[$categoryID] = $this->categories[$categoryID];
			}
			// determine remaining contents
			$allowedContents = array();
			foreach ($this->contents as $contentID => $content) {
				$notExcluded = false;
				foreach ($content->__get('categories') as $categoryID => $category) {
					if ($mode == 'include' && !isset($categoriesOut[$categoryID])) {
						$notExcluded = false;
						continue;
					}
					if ($mode == 'exclude' && isset($categoriesOut[$categoryID])) {
						$notExcluded = false;
						break;
					}
					$notExcluded = true;
				}
				if ($notExcluded) {
					$allowedContents[$contentID] = $content;
				}
			}
			$this->contents = $allowedContents;
		}
		
		// authors
		if (!empty($this->options['authors'])) {
			$authors = $this->options['authors'];
			$authorsOut = array();
			foreach ($authors as $authorID) {
				$authorOut[$authorID] = $this->authors[$authorID];
			}
			$allowedContents = array();
			foreach ($this->contents as $contentID => $content) {
				if (!isset($authorsOut[$content->__get('authorID')])) continue;
				$allowedContents[$contentID] = $content;
			}
			$this->contents = $allowedContents;
		}
		
		// offset
		$offset = $this->options['offset'];
		if ($offset) {
			for ($offset; $offset > 0; $offset--) {
				array_shift($this->contents);
			}
		}
		
		// number of contents
		$amountOfContents = count($this->contents); // get remaining amount of to be displayed contents
		$numberOfContents = $this->options['numberOfContents'];
		// if we don't have that much contents it would be senseless to run a loop
		if ($numberOfContents < $amountOfContents) {
			$remainingContents = array();
			for ($i = $numberOfContents; $i > 0; $i--) {
				$tmpContent = array_shift($this->contents);
				$remainingContents[$tmpContent->__get('contentID')] = $tmpContent;
			}
			$this->contents = $remainingContents;
		}
		
		// sort order
		if ($this->options['sortOrder'] != ULTIMATE_SORT_CONTENT_SORTORDER) {
			$this->contents = array_reverse($this->contents, true);
		}
		
		// sort field
		if ($this->options['sortField'] != ULTIMATE_SORT_CONTENT_SORTFIELD) {
			$this->loadCache(true);
			$contents = array();
			foreach ($this->queryResult as $row) {
				$contents[$row['contentID']] = new Content(null, null, $row);
			}
			$this->contents = $contents;
		}
		
		// comments
		$this->objectTypeID = CommentHandler::getInstance()->getObjectTypeID($this->objectType);
		/* @var $objectType \wcf\data\object\type\ObjectType */
		$objectType = CommentHandler::getInstance()->getObjectType($this->objectTypeID);
		$this->commentManager = $objectType->getProcessor();
		foreach ($this->contents as $contentID => $content) {
			$this->commentList[$contentID] = CommentHandler::getInstance()->getCommentList($this->objectTypeID, $this->commentManager, $content->__get('authorID'));
		}
	}
	
	/**
	 * @see \ultimate\system\blocktype\IBlockType::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// replacing variables in meta above/below content
		$metaAboveContent = $this->options['metaAboveContent'];
		$metaBelowContent = $this->options['metaBelowContent'];
		$metaAbove = array();
		$metaBelow = array();
		foreach ($this->contents as $contentID => $content) {
			// get category output
			$categories = $content->__get('categories');
			$categoryOutput = '';
			foreach ($categories as $categoryID => $category) {
				if (!empty($categoryOutput)) $categoryOutput .= ', ';
				$categoryOutput .= LinkHandler::getInstance()->getLink('Category', array(
					'categorySlug' => $category->__get('categorySlug')
				), '');
			}
			
			// get tag output
			$tags = $content->__get('tags');
			$tagOutput = '';
			foreach ($tags as $tagID => $tag) {
				if (!empty($tagOutput)) $tagOutput .= ', ';
				$tagOutput .= LinkHandler::getInstance()->getLink('Tag', array(
					'tagSlug' => $tag->__get('tagSlug')
				), '');
			}
			
			/* @var $dateTimeObject \DateTime */
			$dateTimeObject = $content->__get('publishDateObj');
			$timestamp = $content->__get('publishDate');
			$date = DateUtil::format($dateTimeObject, 'ultimate.date.dateFormat');
			$time = DateUtil::format($dateTimeObject, DateUtil::TIME_FORMAT);
			$date = '<time datetime="'.DateUtil::format($dateTimeObject, 'c').'" class="datetime" data-timestamp="'.$timestamp.'" data-date="'.$date.'" data-time="'.$time.'" data-offset="'.$dateTimeObject->getOffset().'">'.$date.'</time>';
			
			$__metaAboveContent = str_replace('$date', $date, $metaAboveContent);
			$__metaAboveContent = str_replace('$time', $time, $__metaAboveContent);
			$__metaAboveContent = str_replace('$comments', count($content->__get('comments')), $__metaAboveContent);
			$__metaAboveContent = str_replace('$author', $content->__get('author')->__get('username'), $__metaAboveContent);
			$__metaAboveContent = str_replace('$categories', $categoryOutput, $__metaAboveContent);
			$__metaAboveContent = str_replace('$tags', $tagOutput, $__metaAboveContent);
			$metaAbove[$contentID] = $__metaAboveContent;
			
			$__metaBelowContent = str_replace('$date', $date, $metaBelowContent);
			$__metaBelowContent = str_replace('$time', $time, $__metaBelowContent);
			$__metaBelowContent = str_replace('$comments', count($content->__get('comments')), $__metaBelowContent);
			$__metaBelowContent = str_replace('$author', $content->__get('author')->__get('username'), $__metaBelowContent);
			$__metaBelowContent = str_replace('$categories', $categoryOutput, $__metaBelowContent);
			$__metaBelowContent = str_replace('$tags', $tagOutput, $__metaBelowContent);
			$metaBelow[$contentID] = $__metaBelowContent;
		}
		
		$optionsSelect = array(
			'queryMode',
			'fetchPageContent',
			'categories',
			'categoryMode',
			'authors',
			'sortField',
			'sortOrder',
			'contentBodyDisplay',
			'commentsVisibility',
			'contentMetaDisplay'
		);
		
		// gathering dimensions and position
		$dimensions = explode(',', $this->block->__get('dimensions'));
		$position = explode(',', $this->block->__get('position'));
		
		// assigning values
		WCF::getTPL()->assign('pages', $this->pages);
		WCF::getTPL()->assign('categories', $this->categories);
		WCF::getTPL()->assign('authors', $this->authors);
		
		WCF::getTPL()->assign(array(
			// settings
			'metaAbove' => $metaAbove,
			'metaBelow' => $metaBelow,
			// contents
			'contents' => $this->contents,
			// comments
			'commentObjectTypeID' => $this->objectTypeID,
			'commentCanAdd' => $this->commentManager->canAdd(),
			'commentsPerPage' => $this->commentManager->commentsPerPage(),
			'commentLists' => $this->commentLists,
			// dimensions and position
			'height' => $dimensions[1],
			'width' => $dimensions[0],
			'top' => $position[1],
			'left' => $position[0]
		));
		
		$useRequestData = false;
		I18nHandler::getInstance()->assignVariables($useRequestData);
		
		// assigning option values
		foreach ($this->options as $optionName => $optionValue) {
			if (in_array($optionName, $optionsSelect)) {
				if ($optionName == 'contentMetaDisplay') {
					$tmpArray = array();
					foreach ($optionValue as $option) {
						$tmpArray[$option] = true;
					}
					WCF::getTPL()->assign($optionName.'Selected', $tmpArray);
				} 
				else {
					WCF::getTPL()->assign($optionName.'Selected', $optionValue);
				}
			}
			else {
				WCF::getTPL()->assign($optionName, $optionValue);
			}
		}
	}
}
