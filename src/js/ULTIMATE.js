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

/**
 * Handles multiple language input fields.
 * Manipulated to fit need of textareas.
 * 
 * @param	string		elementID
 * @param	boolean		forceSelection
 * @param	object		values
 * @param	object		availableLanguages
 */
ULTIMATE.MultipleLanguageInput = function(elementID, forceSelection, values, availableLanguages) { this.init(elementID, forceSelection, values, availableLanguages); };
ULTIMATE.MultipleLanguageInput.prototype = {
	/**
	 * list of available languages
	 * @var	object
	 */
	_availableLanguages: {},

	/**
	 * initialization state
	 * @var	boolean
	 */
	_didInit: false,

	/**
	 * target input element
	 * @var	jQuery
	 */
	_element: null,
	
	/**
	 * true, if data was entered after initialization
	 * @var	boolean
	 */
	_insertedDataAfterInit: false,

	/**
	 * enables multiple language ability
	 * @var	boolean
	 */
	_isEnabled: false,

	/**
	 * enforce multiple language ability
	 * @var	boolean
	 */
	_forceSelection: false,

	/**
	 * currently active language id
	 * @var	integer
	 */
	_languageID: 0,

	/**
	 * language selection list
	 * @var	jQuery
	 */
	_list: null,

	/**
	 * list of language values on init
	 * @var	object
	 */
	_values: null,

	/**
	 * Initializes multiple language ability for given element id.
	 * 
	 * @param	integer		elementID
	 * @param	boolean		forceSelection
	 * @param	boolean		isEnabled
	 * @param	object		values
	 * @param	object		availableLanguages
	 */
	init: function(elementID, forceSelection, values, availableLanguages) {
		this._element = $('#' + $.wcfEscapeID(elementID));
		this._forceSelection = forceSelection;
		this._values = values;
		this._availableLanguages = availableLanguages;
		
		// default to current user language
		this._languageID = LANGUAGE_ID;
		if (this._element.length == 0) {
			console.debug("[WCF.MultipleLanguageInput] element id '" + elementID + "' is unknown");
			return;
		}
		
		// build selection handler
		var $enableOnInit = ($.getLength(this._values) > 0) ? true : false;
		this._insertedDataAfterInit = $enableOnInit;
		this._prepareElement($enableOnInit);
		
		// listen for submit event
		this._element.parents('form').submit($.proxy(this._submit, this));

		this._didInit = true;
	},

	/**
	 * Builds language handler.
	 * 
	 * @param	boolean		enableOnInit
	 */
	_prepareElement: function(enableOnInit) {
		// enable DOMNodeInserted event
		WCF.DOMNodeInsertedHandler.enable();
		
		this._element.wrap('<div class="dropdown preInput" />');
		var $wrapper = this._element.parent();
		var $button = $('<p class="button dropdownToggle"><span>' + WCF.Language.get('wcf.global.button.disabledI18n') + '</span></p>').prependTo($wrapper);
		$button.data('toggle', $wrapper.wcfIdentify()).click($.proxy(this._enable, this));
		
		// add a special class if next item is a textarea
		var $top = null;
		if ($button.next().getTagName() === 'textarea') {
			$top = $button.outerHeight() - 1;
			$button.addClass('dropdownCaptionTextarea');
		}
		else {
			$button.addClass('dropdownCaption');
		}
		
		// insert list
		this._list = $('<ul class="dropdownMenu"></ul>').insertAfter($button);
		
		// set top offset for menu
		if ($top !== null) {
			this._list.css({
				top: $top
			});
		}
		
		// insert available languages
		for (var $languageID in this._availableLanguages) {
			$('<li><span>' + this._availableLanguages[$languageID] + '</span></li>').data('languageID', $languageID).click($.proxy(this._changeLanguage, this)).appendTo(this._list);
		}

		// disable language input
		if (!this._forceSelection) {
			$('<li class="dropdownDivider" />').appendTo(this._list);
			$('<li><span>' + WCF.Language.get('wcf.global.button.disabledI18n') + '</span></li>').click($.proxy(this._disable, this)).appendTo(this._list);
		}
		
		if (enableOnInit || this._forceSelection) {
			$button.trigger('click');

			// pre-select current language
			this._list.children('li').each($.proxy(function(index, listItem) {
				var $listItem = $(listItem);
				if ($listItem.data('languageID') == this._languageID) {
					$listItem.trigger('click');
				}
			}, this));
		}
		
		WCF.Dropdown.registerCallback($wrapper.wcfIdentify(), $.proxy(this._handleAction, this));
		
		// disable DOMNodeInserted event
		WCF.DOMNodeInsertedHandler.disable();
	},
	
	/**
	 * Handles dropdown actions.
	 * 
	 * @param	jQuery		dropdown
	 * @param	string		action
	 */
	_handleAction: function(dropdown, action) {
		if (action === 'close') {
			this._closeSelection();
		}
	},

	/**
	 * Enables the language selection or shows the selection if already enabled.
	 * 
	 * @param	object		event
	 */
	_enable: function(event) {
		if (!this._isEnabled) {
			var $button = $(event.currentTarget);
			if ($button.getTagName() === 'p') {
				$button = $button.children('span:eq(0)');
			}
			
			$button.addClass('active');
			
			this._isEnabled = true;
			this._insertedDataAfterInit = false;
		}
		
		// toggle list
		if (this._list.is(':visible')) {
			this._closeSelection();
		}
		else {
			this._showSelection();
		}

		// discard event
		event.stopPropagation();
	},

	/**
	 * Shows the language selection.
	 */
	_showSelection: function() {
		if (this._isEnabled) {
			// display status for each language
			this._list.children('li').each($.proxy(function(index, listItem) {
				var $listItem = $(listItem);
				var $languageID = $listItem.data('languageID');

				if ($languageID) {
					if (this._values[$languageID] && this._values[$languageID] != '') {
						$listItem.removeClass('missingValue');
					}
					else {
						$listItem.addClass('missingValue');
					}
				}
			}, this));
		}
	},

	/**
	 * Closes the language selection.
	 */
	_closeSelection: function() {
		if (!this._insertedDataAfterInit) {
			// prevent loop of death
			this._insertedDataAfterInit = true;
			
			this._disable();
		}
	},

	/**
	 * Changes the currently active language.
	 * 
	 * @param	object		event
	 */
	_changeLanguage: function(event) {
		var $button = $(event.currentTarget);
		this._insertedDataAfterInit = true;
		
		// save current value
		if (this._didInit) {
			this._values[this._languageID] = this._element.val();
		}
		
		// set new language
		this._languageID = $button.data('languageID');
		if (this._values[this._languageID]) {
			var tmpStr = this._values[this._languageID];
			tmpStr = tmpStr.replace(/_specialNewline/g, '\r');
			this._element.val(tmpStr);
		}
		else {
			this._element.val('');
		}
		
		// update marking
		this._list.children('li').removeClass('active');
		$button.addClass('active');
		
		// update label
		this._list.prev('.dropdownToggle').children('span').text(this._availableLanguages[this._languageID]);
		
		// close selection and set focus on input element
		this._closeSelection();
		this._element.blur().focus();
	},

	/**
	 * Disables language selection for current element.
	 */
	_disable: function() {
		if (this._forceSelection || !this._list) {
			return;
		}
		
		// remove active marking
		this._list.prev('.dropdownToggle').children('span').removeClass('active').text(WCF.Language.get('wcf.global.button.disabledI18n'));
		this._closeSelection();

		// update element value
		if (this._values[LANGUAGE_ID]) {
			this._element.val(this._values[LANGUAGE_ID]);
		}
		else {
			// no value for current language found, proceed with empty input
			this._element.val();
		}
		
		this._element.blur();
		this._isEnabled = false;
	},

	/**
	 * Prepares language variables on before submit.
	 */
	_submit: function() {
		// insert hidden form elements on before submit
		if (!this._isEnabled) {
			return 0xDEADBEAF;
		}

		// fetch active value
		if (this._languageID) {
			this._values[this._languageID] = this._element.val();
		}

		var $form = $(this._element.parents('form')[0]);
		var $elementID = this._element.wcfIdentify();

		for (var $languageID in this._values) {
			var tmpStr = this._values[$languageID];
			tmpStr = tmpStr.replace(/_specialNewline/g, '\r');
			$('<input type="hidden" name="' + $elementID + '_i18n[' + $languageID + ']" value="' + tmpStr + '" />').appendTo($form);
		}

		// remove name attribute to prevent conflict with i18n values
		this._element.removeAttr('name');
	}
};