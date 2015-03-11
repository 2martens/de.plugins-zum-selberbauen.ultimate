"use strict";

/**
 * Class and function collection for ULTIMATE CMS
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 */

// a little tweak to know, when remove was used
(function($, undefined) {
	var _empty = $.fn.empty;
	$.fn.empty = function() {
		$(this).triggerHandler("empty");
		return _empty.call($(this));
	};
})(jQuery);

// extends $.ui.resizable to avoid using deprecated method
(function($, undefined) {
	$.extend($.ui.resizable, {
		_propagate : function(n, event) {
			// prevents deprecated usage
			n.call(this, [ event, this.ui() ]);
			(n != "resize" && this._trigger(n, event, this.ui()));
		}
	});
});

// a nice utility to access query values
(function($, undefined) {
    $.getQueryData = function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p=a[i].split('=');
            if (p.length != 2) continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    };
})(jQuery);

(function($, undefined) {
    $.getLiveQueryData = $.getQueryData(window.location.search.substr(1).split('&'));
})(jQuery);

/**
 * Initialize the UTLIMATE namespace.
 * 
 * @namespace
 */
var ULTIMATE = {};

/**
 * Namespace for action-related functions
 * 
 * @namespace
 */
ULTIMATE.Action = {};

ULTIMATE.Action.Delete = WCF.Action.Delete.extend({
	/**
	 * Is called if the delete effect has been triggered on the given element.
	 * 
	 * @param	{jQuery}		element
	 */
	_didTriggerEffect: function(element) {
		var text = $('.counter').text();
		var newCount = text - 1;
		$('.counter').text(newCount);
	},
	
	/**
	 * Initializes available element containers.
	 */
	_initElements: function() {
		var self = this;
		$(this._containerSelector).each(function(index, container) {
			var $container = $(container);
			var $containerID = $container.wcfIdentify();
			
			$container.find(self._buttonSelector).click($.proxy(self._click, self));
			if (!WCF.inArray($containerID, self._containers)) {
				self._containers.push($containerID);
			}
		});
	}
});

ULTIMATE.Action.DeleteVersion = ULTIMATE.Action.Delete.extend({
    /**
     * Sends the request
     *
     * @param {jQuery} object
     */
    _sendRequest: function(object) {
        this.proxy.setOption('data', {
            actionName: 'deleteVersion',
            className: this._className,
            interfaceName: 'wcf\\data\\IDeleteAction',
            objectIDs: [ $(object).data('contentID') ],
            parameters: {
                versionNumber: $(object).data('objectID')
            }
        });

        this.proxy.sendRequest();
    },

    /**
     * Deletes items from containers.
     * 
     * @param {Object} data
     * @param {String} textStatus
     * @param {jqXHR} jqXHR
     * @private
     */
    _success: function(data, textStatus, jqXHR) {
        var returnValues = data['returnValues'];
        var versionNumber = +returnValues['versionNumbers'][0];
        this.triggerEffect([versionNumber]);
    }
});

/**
 * Namespace for date-related functions
 * 
 * @namespace
 */
ULTIMATE.Date = {};

/**
 * Provides a date picker for date input fields.
 */
ULTIMATE.Date.Picker = {
	/**
	 * date format
	 * @var	string
	 */
	_dateFormat: 'yy-mm-dd',
	
	/**
	 * time format
	 * @var	string
	 */
	_timeFormat: 'g:ia',
	
	/**
	 * Initializes the jQuery UI based date picker.
	 */
	init: function() {
		// ignore error 'unexpected literal' error; this might be not the best approach
		// to fix this problem, but since the date is properly processed anyway, we can
		// simply continue :)	- Alex
		var $__log = $.timepicker.log;
		$.timepicker.log = function(error) {
			if (error.indexOf('Error parsing the date/time string: Unexpected literal at position') == -1) {
				$__log(error);
			}
		};
		
		this._convertDateFormat();
		this._initDatePicker();
		WCF.DOMNodeInsertedHandler.addCallback('ULTIMATE.Date.Picker', $.proxy(this._initDatePicker, this));
	},
	
	/**
	 * Convert PHPs date() format to jQuery UIs date picker format.
	 */
	_convertDateFormat: function() {
		// replacement table
		// format of PHP date() => format of jQuery UI date picker
		//
		// No equivalence in PHP date():
		// oo	day of the year (three digit)
		// !	Windows ticks (100ns since 01/01/0001)
		//
		// No equivalence in jQuery UI date picker:
		// N	ISO-8601 numeric representation of the day of the week
		// w	Numeric representation of the day of the week
		// W	ISO-8601 week number of year, weeks starting on Monday
		// t	Number of days in the given month
		// L	Whether it's a leap year
		var $replacementTable = {
			// time
			'a': ' tt',
			'A': ' TT',
			'g': 'h',
			'G': 'H',
			'h': 'hh',
			'H': 'HH',
			'i': 'mm',
			's': 'ss',
			'u': 'l',
			
			// day
			'd': 'dd',
			'D': 'D',
			'j': 'd',
			'l': 'DD',
			'z': 'o',
			'S': '', // English ordinal suffix for the day of the month, 2 characters, will be discarded

			// month
			'F': 'MM',
			'm': 'mm',
			'M': 'M',
			'n': 'm',

			// year
			'o': 'yy',
			'Y': 'yy',
			'y': 'y',

			// timestamp
			'U': '@'
		};
		
		// do the actual replacement
		// this is not perfect, but a basic implementation and should work in 99% of the cases
		this._dateFormat = WCF.Language.get('wcf.date.dateFormat').replace(/([^dDjlzSFmMnoYyU\\]*(?:\\.[^dDjlzSFmMnoYyU\\]*)*)([dDjlzSFmMnoYyU])/g, function(match, part1, part2, offset, string) {
			for (var $key in $replacementTable) {
				if ($replacementTable.hasOwnProperty($key) && part2 == $key) {
					part2 = $replacementTable[$key];
				}
			}
			
			return part1 + part2;
		});
		
		this._timeFormat = WCF.Language.get('wcf.date.timeFormat').replace(/([^aAgGhHisu\\]*(?:\\.[^aAgGhHisu\\]*)*)([aAgGhHisu])/g, function(match, part1, part2, offset, string) {
			for (var $key in $replacementTable) {
				if ($replacementTable.hasOwnProperty($key) && part2 == $key) {
					part2 = $replacementTable[$key];
				}
			}
			
			return part1 + part2;
		});
	},
	
	/**
	 * Initializes the date picker for valid fields.
	 */
	_initDatePicker: function() {
		$('input[type=date]:not(.jsDatePicker)').each($.proxy(function(index, input) {
			var $input = $(input);
			var $inputName = $input.prop('name');
			var $inputValue = $input.val(); // should be Y-m-d, must be interpretable by Date
			
			// update $input
			$input.prop('type', 'text').addClass('jsDatePicker');
			
			// set placeholder
			if ($input.data('placeholder')) $input.attr('placeholder', $input.data('placeholder'));
			
			// insert a hidden element representing the actual date
			$input.removeAttr('name');
			$input.before('<input type="hidden" id="' + $input.wcfIdentify() + 'DatePicker" name="' + $inputName + '" value="' + $inputValue + '" />');
			
			// init date picker
			$input.datepicker({
				altField: '#' + $input.wcfIdentify() + 'DatePicker',
				altFormat: 'yy-mm-dd', // PHPs strtotime() understands this best
				beforeShow: function(input, instance) {
					// dirty hack to force opening below the input
					setTimeout(function() {
						instance.dpDiv.position({
							my: 'left top',
							at: 'left bottom',
							collision: 'none',
							of: input
						});
					}, 1);
				},
				changeMonth: true,
				changeYear: true,
				dateFormat: this._dateFormat,
				dayNames: WCF.Language.get('__days'),
				dayNamesMin: WCF.Language.get('__daysShort'),
				dayNamesShort: WCF.Language.get('__daysShort'),
				monthNames: WCF.Language.get('__months'),
				monthNamesShort: WCF.Language.get('__monthsShort'),
				showOtherMonths: true,
				yearRange: ($input.hasClass('birthday') ? '-100:+0' : '1900:2038'),
				onClose: function(dateText, datePicker) {
					// clear altField when datepicker is cleared
					if (dateText == '') {
						$(datePicker.settings["altField"]).val(dateText);
					}
				}
			});
			
			// format default date
			if ($inputValue) {
				$input.datepicker('setDate', new Date($inputValue));
			}
			
			// bug workaround: setDate creates the widget but unfortunately doesn't hide it...
			$input.datepicker('widget').hide();
		}, this));
		
		$('input[type=datetime]:not(.jsDatePicker)').each($.proxy(function(index, input) {
			var $input = $(input);
			var $inputName = $input.prop('name');
			var $inputValue = $input.val(); // should be Y-m-d H:i:s, must be interpretable by Date
			
			// drop the seconds
			if (/[0-9]{2}:[0-9]{2}:[0-9]{2}$/.test($inputValue)) {
				$inputValue = $inputValue.replace(/:[0-9]{2}$/, '');
				$input.val($inputValue);
			}
			if (/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}/.test($inputValue)) {
				var result = /[0-9]{4}/.exec($inputValue);
				var year = result[0];
				result = /\.(0[1-9]|1[0-2])\./.exec($inputValue);
				var month = result[1];
				result = /^[0-9]{2}/.exec($inputValue);
				var day = result[0];
				result = /[0-9]{2}:[0-9]{2}$/.exec($inputValue);
				var time = result[0];
				$inputValue = year + '-' + month + '-' +  day + 'T' + time + ':00';
			}
			
			// update $input
			$input.prop('type', 'text').addClass('jsDatePicker');
			
			// insert a hidden element representing the actual date
			$input.removeAttr('name');
			$input.before('<input type="hidden" id="' + $input.wcfIdentify() + 'DatePicker" name="' + $inputName + '" value="' + $inputValue + '" />');
			
			// init date picker
			$input.datetimepicker({
				altField: '#' + $input.wcfIdentify() + 'DatePicker',
				altFieldTimeOnly: false,
				altFormat: 'yy-mm-dd', // PHPs strtotime() understands this best
				altTimeFormat: 'HH:mm',
				beforeShow: function(input, instance) {
					// dirty hack to force opening below the input
					setTimeout(function() {
						instance.dpDiv.position({
							my: 'left top',
							at: 'left bottom',
							collision: 'none',
							of: input
						});
					}, 1);
				},
				changeMonth: true,
				changeYear: true,
				controlType: 'select',
				dateFormat: this._dateFormat,
				dayNames: WCF.Language.get('__days'),
				dayNamesMin: WCF.Language.get('__daysShort'),
				dayNamesShort: WCF.Language.get('__daysShort'),
				hourText: WCF.Language.get('wcf.date.hour'),
				minuteText: WCF.Language.get('wcf.date.minute'),
				monthNames: WCF.Language.get('__months'),
				monthNamesShort: WCF.Language.get('__monthsShort'),
				showButtonPanel: false,
				showTime: false,
				showOtherMonths: true,
				timeFormat: this._timeFormat,
				yearRange: ($input.hasClass('birthday') ? '-100:+0' : '1900:2038'),
				onClose: function(dateText, datePicker) {
					// clear altField when datepicker is cleared
					if (dateText == '') {
						$(datePicker.settings.altField).val('');
					}
				}
			});
			
			// format default date
			if ($inputValue) {
				$input.removeClass('hasDatepicker').datetimepicker('setDate', new Date($inputValue));
			}
			
			// bug workaround: setDate creates the widget but unfortunately doesn't hide it...
			$input.datepicker('widget').hide();
		}, this));
	}
};

/**
 * JSON API
 * 
 * @since version 1.0.0
 */
ULTIMATE.JSON = {

	init : function() {

	},

	/**
	 * Encodes a given variable.
	 * 
	 * @param {Object} variable
	 * @return {String}
	 */
	encode : function(variable) {
		var type = typeof variable;
		var $JSON = this;
		if (variable instanceof Array) {
			var output = '[';
			var index = 0;
			var length = variable.length;
			$.each(variable, function(key, value) {
				if (index > 0 && index < length)
					output += ',';
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
				if (index > 0 && index < length)
					output += ',';
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
 * Handles multiple language WYSIWYG textareas.
 *
 * @param	{String}	elementID
 * @param	{Boolean}	forceSelection
 * @param	{Object}	values
 * @param	{Object}	availableLanguages
 */
ULTIMATE.MultipleLanguageWYSIWYG = WCF.MultipleLanguageInput.extend({
    /**
     * target textarea element
     * @var	jQuery
     */
    _element: null,

    /**
     * target wysiwyg box
     * @var jQuery
     */
    _box: null,

    /**
     * Checks if disable has been called.
     * @var Boolean
     */
    _disableCalled: false,
    
    /**
     * Initializes multiple language ability for given element id.
     *
     * @param {String}	elementID
     * @param {Boolean}	forceSelection
     * @param {Object}	values
     * @param {Object}	availableLanguages
     */
    init: function(elementID, forceSelection, values, availableLanguages) {
        this._button = null;
        this._element = $('#' + $.wcfEscapeID(elementID));
        this._box = this._element.redactor('core.getBox');
        this._forceSelection = forceSelection;
        this._values = values;
        this._availableLanguages = availableLanguages;

        // unescape values
        if ($.getLength(this._values)) {
            for (var $key in this._values) {
                if (this._values.hasOwnProperty($key)) {
                    this._values[$key] = WCF.String.unescapeHTML(this._values[$key]);
                }
            }
        }

        // default to current user language
        this._languageID = LANGUAGE_ID;
        if (this._element.length == 0) {
            console.debug("[WCF.MultipleLanguageInput] element id '" + elementID + "' is unknown");
            return;
        }

        // build selection handler
        var $enableOnInit = ($.getLength(this._values) > 0);
        this._insertedDataAfterInit = $enableOnInit;
        this._prepareElement($enableOnInit);

        // listen for submit event
        this._element.parents('form').submit($.proxy(this._submit, this));

        this._didInit = true;
    },

    /**
     * Builds language handler.
     *
     * @param {Boolean} enableOnInit
     */
    _prepareElement: function(enableOnInit) {
        this._box.wrap('<div class="dropdown preInput" />');
        var $wrapper = this._box.parent();
        this._button = $('<p class="button dropdownToggle"><span>' + WCF.Language.get('wcf.global.button.disabledI18n') + '</span></p>').prependTo($wrapper);

        // insert list
        this._list = $('<ul class="dropdownMenu"></ul>').insertAfter(this._button);

        // add a special class if next item is a textarea
        this._button.addClass('dropdownCaptionTextarea');

        // insert available languages
        for (var $languageID in this._availableLanguages) {
            if (this._availableLanguages.hasOwnProperty($languageID)) {
                $('<li><span>' + this._availableLanguages[$languageID] + '</span></li>').data('languageID', $languageID).click($.proxy(this._changeLanguage, this)).appendTo(this._list);
            }
        }

        // disable language input
        if (!this._forceSelection) {
            $('<li class="dropdownDivider" />').appendTo(this._list);
            $('<li><span>' + WCF.Language.get('wcf.global.button.disabledI18n') + '</span></li>').click($.proxy(this._disable, this)).appendTo(this._list);
        }

        WCF.Dropdown.initDropdown(this._button, enableOnInit);

        if (enableOnInit || this._forceSelection) {
            this._isEnabled = true;

            // pre-select current language
            this._list.children('li').each($.proxy(function(index, listItem) {
                var $listItem = $(listItem);
                if ($listItem.data('languageID') == this._languageID) {
                    $listItem.trigger('click');
                }
            }, this));
        }

        WCF.Dropdown.registerCallback($wrapper.wcfIdentify(), $.proxy(this._handleAction, this));
    },
    
    /**
     * Changes the currently active language.
     *
     * @param {Object} event
     */
    _changeLanguage: function(event) {
        var $button = $(event.currentTarget);
        this._insertedDataAfterInit = true;
        if (this._disableCalled) {
            this._disableCalled = false;
        }

        // save current value
        if (this._didInit) {
            this._values[this._languageID] = this._element.redactor('wutil.getText');
        }

        // set new language
        this._languageID = $button.data('languageID');
        if (this._values[this._languageID]) {
            this._element.redactor('wutil.replaceText', this._values[this._languageID]);
        }
        else {
            this._element.redactor('wutil.reset');
        }

        // update marking
        this._list.children('li').removeClass('active');
        $button.addClass('active');

        // update label
        this._button.children('span').addClass('active').text(this._availableLanguages[this._languageID]);

        // close selection and set focus on input element
        if (this._didInit) {
            //this._box.blur();
            //this._element.redactor('focus');
        }
    },

    /**
     * Disables language selection for current element.
     *
     * @param {Object} event
     */
    _disable: function(event) {
        if (event === undefined && this._insertedDataAfterInit) {
            event = null;
        }

        if (this._forceSelection || !this._list || event === null || this._disableCalled) {
            return;
        }
        
        this._disableCalled = true;
        // remove active marking
        this._button.children('span').removeClass('active').text(WCF.Language.get('wcf.global.button.disabledI18n'));

       // update element value
        if (this._values[window.LANGUAGE_ID]) {
            this._element.redactor('wutil.replaceText', this._values[window.LANGUAGE_ID]);
        }
        else {
            this._element.redactor('wutil.reset');
        }
        this._languageID = window.LANGUAGE_ID;

        if (event) {
            this._list.children('li').removeClass('active');
            $(event.currentTarget).addClass('active');
        }

        //this._box.blur();
        //this._element.redactor('focus');
        this._insertedDataAfterInit = false;
        this._isEnabled = false;
        this._values = { };
    },

    /**
     * Prepares language variables on before submit.
     */
    _submit: function() {
        // insert hidden form elements on before submit
        if (!this._isEnabled) {
            return 0xDEADBEEF;
        }

        // fetch active value
        if (this._languageID) {
            this._values[this._languageID] = this._element.redactor('wutil.getText');
        }

        var $form = $(this._element.parents('form')[0]);
        var $elementID = this._element.wcfIdentify();

        for (var $languageID in this._availableLanguages) {
            if (this._availableLanguages.hasOwnProperty($languageID) && this._values[$languageID] === undefined) {
                this._values[$languageID] = '';
            }

            $('<input type="hidden" name="' + $elementID + '_i18n[' + $languageID + ']" value="' + WCF.String.escapeHTML(this._values[$languageID]) + '" />').appendTo($form);
        }

        // remove name attribute to prevent conflict with i18n values
        this._element.removeAttr('name');
    }
});

/**
 * Global permission storage.
 * 
 * @see WCF.Dictionary
 * @since version 1.0.0
 */
ULTIMATE.Permission = {
	/**
	 * Contains the permissions.
	 * 
	 * @type WCF.Dictionary
	 */
	_variables : new WCF.Dictionary(),

	/**
	 * @param {String} key
	 * @param {Boolean} value
	 * @see WCF.Dictionary.add()
	 */
	add : function(key, value) {
		this._variables.add(key, value);
	},

	/**
	 * @see WCF.Dictionary.addObject()
	 */
	addObject : function(object) {
		this._variables.addObject(object);
	},

	/**
	 * Retrieves a variable.
	 * 
	 * @param {String} key
	 * @return {Boolean}
	 */
	get : function(key) {
		var value = this._variables.get(key);

		if (value === null) {
			// return key again
			return key;
		}

		return value;
	}
};

/**
 * Namespace for ULTIMATE.NestedSortable
 * 
 * @namespace
 */
ULTIMATE.NestedSortable = {};

/**
 * @see WCF.Action.Delete
 */
ULTIMATE.NestedSortable.Delete = WCF.Action.Delete.extend({
	/**
	 * @see WCF.Action.Delete.triggerEffect()
	 */
	triggerEffect : function(objectIDs) {
		for (var $index in this._containers) {
            if (this._containers.hasOwnProperty($index)) {
                var $container = $('#' + this._containers[$index]);
                if (WCF.inArray(
                        $container.find('.jsDeleteButton').data('objectID'),
                        objectIDs)) {
                    // move child categories up
                    if ($container.has('ol').has('li')) {
                        var $list = $container.find('> ol');
                        $container.before($list.contents());
                        $list.contents().detach();
                        $container.remove();
                    } else {
                        $container.wcfBlindOut('up', function() {
                            $container.remove();
                        });
                    }
                }
            }
		}
	}
});
