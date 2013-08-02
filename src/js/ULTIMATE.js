/**
 * Class and function collection for ULTIMATE CMS.
 * 
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS
 *          License
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
 * Namespace for ULTIMATE.Button
 * 
 * @namespace
 */
ULTIMATE.Button = {};

/**
 * Handles button replacements.
 * 
 * @param {String}
 *            buttonID
 * @param {String}
 *            checkElementID
 * @param {String}
 *            action
 * @constructor
 * @since version 1.0.0
 */
ULTIMATE.Button.Replacement = function(buttonID, checkElementID, action) {
	this.init(buttonID, checkElementID, action);
};
ULTIMATE.Button.Replacement.prototype = {
	/**
	 * target input[type=submit] element
	 * 
	 * @type jQuery
	 */
	_button : null,

	/**
	 * the button value
	 * 
	 * @type String
	 */
	_buttonValue : '',

	/**
	 * element to check for changes
	 * 
	 * @type jQuery
	 */
	_checkElement : null,

	/**
	 * the initial date
	 * 
	 * @type Date
	 */
	_initialValueDateTime : null,

	/**
	 * the last date
	 * 
	 * @type Date
	 */
	_lastValueDateTime : null,

	/**
	 * the initial status id
	 * 
	 * @type Integer
	 */
	_initialStatusID : 0,

	/**
	 * action parameter
	 * 
	 * @type String
	 */
	_action : '',

	/**
	 * Contains the language variables for the save action.
	 * 
	 * @type Object
	 */
	_saveMap : {
		0 : 'ultimate.button.saveAsDraft',
		1 : 'ultimate.button.saveAsPending'
	},

	/**
	 * Contains the language variables for the publish action.
	 * 
	 * @type Object
	 */
	_publishMap : {
		0 : 'ultimate.button.publish',
		1 : 'ultimate.button.schedule',
		2 : 'ultimate.button.update'
	},

	/**
	 * Initializes the ButtonReplacement API.
	 * 
	 * @param {String}
	 *            buttonID
	 * @param {String}
	 *            checkElementID
	 * @param {String}
	 *            action
	 */
	init : function(buttonID, checkElementID, action) {
		this._button = $('#' + $.wcfEscapeID(buttonID));
		this._buttonValue = this._button.val();
		this._checkElement = $('#' + $.wcfEscapeID(checkElementID));
		this._action = action;

		if (this._action == 'save') {
			this._initialStatusID = this._checkElement.val();
		} else if (this._action == 'publish') {
			var $dateObj = this._checkElement.datetimepicker('getDate');
			this._initialValueDateTime = $dateObj;
			this._lastValueDateTime = $dateObj;
		}

		this._checkElement.change($.proxy(this._change, this));
		this._change();
	},

	/**
	 * Changes button label.
	 */
	_change : function() {
		if (this._action == 'save') {
			var $currentValue = this._checkElement.val();
			var $languageOutput = WCF.Language
					.get(this._saveMap[$currentValue]);
			if ($currentValue >= 2) {
				this._button.attr('disabled', 'disabled')
						.prop('disabled', true);
				this._button.addClass('ultimateHidden');
			} else if ($currentValue == 0 || $currentValue == 1) {
				this._button.removeClass('ultimateHidden');
				this._button.removeAttr('disabled').prop('disabled', false);
				this._button.val($languageOutput);
			}
		} else if (this._action == 'publish') {
			var $dateObj = this._checkElement.datetimepicker('getDate');
			var $dateNow = new Date();

			var $updateButton = WCF.Language.get(this._publishMap[2]);
			var $isUpdateSzenario = ($updateButton == this._buttonValue);

			if ($dateObj > $dateNow) {
				if ($isUpdateSzenario && (this._lastValueDateTime > $dateNow)) {
					return;
				}
				if ($isUpdateSzenario && this._initialValueDateTime > $dateNow)
					this._button.val($updateButton);
				else
					this._button.val(WCF.Language.get(this._publishMap[1]));
			} else {
				if ($isUpdateSzenario && (this._lastValueDateTime < $dateNow)) {
					return;
				}
				if ($isUpdateSzenario && this._initialValueDateTime < $dateNow)
					this._button.val($updateButton);
				else
					this._button.val(WCF.Language.get(this._publishMap[0]));
			}
			this._lastValueDateTime = $dateObj;
		}
	}
};

/**
 * Namespace for ULTIMATE.Block
 * 
 * @namespace
 */
ULTIMATE.Block = {};

/**
 * Transfers new blocks to a template.
 * 
 * @param {String}
 *            elementID
 * @param {String}
 *            containerID
 * @param {String}
 *            className the action class name
 * @class Adds blocks to a block list.
 * @since version 1.0.0
 */
ULTIMATE.Block.Transfer = function(elementID, containerID, className) {
	this.init(elementID, containerID, className);
};
ULTIMATE.Block.Transfer.prototype = {
	/**
	 * Contains the element from which the blocks should be transferred.
	 * 
	 * @type jQuery
	 */
	_element : null,

	/**
	 * Contains the element to which the blocks should be transferred.
	 * 
	 * @type jQuery
	 */
	_container : null,

	/**
	 * Contains the container ID.
	 * 
	 * @type String
	 */
	_containerID : '',

	/**
	 * Contains a notification.
	 * 
	 * @type WCF.System.Notification
	 */
	_notification : null,

	/**
	 * Contains a proxy object.
	 * 
	 * @type WCF.Action.Proxy
	 */
	_proxy : null,

	/**
	 * Contains a list of all available block options.
	 * 
	 * @type Array
	 */
	_optionList : [],

	/**
	 * Contains a dialog object.
	 * 
	 * @type jQuery
	 */
	_dialog : null,

	/**
	 * Contains the edited block id.
	 * 
	 * @type Integer
	 */
	_editBlockID : 0,

	/**
	 * Initializes the BlockTransfer API.
	 * 
	 * @param {String}
	 *            elementID
	 * @param {String}
	 *            containerID
	 * @param {String}
	 *            className
	 */
	init : function(elementID, containerID, className) {
		this._element = $('#' + $.wcfEscapeID(elementID));
		this._container = $('#' + $.wcfEscapeID(containerID));
		this._containerID = $.wcfEscapeID(containerID);
		this._className = className;

		this._proxy = new WCF.Action.Proxy({
			success : $.proxy(this._success, this)
		});
		this._element.parent('form')
				.submit($.proxy(this._stopFormSubmit, this));
		this._element.find('button[data-type="submit"]').click(
				$.proxy(this._submit, this));
		this._init();
	},

	_init : function() {
		$('.jsBlock').on('remove', $.proxy(this._remove, this));
		$('.icon-pencil').not('.disabled').on('click',
				$.proxy(this._edit, this));
	},

	/**
	 * Stops the form submit event.
	 * 
	 * @param {jQuery.Event}
	 *            event
	 * @return {Boolean}
	 */
	_stopFormSubmit : function(event) {
		event.preventDefault();
		return;
	},

	/**
	 * Called each time a block is removed with remove().
	 * 
	 * @param {jQuery.Event}
	 *            event
	 */
	_remove : function(event) {
		if ($('#' + this._containerID).find('.jsBlock').length <= 1) {
			$('#' + this._containerID).find('button[data-type="submit"]').prop(
					'disabled', true).addClass('disabled');
		}
	},

	/**
	 * Called each time a block is edited.
	 * 
	 * @param {jQuery.Event}
	 *            event
	 */
	_edit : function(event) {
		var $target = $(event.currentTarget);
		event.preventDefault();

		var $data = {};
		var blockID = $target.data('objectID');
		this._editedBlockID = blockID;
		// select blockType specific information
		var $formDataParameters = $.extend(true, {
			data : {
				blockID : blockID
			}
		}, {});

		var $formData = $.extend(true, {
			actionName : 'getFormDataEditAJAX',
			className : this._className,
			parameters : $formDataParameters
		}, $data);

		var $proxy = new WCF.Action.Proxy({
			success : $.proxy(this._successFormDataEdit, this)
		});
		$proxy.setOption('data', $formData);
		$proxy.sendRequest();
	},

	/**
	 * Saves blocks.
	 */
	_submit : function() {
		var $data = {};
		// read form data
		var blockTypeID = $('#selectBlocktype').val();

		if (blockTypeID == '0') {
			this._notification = new WCF.System.Notification(WCF.Language
					.get('wcf.global.form.error'), 'error');
			this._element.find('dl:first').addClass('formError');
			var $html = '<small id="selectBlocktypeError" class="innerError">'
					+ WCF.Language
							.get('wcf.acp.ultimate.template.selectBlocktype.error.notSelected')
					+ '</small>';
			$('#selectBlocktypeError').empty().remove();
			this._element.find('dl:first > dd').append($html);
			this._notification.show();
			return;
		}
		this._notification = null;
		$('#selectBlocktypeError').empty().remove();

		// select blockType specific information
		var $formDataParameters = $.extend(true, {
			data : {
				blockTypeID : blockTypeID
			}
		}, {});

		var $formData = $.extend(true, {
			actionName : 'getFormDataAJAX',
			className : this._className,
			parameters : $formDataParameters
		}, $data);

		var $proxy = new WCF.Action.Proxy({
			success : $.proxy(this._successFormData, this)
		});
		$proxy.setOption('data', $formData);
		$proxy.sendRequest();
	},

	/**
	 * Saves the additional block options.
	 */
	_submitFormData : function() {
		// read form data
		var blockTypeID = $('#selectBlocktype').val();
		var height = $('#height').val();
		var $parameters = $.extend(true, {
			data : {
				blockTypeID : blockTypeID,
				additionalData : {
					height : height
				},
				templateID : $('input[name="id"]').val()
			}
		}, {});

		$parameters = this._readBlockOptionsFormData($parameters);

		// reset form
		$('#selectBlocktype').val('0');
		$('#height').val('0');

		// build proxy data
		var $data = $.extend(true, {
			actionName : 'createAJAX',
			className : this._className,
			parameters : $parameters
		}, {});
		this._proxy.setOption('data', $data);

		// send proxy request
		this._proxy.sendRequest();
	},

	/**
	 * Saves the additional block options after editing them.
	 */
	_submitFormDataEdit : function() {
		// read form data
		var blockID = this._editedBlockID;
		var $parameters = $.extend(true, {
			data : {
				additionalData : {}
			}
		}, {});

		$parameters = this._readBlockOptionsFormData($parameters);

		// build proxy data
		var $data = $.extend(true, {
			actionName : 'editAJAX',
			className : this._className,
			parameters : $parameters,
			objectIDs : [ blockID ]
		}, {});

		var $proxy = new WCF.Action.Proxy({
			success : $.proxy(this._successEdit, this)
		});
		$proxy.setOption('data', $data);

		// send proxy request
		$proxy.sendRequest();
	},

	/**
	 * Reads the block options form data.
	 * 
	 * @param {Object}
	 *            parameters
	 */
	_readBlockOptionsFormData : function($parameters) {
		for ( var i = 0; i < this._optionList.length; i++) {
			var $item = this._optionList[i];
			var optionName = $item.replace(/_\d+/, '');
			var optionFound = $('input[name="' + optionName + '"]');
			var isSelection = false;
			if (optionFound.length == 0) {
				optionFound = $('textarea[name="' + optionName + '"]');
			}
			if (optionFound.length == 0) {
				optionFound = $('select[name="' + optionName + '"]');
				isSelection = true;
			}
			if (optionFound.length == 1) {
				var $optionElement = $('#' + $item).val();
				if ($optionElement == null && isSelection) {
					$optionElement = [];
				}
				$parameters['data']['additionalData'][optionName] = $optionElement;
			} else if (optionFound.length == 0) {
				var optionName_i18n = {};
				$('input[name^="' + $item + '_i18n"]').each(
						$.proxy(function(index, listItem) {
							var $listItem = $(listItem);
							var $languageID = $listItem.attr('name').substring(
									$item.length + 6);
							$languageID = $languageID.substr(0,
									$languageID.length - 1);
							optionName_i18n[$languageID] = $listItem.val();
						}, this));
				$parameters['data']['additionalData'][optionName + '_i18n'] = optionName_i18n;
			}
		}
		return $parameters;
	},

	/**
	 * Shows dialog form.
	 * 
	 * @param {Object}
	 *            data
	 * @param {String}
	 *            textStatus
	 * @param {jQuery}
	 *            jqXHR
	 */
	_successFormData : function(data, textStatus, jqXHR) {
		try {
			var $data = data['returnValues'];
			this._createOptionsDialog($data);

			$('#blockForm').find('form').submit(
					$.proxy(this._submitFormData, this));

		} catch (e) {
			var $showError = true;
			if ($showError !== false) {
				$(
						'<div class="ajaxDebugMessage"><p>' + e.message
								+ '</p></div>').wcfDialog({
					title : WCF.Language.get('wcf.global.error.title')
				});
			}
		}
	},

	/**
	 * Shows dialog form on edit.
	 * 
	 * @param {Object}
	 *            data
	 * @param {String}
	 *            textStatus
	 * @param {jQuery}
	 *            jqXHR
	 */
	_successFormDataEdit : function(data, textStatus, jqXHR) {
		try {
			var $data = data['returnValues'];
			this._createOptionsDialog($data);

			$('#blockForm').find('form').submit(
					$.proxy(this._submitFormDataEdit, this));

		} catch (e) {
			var $showError = true;
			if ($showError !== false) {
				$(
						'<div class="ajaxDebugMessage"><p>' + e.message
								+ '</p></div>').wcfDialog({
					title : WCF.Language.get('wcf.global.error.title')
				});
			}
		}
	},

	/**
	 * Initializes the block options dialog.
	 * 
	 * @param {Array}
	 *            $data
	 */
	_createOptionsDialog : function($data) {
		this._optionList = $data[0];
		$('#blockForm').html($data[1]);
		$('#blockForm').find('form')
				.submit($.proxy(this._stopFormSubmit, this));

		if (!$.wcfIsset('blockForm'))
			return;
		this._dialog = $('#' + $.wcfEscapeID('blockForm'));
		this._dialog.wcfDialog({
			title : WCF.Language
					.get('wcf.acp.ultimate.template.dialog.additionalOptions')
		});
		// initializing tabs
		this._dialog.removeClass('ultimateHidden');
		this._dialog.wcfDialog('open');

		WCF.TabMenu.reload();

		this._dialog.find('nav.tabMenu li').each(function(index, item) {
			$(this).removeClass('active');
		});
		this._dialog.find('div.tabMenuContent').each(
				function(index, container) {
					$(this).hide();
				});
		this._dialog.find('nav.tabMenu li').click(
				$.proxy(this._toggleTabs, this));
		this._renderTab(this._dialog.find('nav.tabMenu li:first').data(
				'menuItem'));

		this._dialog.wcfDialog('render');
		this._dialog.css({
			'max-height' : '400px',
			'overflow' : 'scroll'
		});
	},

	/**
	 * Toggles the tabs.
	 * 
	 * @param {jQuery}
	 *            event
	 */
	_toggleTabs : function(event) {
		var $target = $(event.currentTarget);

		if ($target.hasClass('active')) {
			return;
		}

		this._renderTab($target.data('menuItem'));
	},

	/**
	 * Renders the tab content.
	 * 
	 * @param {String}
	 *            menuItem
	 */
	_renderTab : function(menuItem) {
		this._dialog.find('nav.tabMenu li').each(function(index, item) {
			$(this).removeClass('active');
		});
		this._dialog.find('div.tabMenuContent').each(
				function(index, container) {
					$(this).hide();
				});

		this._dialog.find('li[data-menu-item="' + menuItem + '"]').addClass(
				'active');
		this._dialog.find('div[data-parent-menu-item="' + menuItem + '"]')
				.show();
	},

	/**
	 * Shows notification upon success.
	 * 
	 * @param {Object}
	 *            data
	 * @param {String}
	 *            textStatus
	 * @param {jQuery}
	 *            jqXHR
	 */
	_success : function(data, textStatus, jqXHR) {
		if (this._notification === null) {
			this._notification = new WCF.System.Notification(WCF.Language
					.get('wcf.global.success.edit'));
		}
		try {
			var $data = data['returnValues'];
			var $newHtml = '<li class="jsBlock" data-object-name="'
					+ $data['blockTypeName'] + '" data-object-id="'
					+ $data['blockID'] + '">';
			$newHtml += '<span><span class="buttons">';
			if (ULTIMATE.Permission
					.get('admin.content.ultimate.canManageBlocks')) {
				$newHtml += '<span title="'
						+ WCF.Language.get('wcf.acp.ultimate.block.edit')
						+ '" class="icon icon16 icon-pencil jsTooltip" data-object-id="'
						+ $data['blockID'] + '"></span>';

				$newHtml += '\n<span title="'
						+ WCF.Language.get('wcf.global.button.delete')
						+ '" class="icon icon16 icon-remove jsDeleteButton jsTooltip" data-object-id="'
						+ $data['blockID']
						+ '" data-confirm-message="'
						+ WCF.Language
								.get('wcf.acp.ultimate.block.delete.sure')
						+ '"></span>';
			} else {
				$newHtml += '<span title="'
						+ WCF.Language.get('wcf.acp.ultimate.block.edit')
						+ '" class="icon icon16 icon-pencil disabled"></span>';
				$newHtml += '\n<span title="'
						+ WCF.Language.get('wcf.global.button.delete')
						+ '" class="icon icon16 icon-remove disabled"></span>';
			}
			$newHtml += '</span>\n<span class="title">'
					+ $data['blockTypeName'] + ' #' + $data['blockID']
					+ '</span></span></li>';

			$('#' + this._containerID).find('> ol').append($newHtml);
			if ($('#' + this._containerID).find('button[data-type="submit"]')
					.prop('disabled')) {
				$('#' + this._containerID).find('button[data-type="submit"]')
						.prop('disabled', false).removeClass('disabled');
			}
			if (ULTIMATE.Permission
					.get('admin.content.ultimate.canManageBlocks')) {
				new WCF.Action.Delete('ultimate\\data\\block\\BlockAction',
						$('.jsBlock'));
			}
			this._init();
			this._dialog.wcfDialog('close');
			this._notification.show();
		} catch (e) {
			// call child method if applicable
			var $showError = true;
			if ($showError !== false) {
				$(
						'<div class="ajaxDebugMessage"><p>' + e.message
								+ '</p></div>').wcfDialog({
					title : WCF.Language.get('wcf.global.error.title')
				});
			}
		}
	},

	/**
	 * Shows notification upon edit success.
	 * 
	 * @param {Object}
	 *            data
	 * @param {String}
	 *            textStatus
	 * @param {jQuery}
	 *            jqXHR
	 */
	_successEdit : function(data, textStatus, jqXHR) {
		if (this._notification === null) {
			this._notification = new WCF.System.Notification(WCF.Language
					.get('wcf.global.success.edit'));
		}
		try {
			this._dialog.wcfDialog('close');
			this._notification.show();
		} catch (e) {
			// call child method if applicable
			var $showError = true;
			if ($showError !== false) {
				$(
						'<div class="ajaxDebugMessage"><p>' + e.message
								+ '</p></div>').wcfDialog({
					title : WCF.Language.get('wcf.global.error.title')
				});
			}
		}
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
 * Namespace for ULTIMATE.Menu
 * 
 * @namespace
 */
ULTIMATE.Menu = {};

/**
 * Namespace for ULTIMATE.Menu.Item
 * 
 * @namespace
 */
ULTIMATE.Menu.Item = {};

/**
 * Creates a new MenuItemTransfer.
 * 
 * @param {String}
 *            elementID
 * @param {String}
 *            menuItemListID
 * @param {String}
 *            className
 * @param {Integer}
 *            offset
 * @param {String}
 *            type
 * @class Adds menu items to a menu item list.
 * @since version 1.0.0
 */
ULTIMATE.Menu.Item.Transfer = function(elementID, menuItemListID, className,
		offset, type) {
	this.init(elementID, menuItemListID, className, offset, type);
};
ULTIMATE.Menu.Item.Transfer.prototype = {

	/**
	 * Contains the element from which the items should be transferred.
	 * 
	 * @type jQuery
	 */
	_element : null,

	/**
	 * menu item list id
	 * 
	 * @type String
	 */
	_menuItemListID : '',

	/**
	 * action class name
	 * 
	 * @type String
	 */
	_className : '',

	/**
	 * notification object
	 * 
	 * @type WCF.System.Notification
	 */
	_notification : null,

	/**
	 * show order offset
	 * 
	 * @type Integer
	 */
	_offset : 0,

	/**
	 * proxy object
	 * 
	 * @type WCF.Action.Proxy
	 */
	_proxy : null,

	/**
	 * object structure
	 * 
	 * @type Object
	 */
	_structure : {},

	/**
	 * type of IDs (page, category, content, custom)
	 * 
	 * @type String
	 */
	_type : '',

	/**
	 * true if the submit is done
	 * 
	 * @type Boolean
	 */
	_submitDone : false,

	/**
	 * true if the request should be sent
	 * 
	 * @type Boolean
	 */
	_sendRequest : false,

	/**
	 * Initializes a menu item transfer.
	 * 
	 * @param {String}
	 *            elementID
	 * @param {String}
	 *            menuItemListID
	 * @param {String}
	 *            className
	 * @param {Integer}
	 *            offset
	 * @param {String}
	 *            type
	 */
	init : function(elementID, menuItemListID, className, offset, type) {
		this._element = $('#' + $.wcfEscapeID(elementID));
		this._menuItemListID = $.wcfEscapeID(menuItemListID);
		this._className = className;
		this._offset = (offset) ? offset : 0;
		this._type = type;
		this._proxy = new WCF.Action.Proxy({
			success : $.proxy(this._success, this)
		});

		this._structure = {};
		$('#' + this._menuItemListID).find('button[data-type="submit"]').click(
				function(event) {
					event.preventDefault();
				});

		this._element.parent('form')
				.submit($.proxy(this._stopFormSubmit, this));
		if (this._type != 'custom') {
			this._element.find('input:checkbox').change(
					$.proxy(this._change, this));
			this._element.find('button[data-type="submit"]').click(
					$.proxy(this._submit, this));
		}
		this._init();
	},

	/**
	 * Initializes the event handler.
	 */
	_init : function() {
		$('.sortableNode').on('remove', $.proxy(this._remove, this));
	},

	/**
	 * Called each time a menu item is removed with remove().
	 * 
	 * @param {jQuery.Event}
	 *            event
	 */
	_remove : function(event) {
		var $target = $(event.target);
		var $elementName = $target.data('objectName');

		this._element.find('input:disabled').each(
				$.proxy(function(index, item) {
					var $item = $(item);
					var $itemName = $item.data('name');
					if ($elementName == $itemName) {
						$item.prop('disabled', false).removeClass('disabled');
					}
				}, this));

		if ($('#' + this._menuItemListID).find('.sortableNode').length <= 1) {
			$('#' + this._menuItemListID).find('button[data-type="submit"]')
					.prop('disabled', true).addClass('disabled');
		}
	},

	/**
	 * Changes the state of the buttons.
	 */
	_change : function() {
		var checkedCheckboxes = this._element.find('input:checked').length;
		if (checkedCheckboxes) {
			this._element.find('button[data-type="submit"]').removeClass(
					'disabled').prop('disabled', false);
		} else {
			this._element.find('button[data-type="submit"]').addClass(
					'disabled').prop('disabled', true);
		}
	},

	/**
	 * Stops the form submit event.
	 * 
	 * @param {jQuery.Event}
	 *            event
	 * @return {Boolean}
	 */
	_stopFormSubmit : function(event) {
		event.preventDefault();
		if (this._type != 'custom')
			return;
		if (this._element.find('input[name="title"]').length == 0) {
			this._submit();
		} else if (this._element.find('input[name="title"]').length == 1) {
			this._submit();
		}
	},

	/**
	 * Saves object structure.
	 */
	_submit : function() {
		this._structure = {};
		if (this._type == 'custom') {
			var link = $('#link').val();
			var linkTitleFound = this._element.find('input[name="title"]');
			var linkTitle = '';
			var $data = {};
			// only add title to post values if linkTitle is not i18n
			if (linkTitleFound.length == 1) {
				linkTitle = $('#title').val();
				$data = $.extend(true, {
					title : linkTitle
				}, $data);
			} else if (linkTitleFound.length == 0) {
				// if it is i18n add it to post values accordingly
				var linkTitle_i18n = {};
				var $parent = this._element.parent();
				$parent.find('input[name^="title_i18n"]').each(
						$.proxy(function(index, listItem) {
							var $listItem = $(listItem);
							var $languageID = $listItem.attr('name').substring(
									11);
							$languageID = $languageID.substr(0,
									$languageID.length - 1);
							linkTitle_i18n[$languageID] = $listItem.val();
						}, this));
				$data = $.extend(true, {
					title_i18n : linkTitle_i18n
				}, $data);
			}
			this._structure['link'] = link;
			this._structure['linkTitle'] = linkTitle;
			// resets the form
			$('#link').val('http://');
			$('#title').val('');
			// send request
			var $parameters = $.extend(true, {
				data : {
					offset : this._offset,
					structure : this._structure,
					type : this._type,
					menuID : $('input[name="id"]').val()
				}
			}, {});

			$data = $.extend(true, {
				actionName : 'createAJAX',
				className : this._className,
				parameters : $parameters
			}, $data);

			this._proxy.setOption('data', $data);
			this._sendRequest = true;
		} else {
			this._element.find(
					'dl > dd > ul > li > label > input[type="checkbox"]').each(
					$.proxy(function(index, listItem) {
						var $listItem = $(listItem);
						var $parentID = $listItem.val();
						var $parent = $listItem.parent().parent();
						if ($parentID !== undefined) {
							$checkedParent = $listItem.prop('checked');
							this._getNestedElements($parent, $parentID);
							if (!this._structure[0]) {
								this._structure[0] = [];
							}
							if ($checkedParent) {
								this._structure[0].push($parentID);
								this._sendRequest = true;
								$listItem.prop('checked', false).prop(
										'disabled', true).addClass('disabled');
							}
						}
					}, this));
			// send request
			var $parameters = $.extend(true, {
				data : {
					offset : this._offset,
					structure : this._structure,
					type : this._type,
					menuID : $('input[name="id"]').val()
				}
			}, {});

			this._proxy.setOption('data', {
				actionName : 'createAJAX',
				className : this._className,
				parameters : $parameters
			});
		}
		if (this._element.find('input:not(:disabled)').length == 0) {
			this._change();
		}
		if (this._sendRequest) {
			this._proxy.sendRequest();
			this._submitDone = true;
		} else {
			this._notification = new WCF.System.Notification(WCF.Language
					.get('wcf.acp.ultimate.menu.noItemsSelected'));
			this._notification.show();
		}
	},

	/**
	 * Builds all nested elements.
	 * 
	 * @param {jQuery}
	 *            $parent
	 * @param {Integer}
	 *            $parentID
	 */
	_getNestedElements : function($parent, $parentID) {
		$parent.find('ul > li > label > input[type="checkbox"]').each(
				$.proxy(function(index, listItem) {
					var $objectID = $(listItem).val();
					var $checked = $(listItem).prop('checked');
					var $__parent = $(listItem).parent().parent();

					this._getNestedElements($__parent, $objectID);

					if (!this._structure[$parentID]) {
						this._structure[$parentID] = [];
					}
					if ($checked) {
						this._structure[$parentID].push($objectID);
						this._sendRequest = true;
						$(listItem).prop('checked', false).prop('disabled',
								true).addClass('disabled');
					}

				}, this));

	},

	/**
	 * Shows notification upon success.
	 * 
	 * @param {Object}
	 *            data
	 * @param {String}
	 *            textStatus
	 * @param {jQuery}
	 *            jqXHR
	 */
	_success : function(data, textStatus, jqXHR) {
		if (this._notification === null) {
			this._notification = new WCF.System.Notification(WCF.Language
					.get('wcf.global.success.edit'));
		}
		try {
			var data = data['returnValues'];
			for ( var $menuItemID in data) {
				var $newItemHtml = '<li id="' + WCF.getRandomID() + '" class="';
				$newItemHtml += 'sortableNode"';
				$newItemHtml += ' data-object-id="' + $menuItemID
						+ '"  data-object-name="'
						+ data[$menuItemID]['menuItemNameRaw'] + '">';
				$newItemHtml += '<span class="sortableNodeLabel">'
						+ '<span>'
						+ data[$menuItemID]['menuItemName']
						+ '</span><span class="statusDisplay sortableButtonContainer">';
				if (data[$menuItemID]['canDisable']) {
					$newItemHtml += '<span class="icon icon16 icon-check'
							+ (data[$menuItemID]['isDisabled'] ? '-empty' : '')
							+ ' jsToggleButton jsTooltip pointer" '
							+ 'title="'
							+ (data[$menuItemID]['isDisabled'] ? WCF.Language
									.get('wcf.global.button.enable')
									: WCF.Language
											.get('wcf.global.button.disable'))
							+ '" data-object-id="' + $menuItemID
							+ '" data-disable-message="'
							+ WCF.Language.get('wcf.global.button.disable')
							+ '" data-enable-message="'
							+ WCF.Language.get('wcf.global.button.enable')
							+ '"></span>';
				} else {
					$newItemHtml += '<span class="icon icon16 icon-check'
							+ (data[$menuItemID]['isDisabled'] ? '-empty' : '')
							+ ' disabled" '
							+ 'title="'
							+ (data[$menuItemID]['isDisabled'] ? WCF.Language
									.get('wcf.global.button.enable')
									: WCF.Language
											.get('wcf.global.button.disable'))
							+ '"></span>';
				}
				if (data[$menuItemID]['canDelete']) {
					$newItemHtml += '&nbsp;<span class="icon icon16 icon-remove'
							+ ' jsDeleteButton jsTooltip pointer" '
							+ 'title="'
							+ WCF.Language.get('wcf.global.button.delete')
							+ '" data-object-id="'
							+ $menuItemID
							+ '" data-confirm-message="'
							+ data[$menuItemID]['confirmMessage'] + '"></span>';
				} else {
					$newItemHtml += '<span class="icon icon16 icon-remove'
							+ ' disabled" ' + 'title="'
							+ WCF.Language.get('wcf.global.button.delete')
							+ '"></span>';
				}
				$newItemHtml += '</span></span></li>';
				var newEntry = $($newItemHtml);
				$('#' + this._menuItemListID + '> .sortableList').append(
						$newItemHtml);

				if ($('#' + this._menuItemListID).find(
						'button[data-type="submit"]').prop('disabled')) {
					$('#' + this._menuItemListID).find(
							'button[data-type="submit"]').prop('disabled',
							false).removeClass('disabled');
				}
			}

			this._init();
			this._notification.show();
		}
		// something happened
		catch (e) {
			// call child method if applicable
			var $showError = true;
			if ($showError !== false) {
				$(
						'<div class="ajaxDebugMessage"><p>' + e.message
								+ '</p></div>').wcfDialog({
					title : WCF.Language.get('wcf.global.error.title')
				});
			}
		}

	}
};

/**
 * Namespace for ULTIMATE.Widget
 * 
 * @namespace
 */
ULTIMATE.Widget = {};

/**
 * Creates a new WidgetEdit.
 * 
 * @param {jQuery}
 *            containerList
 * @class Manages the edit process of a widget.
 * @since version 1.0.0
 */
ULTIMATE.Widget.Edit = function(containerList) {
	this.init(containerList);
};
ULTIMATE.Widget.Edit.prototype = {
	/**
	 * Contains a list of all widgets.
	 * 
	 * @type jQuery
	 */
	_containerList : null,

	/**
	 * Contains the proxy.
	 * 
	 * @type WCF.Action.Proxy
	 */
	_proxy : null,

	/**
	 * Contains the dialog
	 * 
	 * @type jQuery
	 */
	_dialog : null,

	/**
	 * Contains the notification.
	 * 
	 * @type WCF.System.Notification
	 */
	_notification : null,

	/**
	 * Initializes a widget edit process.
	 * 
	 * @param {jQuery}
	 *            containerList
	 */
	init : function(containerList) {
		this._className = className;
		this._containerList = containerList;
		this._badgeList = badgeList;
		this._proxy = new WCF.Action.Proxy({
			sucess : $.proxy(this._success, this)
		});
		this.initButtons();
	},

	/**
	 * Initializes the button event listeners.
	 */
	initButtons : function() {
		this._containerList.each($.proxy(function(index, container) {
			$(container).find('.jsEditButton').bind('click',
					$.proxy(this._click, this));
		}, this));
	},

	/**
	 * Called each time an edit button is clicked.
	 * 
	 * @param {jQuery.Event}
	 *            event
	 */
	_click : function(event) {
		var $target = $(event.target);
		this._initDialog($target);

		// initialize dialog handler
		this._dialog.find('button[data-type="submit"]').click(
				$.proxy(this._submit, this));
		this._dialog.find('button[data-type="cancel"]').click(
				$.proxy(function(_event) {
					// close dialog
					this._dialog.ultimateDialog('close');
				}, this));
	},

	/**
	 * Initializes the dialog.
	 * 
	 * @param {jQuery}
	 *            $target
	 */
	_initDialog : function($target) {
		this._dialog = null;
		var $widgetTypeID = $('#widgetTypeIDs option:selected').val();
		var options = {
			// dialog
			title : WCF.Language.get('wcf.acp.ultimate.widget.edit'),

			// AJAX support
			ajax : true,
			data : {
				parameters : {
					widgetTypeID : $widgetTypeID,
					widgetID : $target.data('objectID')
				},
				actionName : 'loadWidgetOptions'
			},
			type : 'POST',
			url : 'index.php/Widget/?t=' + SECURITY_TOKEN + SID_ARG_2ND,

			// event callbacks
			onClose : null,
			onShow : null
		};
		this._dialog = WCF.showAJAXDialog('widgetEditor', true, options);
	},

	/**
	 * Called on submitting the dialog form.
	 * 
	 * @param {jQuery.Event}
	 *            event
	 */
	_submit : function(event) {
		var $widgetID = this._dialog.find('#widgetID').val();
		var $settings = {};
		// building setting structure
		this._dialog.find('.setting').each($.proxy(function(index, item) {
			var $item = $(item);
			var $value = $item.val();
			if (typeof $value == 'object') {
				$settings[$item.attr('name')] = [];
				$value.each($.proxy(function(innerIndex, optionValue) {
					var $optionValue = $(optionValue).val();
					$settings[$item.attr('name')].push($optionValue);
				}, this));
			} else {
				$settings[$item.attr('name')] = $value;
			}
		}, this));

		var $data = {
			parameters : {
				widgetID : $widgetID,
				settings : $settings
			},
			actionName : 'saveWidgetOptions'
		};
		// send request
		var $proxy = new WCF.Action.Proxy({
			success : $.proxy(this._successEdit, this),
			url : 'index.php/Widget/?t=' + SECURITY_TOKEN + SIG_ARG_2ND,
			data : $data
		});
		$proxy.sendRequest();
	},

	/**
	 * Called after successful save operation.
	 * 
	 * @param {Object}
	 *            data
	 * @param {String}
	 *            textStatus
	 * @param {jQuery}
	 *            jqXHR
	 */
	_successEdit : function(data, textStatus, jqXHR) {
		if (this._notification === null) {
			this._notification = new WCF.System.Notification(WCF.Language
					.get('wcf.global.success.edit'));
		}
		this._notification.show();
	}
};

/**
 * Creates a new WidgetTransfer.
 * 
 * @param {String}
 *            elementID
 * @param {String}
 *            widgetListID
 * @param {String}
 *            className
 * @param {Integer}
 *            offset
 * @class Adds menu items to a widget list.
 * @since version 1.0.0
 */
ULTIMATE.Widget.Transfer = function(elementID, widgetListID, className, offset) {
	this.init(elementID, widgetListID, className, offset);
};
ULTIMATE.Widget.Transfer.prototype = {
	/**
	 * Contains the element from which the widgets should be transferred.
	 * 
	 * @type jQuery
	 */
	_element : null,

	/**
	 * widget list id
	 * 
	 * @type String
	 */
	_widgetListID : '',

	/**
	 * action class name
	 * 
	 * @type String
	 */
	_className : '',

	/**
	 * notification object
	 * 
	 * @type WCF.System.Notification
	 */
	_notification : null,

	/**
	 * show order offset
	 * 
	 * @type Integer
	 */
	_offset : 0,

	/**
	 * proxy object
	 * 
	 * @type WCF.Action.Proxy
	 */
	_proxy : null,

	/**
	 * object structure
	 * 
	 * @type Object
	 */
	_structure : {},

	/**
	 * true if the submit is done
	 * 
	 * @type Boolean
	 */
	_submitDone : false,

	/**
	 * true if the request should be sent
	 * 
	 * @type Boolean
	 */
	_sendRequest : false,

	/**
	 * Initializes a widget transfer.
	 * 
	 * @param {String}
	 *            elementID
	 * @param {String}
	 *            widgetListID
	 * @param {String}
	 *            className
	 * @param {Integer}
	 *            offset
	 */
	init : function(elementID, widgetListID, className, offset) {
		this._element = $('#' + $.wcfEscapeID(elementID));
		this._widgetListID = $.wcfEscapeID(widgetListID);
		this._className = className;
		this._offset = (offset) ? offset : 0;
		this._proxy = new WCF.Action.Proxy({
			success : $.proxy(this._success, this)
		});
		this._structure = {};
		this._element.parent('form')
				.submit($.proxy(this._stopFormSubmit, this));
		this._element.find('select').change($.proxy(this._change, this));
		this._element.find('button[data-type="submit"]').click(
				$.proxy(this._submit, this));

	},

	/**
	 * Changes the state of the buttons.
	 */
	_change : function() {
		var selected = this._element.find('select option:selected');
		if (selected.val()) {
			this._element.find('button[data-type="submit"]').removeClass(
					'disabled').prop('disabled', false);
		} else {
			this._element.find('button[data-type="submit"]').addClass(
					'disabled').prop('disabled', true);
		}
	},

	/**
	 * Stops the form submit event.
	 * 
	 * @param {jQuery.Event}
	 *            event
	 * @return {Boolean}
	 */
	_stopFormSubmit : function(event) {
		event.preventDefault();
		return;
	},

	/**
	 * Saves object structure.
	 */
	_submit : function() {
		this._structure = {};
		var $selected = this._element
				.find('dl > dd > select > option:selected');
		var $objectID = $selected.val();
		if (!this._structure[0]) {
			this._structure[0] = [];
		}
		if ($objectID) {
			this._structure[0].push($objectID);
			this._sendRequest = true;
		}
		// send request
		var $parameters = $.extend(true, {
			data : {
				offset : this._offset,
				structure : this._structure,
				widgetAreaID : $('input[name="id"]').val()
			}
		}, {});

		this._proxy.setOption('data', {
			actionName : 'createAJAX',
			className : this._className,
			parameters : $parameters
		});

		this._change();

		if (this._sendRequest) {
			this._proxy.sendRequest();
			this._submitDone = true;
		} else {
			this._notification = new WCF.System.Notification(
					WCF.Language
							.get('wcf.acp.ultimate.widgetArea.widgetTypes.noItemsSelected'));
			this._notification.show();
		}
	},

	/**
	 * Shows notification upon success.
	 * 
	 * @param {Object}
	 *            data
	 * @param {String}
	 *            textStatus
	 * @param {jQuery}
	 *            jqXHR
	 */
	_success : function(data, textStatus, jqXHR) {
		if (this._notification === null) {
			this._notification = new WCF.System.Notification(WCF.Language
					.get('wcf.global.success.edit'));
		}
		try {
			var $data = data['returnValues'];
			var $widgetID = $data['widgetID'];
			var $newItemHtml = '<li id="' + WCF.getRandomID()
					+ '" class="sortableNode jsMenuItem" data-object-id="'
					+ $widgetID + '"  data-object-name="'
					+ $data[$widgetID]['widgetNameRaw'] + '">';
			$newItemHtml += '<span class="sortableNodeLabel"><span class="buttons">';
			if (ULTIMATE.Permission
					.get('admin.content.ultimate.canManageWidgets')) {
				$newItemHtml += '<span title="'
						+ WCF.Language.get('wcf.global.button.delete')
						+ '" class="icon icon16 icon-remove jsDeleteButton jsTooltip" data-object-id="'
						+ $widgetID
						+ '" data-confirm-message="'
						+ WCF.Language
								.get('wcf.acp.ultimate.widget.delete.sure')
						+ '"></span>';
			} else {
				$newItemHtml += '<span title="'
						+ WCF.Language.get('wcf.global.button.delete')
						+ '" class="icon icon16 icon-remove disabled"></span>';
			}
			if (ULTIMATE.Permission
					.get('admin.content.ultimate.canManageWidgets')) {
				$newItemHtml += '&nbsp;<span title="'
						+ (($data[$widgetID]['isDisabled']) ? WCF.Language
								.get('wcf.global.button.enable') : WCF.Language
								.get('wcf.global.button.disable'))
						+ '" class="icon icon16 icon-'
						+ (($data[$widgetID]['isDisabled']) ? 'off'
								: 'circle-blank')
						+ ' jsToggleButton jsTooltip" data-object-id="'
						+ $widgetID + '"></span>';
			} else {
				$newItemHtml += '&nbsp;<span title="'
						+ ($data[$widgetID]['isDisabled']) ? WCF.Language
						.get('wcf.global.button.enable') : WCF.Language
						.get('wcf.global.button.disable')
						+ '" class="icon icon16 icon-'
						+ (($data[$widgetID]['isDisabled']) ? 'off'
								: 'circle-blank') + ' disabled"></span>';
			}
			if (ULTIMATE.Permission
					.get('admin.content.ultimate.canManageWidgets')) {
				$newItemHtml += '&nbsp;<span title="'
						+ WCF.Language.get('wcf.global.button.edit')
						+ '" class="icon icon16 icon-pencil jsToggleButton jsTooltip" data-object-id="'
						+ $widgetID + '"></span>';
			} else {
				$newItemHtml += '&nbsp;<span title="'
						+ WCF.Language.get('wcf.global.button.edit')
						+ '" class="icon icon16 icon-pencil disabled"></span>';
			}
			$newItemHtml += '</span>&nbsp;<span class="title">';
			$newItemHtml += $data[$widgetID]['widgetName']
					+ '</span></span><ol class="sortableList" data-object-id="'
					+ $widgetID + '"></ol></li>';

			$('#' + this._widgetListID + '> .sortableList')
					.append($newItemHtml);
			if ($('#' + this._widgetListID).find('button[data-type="submit"]')
					.prop('disabled')) {
				$('#' + this._widgetListID).find('button[data-type="submit"]')
						.prop('disabled', false).removeClass('disabled');
			}

			if (ULTIMATE.Permission
					.get('admin.content.ultimate.canManageWidgets')) {
				new WCF.Action.Delete('ultimate\\data\\widget\\WidgetAction',
						$('.jsWidget'));
			}
			if (ULTIMATE.Permission
					.get('admin.content.ultimate.canManageWidgets')) {
				new WCF.Action.Toggle('ultimate\\data\\widget\\WidgetAction',
						$('.jsWidget'));
				new ULTIMATE.Widget.Edit($('.jsWidget'));
			}
			this._notification.show();
		}
		// something happened
		catch (e) {
			// call child method if applicable
			var $showError = true;
			if ($showError !== false) {
				$(
						'<div class="ajaxDebugMessage"><p>' + e.message
								+ '</p></div>').wcfDialog({
					title : WCF.Language.get('wcf.global.error.title')
				});
			}
		}

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

/**
 * Namespace for ULTIMATE.Content
 * 
 * @namespace
 */
ULTIMATE.Content = {};

/**
 * @see WCF.InlineEditor
 */
ULTIMATE.Content.InlineEditor = WCF.Message.InlineEditor.extend({
	/**
	 * @see	WCF.Message.InlineEditor._getClassName()
	 */
	_getClassName: function() {
		return 'ultimate\\data\\content\\ContentAction';
	},
	
	/**
	 * Saves editor contents.
	 */
	_save: function() {
		var $container = this._container[this._activeElementID];
		var $objectID = $container.data('objectID');
		var $message = '';
		var $isI18n = $container.data('isI18n');
		if ($.browser.mobile) {
			$message = $('#' + this._messageEditorIDPrefix + $objectID).val();
		}
		else {
			var $ckEditor = $('#' + this._messageEditorIDPrefix + $objectID).ckeditorGet();
			$message = $ckEditor.getData();
		}
		
		this._proxy.setOption('data', {
			actionName: 'save',
			className: this._getClassName(),
			interfaceName: 'wcf\\data\\IMessageInlineEditorAction',
			parameters: {
				containerID: this._containerID,
				data: {
					message: $message,
					isI18n: $isI18n
				},
				objectID: $objectID
			}
		});
		this._proxy.sendRequest();
		
		this._hideEditor();
	},
});

/**
 * Like support for contents
 * 
 * @see	WCF.Like
 */
ULTIMATE.Content.Like = WCF.Like.extend({
	/**
	 * @see	WCF.Like._getContainers()
	 */
	_getContainers: function() {
		return $('article.message');
	},

	/**
	 * @see	WCF.Like._getObjectID()
	 */
	_getObjectID: function(containerID) {
		return this._containers[containerID].data('objectID');
	},

	/**
	 * @see	WCF.Like._getWidgetContainer()
	 */
	_getWidgetContainer: function(containerID) {
		return this._containers[containerID].find('.messageHeader');
	},
	
	/**
	 * @see	WCF.Like._buildWidget()
	 */
	_buildWidget: function(containerID, likeButton, dislikeButton, badge, summary) {
		var $widgetContainer = this._getWidgetContainer(containerID);
		if (this._canLike) {
			var $smallButtons = this._containers[containerID].find('.smallButtons');
			likeButton.insertBefore($smallButtons.find('.toTopLink'));
			dislikeButton.insertBefore($smallButtons.find('.toTopLink'));
			dislikeButton.find('a').addClass('button');
			likeButton.find('a').addClass('button');
		}
		
		if (summary) {
			summary.appendTo(this._containers[containerID].find('.messageBody > .messageFooter'));
			summary.addClass('messageFooterNote');
		}
		$widgetContainer.find('.likeContainer').append(badge);
	},
	
	/**
	 * Sets button active state.
	 * 
	 * @param 	jquery		likeButton
	 * @param 	jquery		dislikeButton
	 * @param	integer		likeStatus
	 */
	_setActiveState: function(likeButton, dislikeButton, likeStatus) {
		likeButton = likeButton.find('.button').removeClass('active');
		dislikeButton = dislikeButton.find('.button').removeClass('active');
		
		if (likeStatus == 1) {
			likeButton.addClass('active');
		}
		else if (likeStatus == -1) {
			dislikeButton.addClass('active');
		}
	},
	
	/**
	 * @see	WCF.Like._addWidget()
	 */
	_addWidget: function(containerID, widget) {}
});
