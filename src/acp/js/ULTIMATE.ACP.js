/**
 * Class and function collection for ULTIMATE CMS ACP
 * 
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 */

/**
 * Initialize ULTIMATE.ACP namespace
 */
ULTIMATE.ACP = {};

/**
 * Namespace for ULTIMATE.ACP.Category
 */
ULTIMATE.ACP.Category = {};

/**
 * ContentList clipboard API
 */
ULTIMATE.ACP.Category.List = function() { this.init(); };
ULTIMATE.ACP.Category.List.prototype = {
	/**
	 * Initializes the CategoryList clipboard API.
	 */
	init: function() {
		$('body').bind('clipboardAction', $.proxy(this.handleClipboardEvent, this));
	},
	
	/**
	 * Event handler for clipboard editor item actions.
	 */
	handleClipboardEvent: function(event, type, actionName) {
		// ignore unrelated events
		if ((type != 'de.plugins-zum-selberbauen.ultimate.category') || (actionName != 'category.delete')) return;
		
		var $item = $(event.target);
		this._delete($item);
	},
	
	/**
	 * Handle delete action.
	 * 
	 * @param	jQuery		item
	 */
	_delete: function(item) {
		var $confirmMessage = item.data('internalData')['confirmMessage'];
		if (confirm($confirmMessage)) {
			WCF.Clipboard.sendRequest(item);
		}
	}
};

/**
 * Namespace for ULTIMATE.ACP.Content
 */
ULTIMATE.ACP.Content = {};

/**
 * ContentList clipboard API
 */
ULTIMATE.ACP.Content.List = function() { this.init(); };
ULTIMATE.ACP.Content.List.prototype = {
	/**
	 * Initializes the ContentList clipboard API.
	 */
	init: function() {
		$('body').bind('clipboardAction', $.proxy(this.handleClipboardEvent, this));
	},
	
	/**
	 * Event handler for clipboard editor item actions.
	 */
	handleClipboardEvent: function(event, type, actionName) {
		// ignore unrelated events
		if ((type != 'de.plugins-zum-selberbauen.ultimate.content') || (actionName != 'content.delete')) return;
		
		var $item = $(event.target);
		this._delete($item);
	},
	
	/**
	 * Handle delete action.
	 * 
	 * @param	jQuery		item
	 */
	_delete: function(item) {
		var $confirmMessage = item.data('internalData')['confirmMessage'];
		if (confirm($confirmMessage)) {
			WCF.Clipboard.sendRequest(item);
		}
	}
};

/**
 * Namespace for ULTIMATE.ACP.Page
 */
ULTIMATE.ACP.Page = {};

/**
 * PageList clipboard API
 */
ULTIMATE.ACP.Page.List = function() { this.init(); };
ULTIMATE.ACP.Page.List.prototype = {
	/**
	 * Initializes the PageList clipboard API.
	 */
	init: function() {
		$('body').bind('clipboardAction', $.proxy(this.handleClipboardEvent, this));
	},
	
	/**
	 * Event handler for clipboard editor item actions.
	 */
	handleClipboardEvent: function(event, type, actionName) {
		// ignore unrelated events
		if ((type != 'de.plugins-zum-selberbauen.ultimate.page') || (actionName != 'link.delete')) return;
		
		var $item = $(event.target);
		this._delete($item);
	},
	
	/**
	 * Handle delete action.
	 * 
	 * @param	jQuery		item
	 */
	_delete: function(item) {
		var $confirmMessage = item.data('internalData')['confirmMessage'];
		if (confirm($confirmMessage)) {
			WCF.Clipboard.sendRequest(item);
		}
	}
};