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
	
	foreach ($languages as $languageID => $language) {
		/** @var \wcf\data\language\Language $language */
		$categoryTitle = $language->get($category->categoryTitle);
		$categoryDescription = $language->get($category->categoryDescription);
		$data[$languageID] = array(
			'categoryTitle' => $categoryTitle,
			'categoryDescription' => $categoryDescription
		);
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

	foreach ($languages as $languageID => $language) {
		/** @var \wcf\data\language\Language $language */
		$contentTitle = $language->get($content->contentTitle);
		$contentDescription = $language->get($content->contentDescription);
		$languageData[$languageID] = array(
			'contentTitle' => $contentTitle,
			'contentDescription' => $contentDescription
		);
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

	foreach ($languages as $languageID => $language) {
		/** @var \wcf\data\language\Language $language */
		$pageTitle = $language->get($page->pageTitle);
		$data[$languageID] = array(
			'pageTitle' => $pageTitle
		);
	}

	PageLanguageEntryEditor::createEntries($pageID, $data);
}
