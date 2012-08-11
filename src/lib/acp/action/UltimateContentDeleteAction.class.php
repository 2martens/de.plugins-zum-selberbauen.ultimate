<?php
namespace ultimate\acp\action;
use ultimate\data\content\ContentAction;
use wcf\action\AbstractSecureAction;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Deletes the specified content.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.action
 * @category	Ultimate CMS
 */
class UltimateContentDeleteAction extends AbstractSecureAction {
	/**
	 * @see	\wcf\action\AbstractAction::$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canDeleteContent'
	);
	
	/**
	 * Contains the content id.
	 * @var	integer
	 */
	public $contentID = 0;
	
	/**
	 * Contains the redirect url.
	 * @var	string
	 */
	protected $url = '';
	
	/**
	 * @see	\wcf\action\IAction::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		if (isset($_GET['id'])) $this->contentID = intval($_GET['id']);
		if (isset($_GET['url'])) $this->url = StringUtil::trim($_GET['url']);
	}
	
	/**
	 * @see	\wcf\action\IAction::execute()
	 */
	public function execute() {
		parent::execute();
		$contentAction = new ContentAction(array($this->contentID), 'delete');
		$contentAction->executeAction();
		$this->executed();
		
		// redirect
		HeaderUtil::redirect($this->url);
		exit;
	}
}
