<?php
/**
 * Contains the AbstractWidgetType class.
 * 
 * LICENSE:
 * This file is part of the Ultimate CMS.
 *
 * The Ultimate CMS is free software: you can redistribute it and/or modify
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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.widgettype
 * @category	Ultimate CMS
 */
namespace ultimate\system\widgettype;
use ultimate\data\widget\Widget;
use wcf\system\cache\CacheHandler;
use wcf\system\event\EventHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Abstract class for all widget types.
 * 
 * Use this class for creating own WidgetType classes. If you do that, you offer the chance for others to
 * modify and/or add functionality and you ensure that all methods of IWidgetType are implemented.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.widgettype
 * @category	Ultimate CMS
 */
abstract class AbstractWidgetType implements IWidgetType {
	/**
	 * Contains the template name.
	 * @var	string
	 */
	protected $templateName = '';
	
	/**
	 * Contains the widget options template name.
	 * @var string
	 */
	protected $widgetOptionsTemplateName = '';
	
	/**
	 * If true the template is used.
	 * @var boolean
	 */
	protected $useTemplate = true;
	
	/**
	 * Contains the request type.
	 * @var string
	 */
	protected $requestType = '';
	
	/**
	 * Contains the layout.
	 * @var \ultimate\data\layout\Layout
	 */
	protected $layout = null;
	
	/**
	 * Contains the widget id.
	 * @var integer
	 */
	protected $widgetID = 0;
	
	/**
	 * Contains a Widget object.
	 * @var \ultimate\data\widget\Widget
	 */
	protected $widget = null;
	
	/**
	 * Contains the cache name.
	 * @var	string
	 */
	protected $cacheName = '';
	
	/**
	 * Contains the CacheBuilder class name.
	 * @var	string
	 */
	protected $cacheBuilderClassName = '';
	
	/**
	 * Contains the cache index.
	 * @var	string
	 */
	protected $cacheIndex = '';
	
	/**
	 * Contains all read objects.
	 * @var object[]
	 */
	protected $objects = array();
	
	/**
	 * Creates a new Widget object.
	 *
	 * @internal The constructor does nothing and is final, because you can't control what the constructor
	 * should do. A subclass could easily overwrite this one and do some other stuff.
	 */
	public final function __construct() {}
	
	/**
	 * This method must be called before getHTML() or getOptionsHTML().<br /> 
	 * 
	 * @see \ultimate\system\widget\IWidget::init()
	 */
	public function init($widgetID) {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'init');
		
		$this->widgetID = intval($widgetID);
		$this->widget = new Widget($this->widgetID);
	}
	
	/**
	 * @internal Calls the method loadCache.
	 * @see \ultimate\system\widget\IWidget::readData()
	 */
	public function readData() {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'readData');
		$this->loadCache();
	}
	
	/**
	 * @see \ultimate\system\widget\IWidget::assignVariables()
	 */
	public function assignVariables() {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'assignVariables');
		WCF::getTPL()->assign(array(
			'widgetID' => $this->widgetID,
			'widget' => $this->widget,
			'requestType' => $this->requestType
		));
	}
	
	/**
	 * Returns the fetched template if $this->useTemplate is true and otherwise a string {include file='$this->templateName'}.<br />
	 * 
	 * @internal If you want to do more than fetching a template, you have to override this method.<br />
	 * Calls readData and assignVariables.
	 * @see \ultimate\system\widget\IWidget::getHTML()
	 */
	public function getHTML($requestType, \ultimate\data\layout\Layout $layout) {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'getHTML');
		// assigning object variables
		$this->requestType = StringUtil::trim($requestType);
		$this->layout = $layout;
		
		$this->readData();
		$this->assignVariables();
		
		// guess template name
		if (empty($this->templateName)) {
			$classParts = explode('\\', get_class($this));
			$className = array_pop($classParts);
			$this->templateName = lcfirst($className);
		}
		$output = '';
		// only fetch template if the template should be used
		if ($this->useTemplate) $output = WCF::getTPL()->fetch($this->templateName, 'ultimate');
		// otherwise include template
		else {
			$output = "{include file='".$this->templateName."'}";
		}
		return $output;
	}
	
	/**
	 * @internal If you want to do more than fetching a template, you have to override this method.<br />
	 * Calls readData and assignVariables.<br />
	 * You can't be sure that requestType and requestObject are set.
	 * @see \ultimate\system\widget\IWidget::getOptionsHTML()
	 */
	public function getOptionsHTML() {
		// fire event
		EventHandler::getInstance()->fireAction($this, 'getOptionsHTML');
		$this->readData();
		$this->assignVariables();
	
		// guess template name
		if (empty($this->widgetOptionsTemplateName)) {
			$classParts = explode('\\', get_class($this));
			$className = array_pop($classParts);
			$this->widgetOptionsTemplateName = str_replace('Type', 'Options', lcfirst($className));
		}
		$output = '';
		$output = WCF::getTPL()->fetch($this->widgetOptionsTemplateName, 'ultimate');
		return $output;
	}

	/**
	 * Returns variables.
	 *
	 * @since	1.0.0
	 *
	 * @param	string	$name
	 * @return	mixed|null	null if no fitting variable was found
	 */
	public function __get($name) {
		if (isset($this->{$name})) {
			return $this->{$name};
		}
	
		return null;
	}
	
	/**
	 * Loads the cache.
	 *
	 * Use this method instead of defining an own one. Each Widget should only need one kind of objects.
	 * 
	 * @since	1.0.0
	 */
	protected function loadCache() {
		// prevents error
		if (empty($this->cacheName)) return;
		$file = ULTIMATE_DIR.'cache/cache.'.$this->cacheName.'.php';
		CacheHandler::getInstance()->addResource($this->cacheName, $file, $this->cacheBuilderClassName);
		$this->objects = CacheHandler::getInstance()->get($this->cacheName, $this->cacheIndex);
	}
}
