$.fn.extend({
	/**
	 * Returns the caret position of current element. If the element
	 * does not equal input[type=text], input[type=password] or
	 * textarea, -1 is returned.
	 * 
	 * @return	integer
	 */
	getCaret1: function() {
		if (this.is('input')) {
//			if (this.attr('type') != 'text' && this.attr('type') != 'password') {
//				return -1;
//			}
		}
		else if (!this.is('textarea')) {
			return -1;
		}
		
		var $position = 0;
		var $element = this.get(0);
		if (document.selection) { // IE 8
			// set focus to enable caret on this element
			this.focus();
			
			var $selection = document.selection.createRange();
			$selection.moveStart('character', -this.val().length);
			$position = $selection.text.length;
		}
		else if ($element.selectionStart || $element.selectionStart == '0') { // Opera, Chrome, Firefox, Safari, IE 9+
			$position = parseInt($element.selectionStart);
		}
		
		return $position;
	},
	
	/**
	 * Sets the caret position of current element. If the element
	 * does not equal input[type=text], input[type=password] or
	 * textarea, false is returned.
	 * 
	 * @param	integer		position
	 * @return	boolean
	 */
	setCaret1: function (position) {
		if (this.is('input')) {
//			if (this.attr('type') != 'text' && this.attr('type') != 'password') {
//				return false;
//			}
		}
		else if (!this.is('textarea')) {
			return false;
		}
		
		var $element = this.get(0);
		
		// set focus to enable caret on this element
		this.focus();
		if (document.selection) { // IE 8
			var $selection = document.selection.createRange();
			$selection.moveStart('character', position);
			$selection.moveEnd('character', 0);
			$selection.select();
		}
		else if ($element.selectionStart || $element.selectionStart == '0') { // Opera, Chrome, Firefox, Safari, IE 9+
			$element.selectionStart = position;
			$element.selectionEnd = position;
		}
		
		return true;
	}
});


/**
 * Namespace for ULTIMATE.Tagging
 * @namespace
 */
ULTIMATE.Tagging = {};

/**
 * Handles multiple tag input fields.
 * 
 * @param	{String}	elementID
 * @param	{String}	inputID
 * @param	{Boolean}	forceSelection
 * @param	{Object}	values
 * @param	{Object}	availableLanguages
 */
ULTIMATE.Tagging.MultipleLanguageInput = WCF.MultipleLanguageInput.extend({
	/**
	 * Contains the list of buttons.
	 * @var	Object
	 */
	_buttonList: null,
	
	/**
	 * Contains the list of lists.
	 * @var	Object
	 */
	_listList: null,
	
	/**
	 * Contains the list of wrappers.
	 * @var	Object
	 */
	_wrapperList: null,
	
	/**
	 * Contains the list of elements.
	 * @var	Object
	 */
	_elementList: null,
	
	/**
	 * Contains the input element.
	 * @var	jQuery
	 */
	_input: null,
	
	/**
	 * Contains base for hidden input fields.
	 * @var	String
	 */
	_hiddenInput: '',
	
	/**
	 * Initializes multiple language ability for given element id.
	 * 
	 * @param	{String}		elementID
	 * @param	{String}		inputID
	 * @param	{Boolean}		forceSelection
	 * @param	{Object}		values
	 * @param	{Object}		availableLanguages
	 */
	init: function(elementID, inputID, forceSelection, values, availableLanguages) {
		this._forceSelection = forceSelection;
		this._values = values;
		this._availableLanguages = availableLanguages;
		this._buttonList = {};
		this._listList = {};
		this._wrapperList = {};
		this._elementList = {};
		this._input = $('#' + $.wcfEscapeID(inputID + 'Real'));
		this._hiddenInput = '#' + $.wcfEscapeID(inputID);
		
		for (var $languageID in this._availableLanguages) {
			$element = $('#' + $.wcfEscapeID(elementID + $languageID));
			this._elementList[$languageID] = $element;
		}
		
		// default to current user language
		this._languageID = LANGUAGE_ID;
		this._element = this._elementList[this._languageID];
		if (this._element.length == 0) {
			console.debug("[WCF.MultipleLanguageInput] element id '" + elementID + "' is unknown");
			return;
		}
		$('.tagContainer').hide();
		$('#tagContainer' + this._languageID).show();
		$(this._hiddenInput + this._languageID).hide();
		
		// build selection handler
		var $enableOnInit = ($.getLength(this._values) > 0) ? true : false;
		this._insertedDataAfterInit = $enableOnInit;
		this._prepareElement($enableOnInit);
		
		// listen for submit event
		this._element.parents('form').submit($.proxy(this._submit, this));
		this._input.keyup($.proxy(this._inputChange, this));
		this._input.keydown($.proxy(this._inputKeydown, this));
		
		this._didInit = true;
	},
	
	/**
	 * Sets the necessary variables.
	 */
	_setVariables: function() {
//		this._button = this._buttonList[this._languageID];
//		this._list = this._listList[this._languageID];
		this._element = this._elementList[this._languageID];
	},
	
	/**
	 * Called when input changes.
	 * 
	 * @param	{jQuery}	event
	 */
	_inputChange: function(event) {
		var content = this._input.val();
		console.log('inputChange: ' + content);
		$(this._hiddenInput + this._languageID).val(content);
	},
	
	/**
	 * Called when a key is down on the input.
	 * 
	 * @param	{jQuery}	event
	 */
	_inputKeydown: function(event) {
		var position = this._input.getCaret();
		$(this._hiddenInput + this._languageID).setCaret(position);
		$(this._hiddenInput + this._languageID).trigger(event);
		this._input.focus();
		if (event && event.which == 188) {
			this._input.val('');
		}
	},
	
	/**
	 * Builds language handler.
	 * 
	 * @param	{Boolean}		enableOnInit
	 */
	_prepareElement: function(enableOnInit) {
		this._input.wrap('<div class="dropdown preInput" />');
		var $wrapper = this._input.parent();
		this._button = $('<p class="button dropdownToggle"><span>' + WCF.Language.get('wcf.global.button.disabledI18n') + '</span></p>').prependTo($wrapper);
		
		// insert list
		this._list = $('<ul class="dropdownMenu"></ul>').insertAfter(this._button);
		
		// add a special class if next item is a textarea
		if (this._button.nextAll('textarea').length) {
			this._button.addClass('dropdownCaptionTextarea');
		}
		else {
			this._button.addClass('dropdownCaption');
		}
		
		// insert available languages
		for (var $languageID in this._availableLanguages) {
			$('<li><span>' + this._availableLanguages[$languageID] + '</span></li>').data('languageID', $languageID).click($.proxy(this._changeLanguage, this)).appendTo(this._list);
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
	 * @param	{jQuery}	event
	 */
	_changeLanguage: function(event) {
		var $button = $(event.currentTarget);
		this._insertedDataAfterInit = true;
		
		// save current value
		$('#tagContainer' + this._languageID).hide();
		
		// set new language
		this._languageID = parseInt($button.data('languageID'));
		this._setVariables();
		$('#tagContainer' + this._languageID).show();
		$(this._hiddenInput + this._languageID).hide();
		
//		this._initDropdown(true);
		
		$button = this._list.children('li').filter(function() {
		    return $(this).data('languageID') && $(this).data('languageID') == this._languageID;
		});
		
		// update marking
		this._list.children('li').removeClass('active');
		$button.addClass('active');
		
		// update label
		this._button.children('span').addClass('active').text(this._availableLanguages[this._languageID]);
		
		// close selection and set focus on input element
		if (this._didInit) {
			this._input.blur().focus();
		}
	},
	
//	/**
//	 * Shows the language selection.
//	 */
//	_showSelection: function() {
//		if (this._isEnabled) {
//			// display status for each language
//			this._list.children('li').each($.proxy(function(index, listItem) {
//				var $listItem = $(listItem);
//				var $languageID = $listItem.data('languageID');
//			}, this));
//		}
//	},
	
	/**
	 * Prepares language variables on before submit.
	 */
	_submit: function() {
		// insert hidden form elements on before submit
		if (!this._isEnabled) {
			return 0xDEADBEEF;
		}
	}
});

/**
 * Editable multiple language tag list.
 * 
 * @see	WCF.EditableItemList
 */
ULTIMATE.Tagging.TagList = WCF.Tagging.TagList.extend({
	/**
	 * language ID
	 * @var Integer
	 */
	_languageID: 0,
	
	/**
	 * @param	{Integer}	languageID
	 * @see	WCF.EditableItemList.init()
	 */
	init: function(itemListSelector, searchInputSelector, maxLength, languageID) {
		this._languageID = languageID;
		this._super(itemListSelector, searchInputSelector, maxLength);
	},
	
	/**
	 * @see	WCF.EditableItemList._keyDown()
	 */
	_keyDown: function(event) {
		if (this._keyDown1(event)) {
			// ignore submit event
			if (event === null) {
				return true;
			}
			
			var $keyCode = event.which;
			// allow [backspace], [escape], [enter] and [delete]
			if ($keyCode === 8 || $keyCode === 27 || $keyCode === 13 || $keyCode === 46) {
				return true;
			}
			else if ($keyCode > 36 && $keyCode < 41) {
				// allow arrow keys (37-40)
				return true;
			}
			
			if (this._searchInput.val().length >= this._maxLength) {
				return false;
			}
			
			return true;
		}
		
		return false;
	},
	
	/**
	 * Handles the key down event.
	 * 
	 * @param	object		event
	 */
	_keyDown1: function(event) {
		// 188 = [,]
		if (event === null || event.which === 188) {
			var $value = $.trim(this._searchInput.val());
			
			// read everything left from caret position
			if (event && event.which === 188) {
				$value = $value.substring(0, this._searchInput.val().length);
			}
			
			if ($value === '') {
				return true;
			}
			
			this.addItem({
				objectID: 0,
				label: $value
			});
			
			// reset input
			if (event && event.which === 188) {
				this._searchInput.val('');
			}
			else {
				this._searchInput.val('');
			}
			
			if (event !== null) {
				event.stopPropagation();
			}
			
			return false;
		}
		
		return true;
	},
	
	/**
	 * @see	WCF.EditableItemList._submit()
	 */
	_submit: function() {
		this._keyDown(null);
		var data = [];
		for (var $i = 0, $length = this._data.length; $i < $length; $i++) {
			// deleting items leaves crappy indices
			if (this._data[$i]) {
				data.push(this._data[$i]);
			}
		};
		$('<input type="hidden" name="tags_i18n[' + this._languageID + ']" value="' + data.join(', ') + '" />').appendTo(this._form);
	}
});