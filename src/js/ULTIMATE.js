/**
 * Class and function collection for ULTIMATE CMS
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
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
	 * @param	jQuery		element
	 */
	_didTriggerEffect: function(element) {
		var text = $('.counter').text();
		var newCount = text - 1;
		$('.counter').text(newCount);
	},
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
				if (part2 == $key) {
					part2 = $replacementTable[$key];
				}
			}
			
			return part1 + part2;
		});
		
		this._timeFormat = WCF.Language.get('wcf.date.timeFormat').replace(/([^aAgGhHisu\\]*(?:\\.[^aAgGhHisu\\]*)*)([aAgGhHisu])/g, function(match, part1, part2, offset, string) {
			for (var $key in $replacementTable) {
				if (part2 == $key) {
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
	 * @param {Object}
	 *            variable
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
	 * @param {String}
	 *            key
	 * @param {Boolean}
	 *            value
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
	 * @param {String}
	 *            key
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
		for ( var $index in this._containers) {
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
});
