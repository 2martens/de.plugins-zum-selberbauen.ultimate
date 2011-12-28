<?php
/**
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.cms
 * @category Ultimate CMS
 */
require_once('./global.php');
require_once(ULTIMATE_DIR.'lib/system/Dispatcher.class.php');
ultimate\system\Dispatcher::getInstance()->handle();
