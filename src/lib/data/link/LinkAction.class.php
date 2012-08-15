<?php
namespace ultimate\data\link;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes link-related functions.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.link
 * @category	Ultimate CMS
 */
class LinkAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	public $className = '\ultimate\data\link\LinkEditor';
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canAddLink');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	*/
	protected $permissionsDelete = array('admin.content.ultimate.canDeleteLink');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	*/
	protected $permissionsUpdate = array('admin.content.ultimate.canEditLink');
	
	/**
	 * Creates new link.
	 *
	 * @return	\ultimate\data\link\Link
	 */
	public function create() {
		$link = parent::create();
		$linkEditor = new LinkEditor($link);
	
		// insert categories
		$categoryIDs = (isset($this->parameters['categories'])) ? $this->parameters['categories'] : array();
		$linkEditor->addToCategories($categoryIDs, false);
	
		return $link;
	}
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::update()
	 */
	public function update() {
		if (isset($this->parameters['data'])) {
			parent::update();
		}
		else {
			if (empty($this->objects)) {
				$this->readObjects();
			}
		}
	
		$categoryIDs = (isset($this->parameters['categories'])) ? $this->parameters['categories'] : array();
		$removeCategories = (isset($this->parameters['removeCategories'])) ? $this->parameters['removeCategories'] : array();
		
		foreach ($this->objects as $linkEditor) {
			/* @var $linkEditor \ultimate\data\link\LinkEditor */
			if (!empty($categoryIDs)) {
				$linkEditor->addToCategories($categoryIDs);
			}
			
			if (!empty($removeCategories)) {
				$linkEditor->removeFromCategories($removeCategories);
			}
		}
	}
}
