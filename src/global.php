<?php
/**
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.cms
 * @category Ultimate CMS
 */
// include config
$packageDirs = array();
require_once(dirname(__FILE__).'/config.inc.php');

// include WCF
require_once(RELATIVE_WCF_DIR.'global.php');
if (!count($packageDirs)) $packageDirs[] = ULTIMATE_DIR;
$packageDirs[] = WCF_DIR;

// starting ultimate core
require_once(ULTIMATE_DIR.'lib/system/UltimateCMS.class.php');
new ultimate\system\UltimateCMS();
