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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
namespace ultimate\system\blocktype;
use ultimate\data\content\CategorizedContent;
use ultimate\data\content\Content;
use ultimate\data\content\TaggedContent;
use ultimate\data\IUltimateData;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\comment\CommentHandler;
use wcf\system\language\I18nHandler;
use wcf\system\like\LikeHandler;
use wcf\system\request\LinkHandler;
use wcf\system\request\UltimateLinkHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Represents the content block type.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
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
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheBuilderClassName
	 */
	protected $cacheBuilderClassName = '\ultimate\system\cache\builder\ContentCacheBuilder';
	
	/**
	 * @see \ultimate\system\blocktype\AbstractBlockType::$cacheIndex
	 */
	protected $cacheIndex = 'contents';
	
	/**
	 * @see	\ultimate\system\blocktype\AbstractBlockType::$blockOptionIDs
	 */
	protected $blockOptionIDs = array(
		'queryMode_{$blockID}',
		'fetchPageContent_{$blockID}',
		'categories_{$blockID}',
		'categoryMode_{$blockID}',
		'authors_{$blockID}',
		'numberOfContents_{$blockID}',
		'offset_{$blockID}',
		'sortField_{$blockID}',
		'sortOrder_{$blockID}',
		'readMoreText_{$blockID}',
		'hideTitles_{$blockID}',
		'contentBodyDisplay_{$blockID}',
		'hideContent_{$blockID}',
		'commentsVisibility_{$blockID}',
		'featuredContents_{$blockID}',
		'hideInlineEdit_{$blockID}',
		'contentMetaDisplay_{$blockID}',
		'metaAboveContent_{$blockID}',
		'metaBelowContent_{$blockID}'
	);
	
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
	 * Contains the like data.
	 * @var \wcf\data\like\object\LikeObject[]
	 */
	protected $likeData = array();
	
	/**
	 * @see \ultimate\system\blocktype\IBlockType::readData()
	 */
	public function readData() {
		if (!empty($this->requestType) && $this->requestType != 'content' && $this->requestType != 'index') {
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
				if ($this->requestObject instanceof IUltimateData) {
					if ($this->requestObject instanceof CategorizedContent) {
						$this->requestObject = $this->requestObject->getDecoratedObject();
					}
					$this->requestObject = new TaggedContent($this->requestObject);
				}
				$this->contents[$this->requestObject->__get('contentID')] = $this->requestObject;
				break;
			case 'page':
				$content = $this->objects[$this->requestObject->__get('pageID')];
				$this->contents[$content->__get('contentID')] = $content;
				break;
			case 'index':
				$this->contents = $this->objects;
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
			// query options
			'fetchPageContent' => 'none',
			'categories' => array(),
			'categoryMode' => 'include',
			'authors' => array(),
			'numberOfContents' => 10,
			'offset' => 0,
			'sortField' => ULTIMATE_SORT_CONTENT_SORTFIELD,
			'sortOrder' => ULTIMATE_SORT_CONTENT_SORTORDER,
			// display options
			'readMoreText' => ULTIMATE_GENERAL_CONTENT_READMORETEXT,
			'hideTitles' => false,
			'contentBodyDisplay' => 'default',
			'hideContent' => false,
			'commentsVisibility' => 'auto',
			'featuredContents' => 1,
			'hideInlineEdit' => false,
			// meta options
			'contentMetaDisplay' => array(),
			'metaAboveContent' => ULTIMATE_GENERAL_CONTENT_METAABOVECONTENT,
			'metaBelowContent' => ULTIMATE_GENERAL_CONTENT_METABELOWCONTENT
		);
		$options = $this->block->__get('additionalData');
		$useDefaultReadMoreText = (!isset($options['readMoreText']));
		$useDefaultMetaAboveContent = (!isset($options['metaAboveContent']));
		$useDefaultMetaBelowContent = (!isset($options['metaBelowContent']));
		
		// if the default mode has been chosen, no variations to default shall be allowed
		if ($options['queryMode'] == 'default') {
			$options['fetchPageContent'] = 'none';
			$options['categories'] = array();
			$options['categoryMode'] = 'include';
			$options['authors'] = array();
			$options['numberOfContents'] = 10;
			$options['offset'] = 0;
			$options['sortField'] = ULTIMATE_SORT_CONTENT_SORTFIELD;
			$options['sortOrder'] = ULTIMATE_SORT_CONTENT_SORTORDER;
		}
		
		// convert to real value type
		$convertedOptions = array();
		foreach ($options as $optionName => $optionValue) {
			if ($optionName == 'hideTitles' || $optionName == 'hideContent' || $optionName == 'hideInlineEdit') {
				$convertedOptions[$optionName] = (boolean) intval($optionValue);
				continue;
			}
			if ($optionName == 'numberOfContents' || $optionName == 'offset' || $optionName == 'featuredContents') {
				$convertedOptions[$optionName] = intval($optionValue);
				continue;
			} 
			$convertedOptions[$optionName] = $optionValue;
		}
		
		$this->options = array_replace_recursive($defaults, $convertedOptions);
		
		if (isset($convertedOptions['categories'])) $this->options['categories'] = $convertedOptions['categories'];
		if (isset($convertedOptions['authors'])) $this->options['authors'] = $convertedOptions['authors'];
		if (isset($convertedOptions['contentMetaDisplay'])) $this->options['contentMetaDisplay'] = $convertedOptions['contentMetaDisplay'];
		
		// multilingual support for readMoreText, metaAboveContent and metaBelowContent
		$readMoreText = $this->options['readMoreText'];
		$metaAboveContent = $this->options['metaAboveContent'];
		$metaBelowContent = $this->options['metaBelowContent'];
		I18nHandler::getInstance()->register('readMoreText_'.$this->blockID);
		I18nHandler::getInstance()->register('metaAboveContent_'.$this->blockID);
		I18nHandler::getInstance()->register('metaBelowContent_'.$this->blockID);
		I18nHandler::getInstance()->setOptions('readMoreText_'.$this->blockID, PACKAGE_ID, $readMoreText, ($useDefaultReadMoreText ? 'wcf.acp.option.option.\d' : 'ultimate.block.content.\d+.readMoreText'));
		I18nHandler::getInstance()->setOptions('metaAboveContent_'.$this->blockID, PACKAGE_ID, $metaAboveContent, ($useDefaultMetaAboveContent ? 'wcf.acp.option.option.\d' : 'ultimate.block.content.\d+.metaAboveContent'));
		I18nHandler::getInstance()->setOptions('metaBelowContent_'.$this->blockID, PACKAGE_ID, $metaBelowContent, ($useDefaultMetaBelowContent ? 'wcf.acp.option.option.\d' : 'ultimate.block.content.\d+.metaBelowContent'));
		
		// check if content is attached to page
		if ($this->requestType == 'category' || $this->requestType == 'index') {
			$remainingContents = array();
			foreach ($this->contents as $contentID => $content) {
				$this->cacheBuilderClassName = '\ultimate\system\cache\builder\ContentPageCacheBuilder';
				$this->cacheIndex = 'contentIDsToPageID';
				$this->loadCache();
				if (!in_array($contentID, $this->objects)) {
					$remainingContents[$contentID] = $content;
				}
			}
			$this->contents = $remainingContents;
		}
		// fetchPageContent
		if ($this->options['fetchPageContent'] != 'none') {
			$pageID = $this->options['fetchPageContent'];
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
		
		// sort field
		if ($this->options['sortField'] != ULTIMATE_SORT_CONTENT_SORTFIELD) {
			$this->loadCache(true);
			$contents = array();
			foreach ($this->queryResult as $row) {
				if (isset($this->contents[$row['contentID']])) {
					$contents[$row['contentID']] = $this->contents[$row['contentID']];
				}
			}
			
			$this->contents = $contents;
		} else {
			// sort order
			if ($this->options['sortOrder'] != ULTIMATE_SORT_CONTENT_SORTORDER) {
				$this->contents = array_reverse($this->contents, true);
			}
		}
		
		// offset
		$offset = $this->options['offset'];
		if ($offset) {
			for ($offset; $offset > 0; $offset--) {
				array_shift($this->contents);
			}
		}
		
		// comments
		$this->objectTypeID = CommentHandler::getInstance()->getObjectTypeID($this->objectType);
		/* @var $objectType \wcf\data\object\type\ObjectType */
		$objectType = CommentHandler::getInstance()->getObjectType($this->objectTypeID);
		$this->commentManager = $objectType->getProcessor();
		foreach ($this->contents as $contentID => $content) {
			$this->commentLists[$contentID] = CommentHandler::getInstance()->getCommentList($this->commentManager, $this->objectTypeID,  $content->__get('contentID'));
		}
		
		// fetch likes
		if (MODULE_LIKE && !empty($this->contents)) {
			$contentIDs = array_keys($this->contents);
			$objectType = LikeHandler::getInstance()->getObjectType('de.plugins-zum-selberbauen.ultimate.likeableContent');
			LikeHandler::getInstance()->loadLikeObjects($objectType, $contentIDs);
			$this->likeData = LikeHandler::getInstance()->getLikeObjects($objectType);
		}
	}
	
	/**
	 * @see \ultimate\system\blocktype\IBlockType::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// dirty workaround to get the i18n values
		I18nHandler::getInstance()->assignVariables(false);
		
		$i18nPlainValues = WCF::getTPL()->get('i18nPlainValues');
		$i18nValues = WCF::getTPL()->get('i18nValues');
		
		// replacing variables in meta above/below content
		$metaAboveContent = (isset($i18nPlainValues['metaAboveContent_'.$this->blockID]) ? $i18nPlainValues['metaAboveContent_'.$this->blockID] : '');
		$metaBelowContent = (isset($i18nPlainValues['metaBelowContent_'.$this->blockID]) ? $i18nPlainValues['metaBelowContent_'.$this->blockID] : '');
		$metaAboveContent_i18n = (isset($i18nValues['metaAboveContent_'.$this->blockID]) ? $i18nValues['metaAboveContent_'.$this->blockID] : array());
		$metaBelowContent_i18n = (isset($i18nValues['metaBelowContent_'.$this->blockID]) ? $i18nValues['metaBelowContent_'.$this->blockID] : array());
		
		$metaAbove = array();
		$metaBelow = array();
		$metaAbove_i18n = array();
		$metaBelow_i18n = array();
		$readMoreLink = array();
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
			foreach ($tags as $languageID => $_tags) {
				$_languageID = WCF::getSession()->getLanguageID();
				if ($languageID != $_languageID) continue;
				foreach ($_tags as $tagID => $tag) {
					if (!empty($tagOutput)) $tagOutput .= ', ';
					$tagOutput .= LinkHandler::getInstance()->getLink('Tag', array(
						'tagSlug' => $tag->__get('tagSlug')
					), '');
				}
			}
			
			/* @var $dateTimeObject \DateTime */
			$dateTimeObject = $content->__get('publishDateObject');
			$dateLink = DateUtil::format($dateTimeObject, 'Y-m-d');
			
			// build readMore link
			$readMoreLink[$contentID] = UltimateLinkHandler::getInstance()->getLink(null, array(
				'date' => ''. $dateLink,
				'contentSlug' => $content->__get('contentSlug')
			));
			
			$timestamp = $content->__get('publishDate');
			$format = WCF::getLanguage()->getDynamicVariable('ultimate.date.dateFormat', array(
				'britishEnglish' => ULTIMATE_GENERAL_ENGLISHLANGUAGE
			));
			$date = DateUtil::format($dateTimeObject, $format);
			$time = DateUtil::format($dateTimeObject, DateUtil::TIME_FORMAT);
			$dateAndTime = $date.' '.$time;
			$dateString = '<time itemprop="datePublished" datetime="'.DateUtil::format($dateTimeObject, 'c').'" class="datetime" data-timestamp="'.$timestamp.'" data-date="'.$date.'" data-time="'.$time.'" data-offset="'.$dateTimeObject->getOffset().'">'.$date.'</time>';
			$timeString = '<time itemprop="datePublished" datetime="'.DateUtil::format($dateTimeObject, 'c').'" class="datetime" data-timestamp="'.$timestamp.'" data-date="'.$date.'" data-time="'.$time.'" data-offset="'.$dateTimeObject->getOffset().'">'.$time.'</time>';
			$dateAndTime = '<time itemprop="datePublished" datetime="'.DateUtil::format($dateTimeObject, 'c').'" class="datetime" data-timestamp="'.$timestamp.'" data-date="'.$date.'" data-time="'.$time.'" data-offset="'.$dateTimeObject->getOffset().'">'.$dateAndTime.'</time>';
			$authorUserProfile = new UserProfile($content->__get('author'));
			
			$author = '<div itemprop="creator" itemscope itemtype="http://schema.org/Person">
				<a href="'
				.LinkHandler::getInstance()->getLink(
					'User', 
					array(
						'object' => $authorUserProfile,
						'application' => 'ultimate'
					)
				).
				'" title="'.$authorUserProfile->username.'" class="framed">'
					.$authorUserProfile->getAvatar()->getImageTag(32).
				'</a>'.WCF::getLanguage()->getDynamicVariable(
							'ultimate.content.authorVCard', 
							array(
								'author' => $content->__get('author')->__get('username')
							)
						).
				'</div>';
			
			if ($metaAboveContent !== '') {
				$metaAbove[$contentID] = '';
				$__metaAboveContent = str_replace('$datetime', $dateAndTime, $metaAboveContent);
				$__metaAboveContent = str_replace('$date', $dateString, $__metaAboveContent);
				$__metaAboveContent = str_replace('$time', $timeString, $__metaAboveContent);
				$__metaAboveContent = str_replace('$comments', count($content->__get('comments')), $__metaAboveContent);
				$__metaAboveContent = str_replace('$author', $author, $__metaAboveContent);
				$__metaAboveContent = str_replace('$categories', $categoryOutput, $__metaAboveContent);
				$__metaAboveContent = str_replace('$tags', $tagOutput, $__metaAboveContent);
				$metaAbove[$contentID] = $__metaAboveContent;
			} elseif (!empty($metaAboveContent_i18n)) {
				$metaAbove_i18n[$contentID] = array();
				foreach ($metaAboveContent_i18n as $languageID => $_metaAboveContent) {
					$__metaAboveContent = str_replace('$datetime', $dateAndTime, $_metaAboveContent);
					$__metaAboveContent = str_replace('$date', $dateString, $__metaAboveContent);
					$__metaAboveContent = str_replace('$time', $timeString, $__metaAboveContent);
					$__metaAboveContent = str_replace('$comments', count($content->__get('comments')), $__metaAboveContent);
					$__metaAboveContent = str_replace('$author', $author, $__metaAboveContent);
					$__metaAboveContent = str_replace('$categories', $categoryOutput, $__metaAboveContent);
					$__metaAboveContent = str_replace('$tags', $tagOutput, $__metaAboveContent);
					$metaAbove_i18n[$contentID][$languageID] = $__metaAboveContent;
				}
			}
			
			if ($metaBelowContent !== '') {
				$metaBelow[$contentID] = '';
				$__metaBelowContent = str_replace('$datetime', $dateAndTime, $metaBelowContent);
				$__metaBelowContent = str_replace('$date', $dateString, $__metaBelowContent);
				$__metaBelowContent = str_replace('$time', $timeString, $__metaBelowContent);
				$__metaBelowContent = str_replace('$comments', count($content->__get('comments')), $__metaBelowContent);
				$__metaBelowContent = str_replace('$author', $author, $__metaBelowContent);
				$__metaBelowContent = str_replace('$categories', $categoryOutput, $__metaBelowContent);
				$__metaBelowContent = str_replace('$tags', $tagOutput, $__metaBelowContent);
				$metaBelow[$contentID] = $__metaBelowContent;
			} elseif (!empty($metaBelowContent_i18n)) {
				$metaBelow_i18n[$contentID] = array();
				foreach ($metaBelowContent_i18n as $languageID => $_metaBelowContent) {
					$__metaBelowContent = str_replace('$datetime', $dateAndTime, $_metaBelowContent);
					$__metaBelowContent = str_replace('$date', $date, $__metaBelowContent);
					$__metaBelowContent = str_replace('$time', $time, $__metaBelowContent);
					$__metaBelowContent = str_replace('$comments', count($content->__get('comments')), $__metaBelowContent);
					$__metaBelowContent = str_replace('$author', $author, $__metaBelowContent);
					$__metaBelowContent = str_replace('$categories', $categoryOutput, $__metaBelowContent);
					$__metaBelowContent = str_replace('$tags', $tagOutput, $__metaBelowContent);
					$metaBelow_i18n[$contentID][$languageID] = $__metaBelowContent;
				}
			}
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
		
		
		// assigning values
		WCF::getTPL()->assign('pages', $this->pages);
		WCF::getTPL()->assign('categories', $this->categories);
		WCF::getTPL()->assign('authors', $this->authors);
		
		WCF::getTPL()->assign(array(
			// settings
			'metaAbove' => $metaAbove,
			'metaBelow' => $metaBelow,
			'metaAbove_i18n' => $metaAbove_i18n,
			'metaBelow_i18n' => $metaBelow_i18n,
			'readMoreLink' => $readMoreLink,
			// contents
			'contents' => $this->contents,
			// comments
			'commentObjectTypeID' => $this->objectTypeID,
			'commentCanAdd' => $this->commentManager->canAdd(WCF::getUser()->__get('userID')),
			'commentsPerPage' => $this->commentManager->getCommentsPerPage(),
			'commentLists' => $this->commentLists,
			//like data
			'likeData' => $this->likeData,
			// dimensions and position
			'height' => $this->block->__get('height')
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
