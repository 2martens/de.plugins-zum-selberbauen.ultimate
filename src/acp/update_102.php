<?php
/**
 * Contains the update script for the update from 1.0.2 to 1.1.0.
 *
 * LICENSE:
 * This file is part of the Ultimate CMS.
 *
 * Foobar is free software: you can redistribute it and/or modify
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
 * @category	Ultimate CMS
 */

use ultimate\data\category\language\CategoryLanguageEntryEditor;
use ultimate\data\category\CategoryList;
use ultimate\data\content\language\ContentLanguageEntryEditor;
use ultimate\data\content\ContentEditor;
use ultimate\data\content\ContentList;
use ultimate\data\page\language\PageLanguageEntryEditor;
use ultimate\data\page\PageList;
use wcf\system\WCF;

$languages = WCF::getLanguage()->getLanguages();

// category language

$categoryList = new CategoryList();
$categoryList->readObjects();
$categories = $categoryList->getObjects();

foreach ($categories as $categoryID => $category) {
	/** @var \ultimate\data\category\Category $category */
	$data = array();
	$neutralCategoryTitle = false;
	$neutralCategoryDescription = false;
	$neutralTitle = '';
	$neutralDescription = '';

	foreach ($languages as $languageID => $language) {
		/** @var \wcf\data\language\Language $language */
		$data[$languageID] = array();
		$rawCategoryTitle = $category->categoryTitle;
		$categoryTitle = $language->get($rawCategoryTitle);
		$neutralCategoryTitle = ($rawCategoryTitle == $categoryTitle);
		if (!$neutralCategoryTitle) {
			$data[$languageID]['categoryTitle'] = $categoryTitle;
		}
		else {
			$neutralTitle = $categoryTitle;
		}

		$rawCategoryDescription = $category->categoryDescription;
		$categoryDescription = $language->get($rawCategoryDescription);
		$neutralCategoryDescription = ($rawCategoryDescription == $categoryDescription);
		if (!$neutralCategoryDescription) {
			$languageData[$languageID]['categoryDescription'] = $categoryDescription;
		}
		else {
			$neutralDescription = $categoryDescription;
		}
	}

	$data[0] = array();
	if ($neutralCategoryTitle) {
		$data[0]['categoryTitle'] = $neutralTitle;
	}
	if ($neutralCategoryDescription) {
		$data[0]['categoryDescription'] = $neutralDescription;
	}
	
	CategoryLanguageEntryEditor::createEntries($categoryID, $data);
}

// content versions and language
$contentList = new ContentList();
$contentList->readObjects();
$contents = $contentList->getObjects();

foreach ($contents as $contentID => $content) {
	/** @var \ultimate\data\content\Content $content */
	$versionData = array(
		'authorID' => $content->authorID,
		'attachments' => $content->attachments,
		'enableBBCodes' => $content->enableBBCodes,
		'enableHtml' => $content->enableHtml,
		'enableSmilies' => $content->enableSmilies,
		'publishDate' => $content->publishDate,
		'status' => $content->status
	);
	$languageData = array();
	
	$contentEditor = new ContentEditor($content);
	/** @var \ultimate\data\content\version\ContentVersion $version */
	$version = $contentEditor->createVersion($versionData);
	$neutralContentTitle = false;
	$neutralContentDescription = false;
	$neutralContentText = false;
	$neutralTitle = '';
	$neutralDescription = '';
	$neutralText = '';

	foreach ($languages as $languageID => $language) {
		/** @var \wcf\data\language\Language $language */
		$languageData[$languageID] = array();
		$rawContentTitle = $content->contentTitle;
		$contentTitle = $language->get($rawContentTitle);
		$neutralContentTitle = ($rawContentTitle == $contentTitle);
		if (!$neutralContentTitle) {
			$languageData[$languageID]['contentTitle'] = $contentTitle;
		}
		else {
			$neutralTitle = $contentTitle;
		}

		$rawContentDescription = $content->contentDescription;
		$contentDescription = $language->get($rawContentDescription);
		$neutralContentDescription = ($rawContentDescription == $contentDescription);
		if (!$neutralContentDescription) {
			$languageData[$languageID]['contentDescription'] = $contentDescription;
		}
		else {
			$neutralDescription = $contentDescription;
		}

		$rawContentText = $content->contentText;
		$contentText = $language->get($rawContentText);
		$neutralContentText = ($rawContentText == $contentText);
		if (!$neutralContentText) {
			$languageData[$languageID]['contentText'] = $contentText;
		}
		else {
			$neutralText = $contentText;
		}
	}
	
	$languageData[0] = array();
	if ($neutralContentTitle) {
		$languageData[0]['contentTitle'] = $neutralTitle;
	}

	if ($neutralContentDescription) {
		$languageData[0]['contentDescription'] = $neutralDescription;
	}

	if ($neutralContentText) {
		$languageData[0]['contentText'] = $neutralText;
	}
	
	ContentLanguageEntryEditor::createEntries($version->versionID, $languageData);
}

// page language
$pageList = new PageList();
$pageList->readObjects();
$pages = $pageList->getObjects();

foreach ($pages as $pageID => $page) {
	/** @var \ultimate\data\page\Page $page */
	$data = array();
	$neutralPageTitle = false;
	$neutralTitle = '';
	
	foreach ($languages as $languageID => $language) {
		/** @var \wcf\data\language\Language $language */
		$data[$languageID] = array();
		$rawPageTitle = $page->pageTitle;
		$pageTitle = $language->get($rawPageTitle);
		$neutralPageTitle = ($rawPageTitle == $pageTitle);
		if (!$neutralPageTitle) {
			$data[$languageID]['pageTitle'] = $pageTitle;
		}
		else {
			$neutralTitle = $pageTitle;
		}
	}
	
	if ($neutralPageTitle) {
		$data[0] = array(
			'pageTitle' => $neutralTitle
		);
	}	

	PageLanguageEntryEditor::createEntries($pageID, $data);
}
