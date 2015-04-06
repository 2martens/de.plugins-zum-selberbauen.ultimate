'use strict';

/**
 * Class and function collection for ULTIMATE CMS Tagging
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 */

$.fn.extend({
	/**
	 * Returns the caret position of current element. If the element
	 * does not equal input[type=text], input[type=password] or
	 * textarea, -1 is returned.
	 * 
	 * @return	{number}
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
	 * @param	{number}	position
	 * @return	{boolean}
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
 * @param	{string}	elementID
 * @param	{string}	inputID
 * @param	{boolean}	forceSelection
 * @param	{Object}	values
 * @param	{Object}	availableLanguages
 */
ULTIMATE.Tagging.MultipleLanguageInput = WCF.MultipleLanguageInput.extend({
	/**
	 * Contains the list of buttons.
	 * @type {Object}
	 */
	_buttonList: null,
	
	/**
	 * Contains the list of lists.
	 * @type {Object}
	 */
	_listList: null,
	
	/**
	 * Contains the list of wrappers.
	 * @type {Object}
	 */
	_wrapperList: null,
	
	/**
	 * Contains the list of elements.
	 * @type {Object}
	 */
	_elementList: null,
	
	/**
	 * Contains base for hidden input fields.
	 * @type {string}
	 */
	_hiddenInput: '',
	
	/**
	 * The hidden container.
	 * @type {jQuery}
	 */
	_hiddenContainer: null,
	
	/**
	 * The input container.
	 * @type {jQuery}
	 */
	_inputContainer: null,
	
	/**
	 * Initializes multiple language ability for given element id.
	 * 
	 * @param	{string}		elementID
	 * @param	{string}		inputID
	 * @param	{boolean}		forceSelection
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
		this._hiddenInput = '#' + $.wcfEscapeID(inputID);
		this._hiddenContainer = $('#tagSearchHidden');
		this._inputContainer = $('#tagSearchWrap');
		
		for (var $languageID in this._availableLanguages) {
            if (this._availableLanguages.hasOwnProperty($languageID)) {
                this._elementList[$languageID] = $('#' + $.wcfEscapeID(elementID + $languageID));
                $(this._hiddenInput + 'Wrap' + $languageID).detach().appendTo(this._hiddenContainer);
            }
		}
		
		// default to current user language
		this._languageID = LANGUAGE_ID;
		this._element = this._elementList[this._languageID];
		$(this._hiddenInput + 'Wrap' + this._languageID).detach().appendTo(this._inputContainer);
		if (this._element.length == 0) {
			console.debug("[WCF.MultipleLanguageInput] element id '" + elementID + "' is unknown");
			return;
		}
		$('.tagContainer').hide();
		$('#tagContainer' + this._languageID).show();
		
		// build selection handler
		var $enableOnInit = ($.getLength(this._values) > 0);
		this._insertedDataAfterInit = $enableOnInit;
		this._prepareElement($enableOnInit);
		
		// listen for submit event
		this._element.parents('form').submit($.proxy(this._submit, this));
		$(this._hiddenInput + this._languageID).keydown($.proxy(this._inputKeydown, this));
		
		this._didInit = true;
	},
	
	/**
	 * Sets the necessary variables.
	 */
	_setVariables: function() {
		this._element = this._elementList[this._languageID];
	},
	
	/**
	 * Called when a key is down on the input.
	 * 
	 * @param {jQuery.Event} event
	 */
	_inputKeydown: function(event) {
		$(this._hiddenInput + this._languageID).focus();
		if (event && event.which == 188) {
			$(this._hiddenInput + this._languageID).val('');
		}
	},
	
	/**
	 * Builds language handler.
	 * 
	 * @param	{boolean}		enableOnInit
	 */
	_prepareElement: function(enableOnInit) {
		var $wrapper = $('#tagSearchWrap');
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
	 * @param {jQuery.Event} event
	 */
	_changeLanguage: function(event) {
		var $button = $(event.currentTarget);
		this._insertedDataAfterInit = true;
		
		// save current value
		$('#tagContainer' + this._languageID).hide();
		$(this._hiddenInput + 'Wrap' + this._languageID).detach().appendTo(this._hiddenContainer);
		
		// set new language
		this._languageID = parseInt($button.data('languageID'));
		this._setVariables();
		$('#tagContainer' + this._languageID).show();
		$(this._hiddenInput + 'Wrap' + this._languageID).detach().appendTo(this._inputContainer);
		
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
			$(this._inputHidden + this._languageID).blur().focus();
		}
	},
	
	/**
	 * Handles dropdown actions.
	 * 
	 * @param	{string}	containerID
	 * @param	{string}	action
	 */
	_handleAction: function(containerID, action) {
		if (action === 'open') {
			this._enable();
		}
		else {
			this._closeSelection();
		}
	},
	
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
	 * @type {number}
	 */
	_languageID: 0,
	
	/**
     * @param   {string}    itemListSelector
     * @param   {string}    searchInputSelector
     * @param   {number}    maxLength
	 * @param	{number}	languageID
	 * @see	WCF.EditableItemList.init()
	 */
	init: function(itemListSelector, searchInputSelector, maxLength, languageID) {
		this._languageID = languageID;
		this._allowCustomInput = true;
		this._maxLength = maxLength;
		
		this._itemList = $(itemListSelector);
		this._searchInput = $(searchInputSelector);
		this._data = { };
		
		if (!this._itemList.length || !this._searchInput.length) {
			console.debug("[WCF.EditableItemList] Item list and/or search input do not exist, aborting.");
			return;
		}
		
		this._objectID = this._getObjectID();
		this._objectTypeID = this._getObjectTypeID();
		
		// bind item listener
		this._itemList.find('.jsEditableItem').click($.proxy(this._click, this));
		
		// create item list
		if (!this._itemList.children('ul').length) {
			$('<ul />').appendTo(this._itemList);
		}
		this._itemList = this._itemList.children('ul');
		
		// bind form submit
		this._form = this._itemList.parents('form').submit($.proxy(this._submit, this));
		
		if (this._allowCustomInput) {
			var self = this;
			this._searchInput.keydown($.proxy(this._keyDown, this)).on('paste', function() {
				setTimeout(function() { self._onPaste(); }, 100);
			});
		}
		
		this._data = [ ];
		this._search = new ULTIMATE.Tagging.TagSearch(searchInputSelector, this._languageID, $.proxy(this.addItem, this));
		this._itemList.addClass('tagList');
		
		// block form submit through [ENTER]
		this._searchInput.parents('.dropdown').data('preventSubmit', true);
	},
	
	/**
	 * @see	WCF.EditableItemList._keyDown()
     * @return  {boolean}
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
			
			return this._searchInput.val().length < this._maxLength;
		}
		
		return false;
	},
	
	/**
	 * Handles the key down event.
	 * 
	 * @param {jQuery.Event} event
     * @return {boolean}
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

/**
 * Multiple language tag search.
 * 
 * @see	WCF.Tagging.TagSearch
 */
ULTIMATE.Tagging.TagSearch = WCF.Tagging.TagSearch.extend({
	/**
	 * @see	WCF.Search.Base._className
	 */
	_className: 'ultimate\\data\\tag\\TagAction',
	
	/**
	 * The language id.
	 * @type {number}
	 */
	_languageID: 0,
	
	/**
	 * Initializes the Ultimate Tag Search.
	 * 
	 * @param	{string}	searchInput
	 * @param	{number}	languageID
	 * @param	{Function}	callback
	 * @param	{Array}		excludedSearchValues
	 * @param	{boolean}	commaSeparated
	 * 
	 * @see	WCF.Search.Base.init()
	 */
	init: function(searchInput, languageID, callback, excludedSearchValues, commaSeparated) {
		if (callback !== null && callback !== undefined && !$.isFunction(callback)) {
			console.debug("[ULTIMATE.Tagging.TagSearch] The given callback is invalid, aborting.");
			return;
		}
		
		this._languageID = languageID;
		
		this._callback = (callback) ? callback : null;
		this._excludedSearchValues = [];
		if (excludedSearchValues) {
			this._excludedSearchValues = excludedSearchValues;
		}
		
		this._searchInput = $(searchInput);
		if (!this._searchInput.length) {
			console.debug("[ULTIMATE.Tagging.TagSearch] Selector '" + searchInput + "' for search input is invalid, aborting.");
			return;
		}
		
		this._searchInput.keydown($.proxy(this._keyDown, this)).keyup($.proxy(this._keyUp, this));
		this._list = $('<ul class="dropdownMenu" />').insertAfter(this._searchInput);
		
		this._commaSeperated = !!(commaSeparated);
		this._oldSearchString = [ ];
		
		this._itemCount = 0;
		this._itemIndex = -1;
		
		this._proxy = new WCF.Action.Proxy({
			showLoadingOverlay: false,
			success: $.proxy(this._success, this),
			autoAbortPrevious: true
		});
		
		if (this._searchInput.is('input')) {
			this._searchInput.attr('autocomplete', 'off');
		}
		
		this._searchInput.blur($.proxy(this._blur, this));
		
		WCF.Dropdown.initDropdownFragment(this._searchInput.parent(), this._list);
	},
	
	/**
	 * Blocks execution of 'Enter' event.
	 * 
	 * @param	{Object}	event
	 */
	_keyDown: function(event) {
		if (event.which === $.ui.keyCode.ENTER) {
			var $dropdown = this._searchInput.parents('.dropdown');
			
			if ($dropdown.data('disableAutoFocus')) {
				if (this._itemIndex !== -1) {
					event.preventDefault();
				}
			}
			else if ($dropdown.data('preventSubmit') || this._itemIndex !== -1) {
				event.preventDefault();
			}
		}
	},
	
	/**
	 * Performs a search upon key up.
	 * 
	 * @param {jQuery.Event} event
	 */
	_keyUp: function(event) {
		// handle arrow keys and return key
		switch (event.which) {
			case 37: // arrow-left
			case 39: // arrow-right
				return;
			break;
			
			case 38: // arrow up
				this._selectPreviousItem();
				return;
			break;
			
			case 40: // arrow down
				this._selectNextItem();
				return;
			break;
			
			case 13: // return key
				return this._selectElement(event);
			break;
		}
		
		var $content = this._getSearchString(event);
		if ($content === '') {
			this._clearList(true);
		}
		else if ($content.length >= this._triggerLength) {
			var $parameters = {
				data: {
					excludedSearchValues: this._excludedSearchValues,
					searchString: $content,
					languageID: this._languageID
				}
			};
			
			this._searchInput.parents('.searchBar').addClass('loading');
			this._proxy.setOption('data', {
				actionName: 'getSearchResultList',
				className: this._className,
				interfaceName: 'wcf\\data\\ISearchAction',
				parameters: this._getParameters($parameters)
			});
			this._proxy.sendRequest();
		}
		else {
			// input below trigger length
			this._clearList(false);
		}
	},
	
	/**
	 * Evaluates search results.
	 * 
	 * @param	{Object}	data
	 * @param	{string}	textStatus
	 * @param	{jQuery}	jqXHR
	 */
	_success: function(data, textStatus, jqXHR) {
		this._clearList(false);
		this._searchInput.parents('.searchBar').removeClass('loading');
		
		if ($.getLength(data.returnValues)) {
			for (var $i in data.returnValues) {
				var $item = data.returnValues[$i];
				this._createListItem($item);
			}
		}
		else if (!this._handleEmptyResult()) {
			return;
		}
		
		WCF.CloseOverlayHandler.addCallback('WCF.Search.Base', $.proxy(function() { this._clearList(); }, this));
		
		var $containerID = this._searchInput.parents('.dropdown').wcfIdentify();
		if (!WCF.Dropdown.getDropdownMenu($containerID).hasClass('dropdownOpen')) {
			WCF.Dropdown.toggleDropdown($containerID);
		}
		
		// pre-select first item
		this._itemIndex = -1;
		if (!WCF.Dropdown.getDropdown($containerID).data('disableAutoFocus')) {
			this._selectNextItem();
		}
		
		this._searchInput.focus();
	},
	
	/**
	 * Closes the suggestion list and clears search input on demand.
	 * 
	 * @param	{boolean}	clearSearchInput
	 */
	_clearList: function(clearSearchInput) {
		if (clearSearchInput && !this._commaSeperated) {
			this._searchInput.val('');
		}
		
		// close dropdown
        var dropdown = this._searchInput.parent().wcfIdentify();
		WCF.Dropdown.getDropdown(dropdown).removeClass('dropdownOpen');
		WCF.Dropdown.getDropdownMenu(dropdown).removeClass('dropdownOpen');
		
		this._list.end().empty();
		
		WCF.CloseOverlayHandler.removeCallback('WCF.Search.Base');
		
		// reset item navigation
		this._itemCount = 0;
		this._itemIndex = -1;
	}
});
