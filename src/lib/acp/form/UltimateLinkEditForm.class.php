<?php
namespace ultimate\acp\form;
use wcf\form\AbstractForm;

use ultimate\data\link\CategorizedLink;
use ultimate\data\link\Link;
use wcf\system\WCF;

/**
 * Shows the UltimateLinkEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateLinkEditForm extends UltimateLinkAddForm {
	/**
	 * @var	string[]
	 * @see	\wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.link';
	
	/**
	 * Contains the link id.
	 * @var	integer
	 */
	public $linkID = 0;
	
	/**
	 * Contains the link object.
	 * @var \ultimate\data\link\CategorizedLink
	 */
	public $link = null;
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['id'])) $this->linkID = intval($_REQUEST['id']);
		$link = new CategorizedLink(new Link($this->linkID));
		if (!$link->__get('linkID')) {
			throw new IllegalLinkException();
		}
		
		$this->link = $link;
	}
	
	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		$this->linkName = $this->link->__get('linkName');
		$this->linkDescription = $this->link->__get('linkDescription');
		$this->linkURL = $this->link->__get('linkURL');
		$this->categoryIDs = array_keys($this->link->__get('categories'));
		parent::readData();
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();
		
		$this->linkName = 'ultimate.link.'.$this->linkID.'.linkName';
		if (I18nHandler::getInstance()->isPlainValue('linkName')) {
			I18nHandler::getInstance()->remove($this->linkName, PACKAGE_ID);
			$this->linkName = I18nHandler::getInstance()->getValue('linkName');
		} else {
			I18nHandler::getInstance()->save('linkName', $this->linkName, 'ultimate.link', PACKAGE_ID);
		}
		
		$this->linkDescription = 'ultimate.link.'.$this->linkID.'.linkDescription';
		if (I18nHandler::getInstance()->isPlainValue('linkDescription')) {
			I18nHandler::getInstance()->remove($this->linkDescription, PACKAGE_ID);
			$this->linkDescription = I18nHandler::getInstance()->getValue('linkDescription');
		} else {
			I18nHandler::getInstance()->save('linkDescription', $this->linkDescription, 'ultimate.link', PACKAGE_ID);
		}
		
		$parameters = array(
			'data' => array(
				'linkName' => $this->linkName,
				'linkDescription' => $this->linkDescription,
				'linkURL' => $this->linkURL
			),
			'categories' => $this->categoryIDs
		);
		
		$this->objectAction = new LinkAction(array(), 'update', $parameters);
		$this->objectAction->executeAction();
		
		$this->saved();
		
		WCF::getTPL()->assign(
			'success', true
		);
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$useRequestData = (!empty($_POST)) ? true : false;
		I18nHandler::getInstance()->assignVariables($useRequestData);
		
		WCF::getTPL()->assign(array(
			'linkID' => $this->linkID,
			'action' => 'edit'
		));
	}
}
