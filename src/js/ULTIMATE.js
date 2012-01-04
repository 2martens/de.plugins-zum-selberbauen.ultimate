/**
 * Class and function collection for ULTIMATE CMS.
 * 
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 */

/**
 * Initialize the UTLIMATE namespace.
 */
var ULTIMATE = {};

/**
 * ConfigEditor API
 */
ULTIMATE.ConfigEditor = function() { this.init(); };
ULTIMATE.ConfigEditor.prototype = {
	/**
	 * Contains the current selector.
	 */
	_selector: null,
		
	/**
	 * Initializes the ConfigEditor API.
	 */
	init: function() {
		
	},	

	addEntry: function(selector, id) {
		this._selector = selector;
		
	}
};