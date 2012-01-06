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
 * ULTIMATE Content Management System core methods
 */
$.extend(ULTIMATE, {
	
	/**
	 * Counts the elements in an array-like object.
	 * 
	 * @param 	Object 	object
	 * @return 	integer
	 */
	countArrayLikeObj: function(object) {
		var length = 0;
		if (typeof object != 'object') return null;
		$.each(object, function(key, value) {
			length++;
		});
		return length;
	}
});

/**
 * JSON API
 */
ULTIMATE.JSON = {
	
    /**
     * Encodes a given variable.
     * 
     * @param 	Object variable
     * @return 	string
     */
	encode: function(variable) {
		var type = typeof variable;
		
		if (variable instanceof Array) {
			var output = '[';
			var index = 0;
			var length = variable.length;
			$.each(variable, function(key, value) {
				if (index > 0 && index < length) output += ',';
				if (value instanceof Array || typeof value == 'object') {
					output += this.encode(value);
				}
				output += '"' + value + '"';								
				index++;
			});
			output += ']';
			return output;
		} else if (type == 'object') {
			var output = '{';
			var index = 0;
			var length = ULTIMATE.countArrayLikeObj(variable);
			$.each(variable, function(key, value) {
				if (index > 0 && index < length) output += ',';
				output += '"' + key + '":';
				var valType = typeof value;
				if (value instanceof Array) {
					output += this.encode(value);
				} else if (valType == 'object') {
					output += this.encode(value);
				} else if (valType == 'string') {
					output += '"' + value + '"';
				}
				index++;
			});
			
			output += '}';
			return output;
		}
		return null;		
	}
};

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