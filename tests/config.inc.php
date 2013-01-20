<?php
if (!defined('TEST_DIR')) {
	define('TEST_DIR', dirname(__FILE__).'/');
}
require_once(TEST_DIR.'../src/global.php');
require_once(TEST_DIR.'UltimateTestCore.class.php');
spl_autoload_register(array('ultimate\tests\UltimateTestCore', 'autoload'));

new \ultimate\tests\UltimateTestCore();