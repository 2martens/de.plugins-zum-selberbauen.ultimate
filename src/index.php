<?php
/**
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @category Ultimate CMS
 */
require_once('./global.php');
require_once(ULTIMATE_DIR.'lib/system/Dispatcher.class.php');
define('DEBUG', true); //@todo: change into false for production
if (isset($_GET['request'])) {
    \ultimate\system\Dispatcher::getInstance()->handle();
} else {
    \wcf\system\request\RequestHandler::getInstance()->handle('ultimate');
}
