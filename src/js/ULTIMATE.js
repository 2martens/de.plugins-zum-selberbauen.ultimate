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
 * JSON API
 */
ULTIMATE.JSON = {
	
	init: function() {
		
	},
		
    /**
     * Encodes a given variable.
     * 
     * @param 	Object variable
     * @return 	string
     */
	encode: function(variable) {
		var type = typeof variable;
		var $JSON = this;
		if (variable instanceof Array) {
			var output = '[';
			var index = 0;
			var length = variable.length;
			$.each(variable, function(key, value) {
				if (index > 0 && index < length) output += ',';
				if (value instanceof Array || typeof value == 'object') {
					output += $JSON.encode(value);
				}
				output += '"' + value + '"';								
				index++;
			});
			output += ']';
			return output;
		} else if (type == 'object') {
			var output = '{';
			var index = 0;
			var length = $.getLength(variable);
			$.each(variable, function(key, value) {
				if (index > 0 && index < length) output += ',';
				output += '"' + key + '":';
				var valType = typeof value;
				if (value instanceof Array) {
					output += $JSON.encode(value);
				} else if (valType == 'object') {
					output += $JSON.encode(value);
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
 * Namespace for ULTIMATE.Button
 */
ULTIMATE.Button = {};

/**
 * Handles button replacements.
 * 
 * @param   string buttonID
 * @param   string checkElementID
 * @param   string action
 */
ULTIMATE.Button.Replacement = function(buttonID, checkElementID, action) { this.init(buttonID, checkElementID, action); };
ULTIMATE.Button.Replacement.prototype = {
    /**
     * target input[type=submit] element
     * @var jQuery
     */
    _button: null,
    
    /**
     * the button value
     * @var String
     */
    _buttonValue: '',
    
    /**
     * element to check for changes
     * @var jQuery
     */
    _checkElement: null,
    
    /**
     * the initial timestamp
     * @var integer
     */
    _initialValueDateTime: 0,
    
    /**
     * the initial status id
     * @var integer
     */
    _initialStatusID: 0,
    
    /**
     * action parameter
     * @var String
     */
    _action: '',
    
    /**
     * Contains the language variables for the save action.
     * @var Object
     */
    _saveMap: {
        0: 'ultimate.button.saveAsDraft',
        1: 'ultimate.button.saveAsPending'
    },
    
    /**
     * Contains the language variables for the publish action.
     * @var object
     */
    _publishMap: {
        0: 'ultimate.button.publish',
        1: 'ultimate.button.schedule',
        2: 'ultimate.button.update'
    },
    
    /**
     * Initializes the ButtonReplacement API.
     */
    init: function(buttonID, checkElementID, action) {
        this._button = $('#' + $.wcfEscapeID(buttonID));
        this._buttonValue = this._button.val();
        this._checkElement = $('#' + $.wcfEscapeID(checkElementID));
        this._action = action;
        
        if (this._action == 'save') {
            this._initialStatusID = this._checkElement.val();
        } else if (this._action == 'publish') {
            var $initialDateTime = this._checkElement.val();
            var $dateObj = new Date($initialDateTime);
            this._initialDateTime = WCF.Date.Util.gmdate($dateObj);
        }
        
        this._checkElement.change($.proxy(this._change, this));
        this._change();
    },

    _change: function() {
        if (this._action == 'save') {
            var $currentValue = this._checkElement.val();
            var $languageOutput = WCF.Language.get(this._saveMap[$currentValue]);
            if ($currentValue >= 2) {
                this._button.attr('disabled', 'disabled');
                this._button.addClass('ultimateHidden');
            } else if ($currentValue == 0 || $currentValue == 1) {
                this._button.removeClass('ultimateHidden');
                this._button.removeAttr('disabled');
                this._button.val($languageOutput);
            }
        } else if (this._action == 'publish') {
            var $insertedDateTime = this._checkElement.val();
            var $dateObj = new Date($insertedDateTime);
            var $timestamp = WCF.Date.Util.gmdate($dateObj);
            var $timestampNow = WCF.Date.Util.gmdate();
            
            var $updateButton = WCF.Language.get(this._publishMap[2]);
            var $isUpdateSzenario = ($updateButton == this._buttonValue);
            
            if ($timestamp > $timestampNow) {
                if ($isUpdateSzenario && (this._initialDateTime > $timestampNow)) return;
                this._button.val(WCF.Language.get(this._publishMap[1]));
            } else {
                if ($isUpdateSzenario && (this._initialDateTime < $timestampNow)) return;
                this._button.val(WCF.Language.get(this._publishMap[0]));
            }
        }
    }
};

