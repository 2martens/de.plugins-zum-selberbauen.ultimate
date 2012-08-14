<?php
namespace ultimate\acp;
use wcf\system\io\File;
use wcf\system\WCF;

/**
 * Is called during installation of Ultimate CMS.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @category	Ultimate CMS
 */
final class InstallUltimateCMS {
	
	/**
	 * Creates a new InstallUltimateCMS object.
	 */
	public function __construct() {
		$this->install();
	}
	
	/**
	 * Installs important things.
	 */
	protected function install() {
		require_once(dirname(dirname(__FILE__)).'/config.inc.php');
		$this->createHtaccess();
	}
	
	/**
	 * Creates a htaccess file.
	 */
	protected function createHtaccess() {
		WCF::getTPL()->addTemplatePath(PACKAGE_ID, ULTIMATE_DIR.'acp/templates/');
		
		$output = WCF::getTPL()->fetch('htaccess');
		$file = new File(ULTIMATE_DIR.'.htaccess');
		$file->write($output);
		$file->close();
	}
	
}
new InstallUltimateCMS();
