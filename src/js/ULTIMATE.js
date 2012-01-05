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
ULTIMATE.ConfigEditor = {
	/**
	 * Contains the current selector.
	 */
	_jqXHR: null,
		
	/**
	 * Initializes the ConfigEditor API.
	 */
	init: function() {
		
	},	

	addEntry: function(column, url) {
		var options = arguments[2] || {};
		this._jqXHR = $.post(url, options,
    		function(data) {
				$('#popupAddEntry').wcfDialog('close');
				
    			var result = data;
    			var selectorColumn = '#column' + column;
				$(selectorColumn).append(result);
    			increaseIndex();
    		},
    		'html'
    	);		
	}
};