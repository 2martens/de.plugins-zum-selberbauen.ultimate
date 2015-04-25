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
use ultimate\data\menu\item\MenuItemList;
use ultimate\data\page\language\PageLanguageEntryEditor;
use ultimate\data\page\PageList;
use wcf\system\io\File;
use wcf\system\WCF;

// remove ULTIMATE headInclude template
unlink(ULTIMATE_DIR.'templates/headInclude.tpl');

$languages = WCF::getLanguage()->getLanguages();

// menu items
$menuItemList = new MenuItemList();
$menuItemList->readObjects();
/** @var \ultimate\data\menu\item\MenuItem[] $menuItems */
$menuItems = $menuItemList->getObjects();
$objectIDToMenuItemID = array();
$menuItemNameToMenuItemID = array();

// category language
$categoryList = new CategoryList();
$categoryList->readObjects();
/** @var \ultimate\data\category\Category[] $categories */
$categories = $categoryList->getObjects();

foreach ($categories as $categoryID => $category) {
	$data = array();
	$neutralCategoryTitle = false;
	$neutralCategoryDescription = false;
	$neutralTitle = '';
	$neutralDescription = '';
	$rawCategoryTitle = $category->categoryTitle;
	$rawCategoryDescription = $category->categoryDescription;

	// check menu item relation
	foreach ($menuItems as $menuItemID => $menuItem) {
		$menuItemName = $menuItem->menuItemName;
		if ($menuItemName != $rawCategoryTitle) {
			continue;
		}
		$objectIDToMenuItemID[$menuItemID] = $categoryID;
		$menuItemNameToMenuItemID[$menuItemID] = $category->categorySlug;
	}

	foreach ($languages as $languageID => $language) {
		/** @var \wcf\data\language\Language $language */
		$data[$languageID] = array();
		$categoryTitle = $language->get($rawCategoryTitle);
		$neutralCategoryTitle = ($rawCategoryTitle == $categoryTitle);
		if (!$neutralCategoryTitle) {
			$data[$languageID]['categoryTitle'] = $categoryTitle;
		}
		else {
			$neutralTitle = $categoryTitle;
		}

		$categoryDescription = $language->get($rawCategoryDescription);
		$neutralCategoryDescription = ($rawCategoryDescription == $categoryDescription);
		if (!$neutralCategoryDescription) {
			$data[$languageID]['categoryDescription'] = $categoryDescription;
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
	$rawContentTitle = $content->contentTitle;
	$rawContentDescription = $content->contentDescription;

	// check menu item relation
	foreach ($menuItems as $menuItemID => $menuItem) {
		$menuItemName = $menuItem->menuItemName;
		if ($menuItemName != $rawContentTitle) {
			continue;
		}
		$objectIDToMenuItemID[$menuItemID] = $contentID;
		$menuItemNameToMenuItemID[$menuItemID] = $content->contentSlug;
	}

	foreach ($languages as $languageID => $language) {
		/** @var \wcf\data\language\Language $language */
		$languageData[$languageID] = array();		
		$contentTitle = $language->get($rawContentTitle);
		$neutralContentTitle = ($rawContentTitle == $contentTitle);
		if (!$neutralContentTitle) {
			$languageData[$languageID]['contentTitle'] = $contentTitle;
		}
		else {
			$neutralTitle = $contentTitle;
		}
		
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
	$rawPageTitle = $page->pageTitle;

	// check menu item relation
	foreach ($menuItems as $menuItemID => $menuItem) {
		$menuItemName = $menuItem->menuItemName;
		if ($menuItemName != $rawPageTitle) {
			continue;
		}
		$objectIDToMenuItemID[$menuItemID] = $pageID;
		$menuItemNameToMenuItemID[$menuItemID] = $page->pageSlug;
	}
	
	foreach ($languages as $languageID => $language) {
		/** @var \wcf\data\language\Language $language */
		$data[$languageID] = array();
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

// change database entries
$sql = 'UPDATE ultimate'.WCF_N.'_menu_item SET objectID = ?, menuItemName = ? WHERE menuItemID = ?';
$statement = WCF::getDB()->prepareStatement($sql);
WCF::getDB()->beginTransaction();
foreach ($objectIDToMenuItemID as $menuItemID => $objectID) {
	$statement->execute(array($objectID, $menuItemNameToMenuItemID[$menuItemID], $menuItemID));
}
WCF::getDB()->commitTransaction();
