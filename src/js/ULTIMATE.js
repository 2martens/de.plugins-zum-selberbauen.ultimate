/**
 * Class and function collection for ULTIMATE CMS.
 * 
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 */

// a little tweak to know, when remove was used
(function($, undefined) {
	var _empty = $.fn.empty;
	$.fn.empty = function() {
		$( this ).triggerHandler( "empty" );
		return _empty.call( $(this) );
	};
})( jQuery );

// extends $.ui.resizable to avoid using deprecated method
(function($, undefined) {
	$.extend($.ui.resizable, {
		_propagate: function(n, event) {
			// prevents deprecated usage
			n.call(this, [event, this.ui()]);
			(n != "resize" && this._trigger(n, event, this.ui()));
		}
	});
});

/**
 * Initialize the UTLIMATE namespace.
 * @namespace
 */
var ULTIMATE = {};


/**
 * JSON API
 * 
 * @since	version 1.0.0
 */
ULTIMATE.JSON = {
	
	init: function() {
		
	},
		
	/**
	 * Encodes a given variable.
	 * 
	 * @param	{Object}	variable
	 * @return	{String}
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
 * @namespace
 */
ULTIMATE.Button = {};

/**
 * Handles button replacements.
 * 
 * @param	{String}	buttonID
 * @param	{String}	checkElementID
 * @param	{String}	action
 * @constructor
 * @since	version 1.0.0
 */
ULTIMATE.Button.Replacement = function(buttonID, checkElementID, action) { this.init(buttonID, checkElementID, action); };
ULTIMATE.Button.Replacement.prototype = {
	/**
	 * target input[type=submit] element
	 * @type	jQuery
	 */
	_button: null,
	
	/**
	 * the button value
	 * @type String
	 */
	_buttonValue: '',
	
	/**
	 * element to check for changes
	 * @type	jQuery
	 */
	_checkElement: null,
	
	/**
	 * the initial date
	 * @type	Date
	 */
	_initialValueDateTime: null,
	
	/**
	 * the last date
	 * @type	Date
	 */
	_lastValueDateTime: null,
	
	/**
	 * the initial status id
	 * @type	Integer
	 */
	_initialStatusID: 0,
	
	/**
	 * action parameter
	 * @type	String
	 */
	_action: '',
	
	/**
	 * Contains the language variables for the save action.
	 * @type	Object
	 */
	_saveMap: {
		0: 'ultimate.button.saveAsDraft',
		1: 'ultimate.button.saveAsPending'
	},
	
	/**
	 * Contains the language variables for the publish action.
	 * @type	Object
	 */
	_publishMap: {
		0: 'ultimate.button.publish',
		1: 'ultimate.button.schedule',
		2: 'ultimate.button.update'
	},
	
	/**
	 * Initializes the ButtonReplacement API.
	 * 
	 * @param	{String}	buttonID
	 * @param	{String}	checkElementID
	 * @param	{String}	action
	 */
	init: function(buttonID, checkElementID, action) {
		this._button = $('#' + $.wcfEscapeID(buttonID));
		this._buttonValue = this._button.val();
		this._checkElement = $('#' + $.wcfEscapeID(checkElementID));
		this._action = action;
		
		if (this._action == 'save') {
			this._initialStatusID = this._checkElement.val();
		} else if (this._action == 'publish') {
			var $dateObj = this._checkElement.datetimepicker( 'getDate' );
			this._initialValueDateTime = $dateObj;
			this._lastValueDateTime = $dateObj;
		}
		
		this._checkElement.change($.proxy(this._change, this));
		this._change();
	},

	/**
	 * Changes button label.
	 */
	_change: function() {
		if (this._action == 'save') {
			var $currentValue = this._checkElement.val();
			var $languageOutput = WCF.Language.get(this._saveMap[$currentValue]);
			if ($currentValue >= 2) {
				this._button.attr('disabled', 'disabled').prop('disabled', true);
				this._button.addClass('ultimateHidden');
			} else if ($currentValue == 0 || $currentValue == 1) {
				this._button.removeClass('ultimateHidden');
				this._button.removeAttr('disabled').prop('disabled', false);
				this._button.val($languageOutput);
			}
		} else if (this._action == 'publish') {
			var $dateObj = this._checkElement.datetimepicker( 'getDate' );
			var $dateNow = new Date();
			
			var $updateButton = WCF.Language.get(this._publishMap[2]);
			var $isUpdateSzenario = ($updateButton == this._buttonValue);
			
			if ($dateObj > $dateNow) {
				if ($isUpdateSzenario && (this._lastValueDateTime > $dateNow)) {
					return;
				}
				if ($isUpdateSzenario && this._initialValueDateTime > $dateNow) this._button.val($updateButton);
				else this._button.val(WCF.Language.get(this._publishMap[1]));
			} else {
				if ($isUpdateSzenario && (this._lastValueDateTime < $dateNow)) {
					return;
				}
				if ($isUpdateSzenario && this._initialValueDateTime < $dateNow) this._button.val($updateButton);
				else this._button.val(WCF.Language.get(this._publishMap[0]));
			}
			this._lastValueDateTime = $dateObj;
		}
	}
};

/**
 * Namespace for ULTIMATE.Block
 * @namespace
 */
ULTIMATE.Block = {};

/**
 * Transfers new blocks to a template.
 * 
 * @param	{String}	elementID
 * @param	{String}	containerID
 * @param	{String}	className	the action class name
 * @class	Adds blocks to a block list.
 * @since	version 1.0.0
 */
ULTIMATE.Block.Transfer = function(elementID, containerID, className){ this.init(elementID, containerID, className); };
ULTIMATE.Block.Transfer.prototype = {
	/**
	 * Contains the element from which the blocks should be transferred.
	 * @type	jQuery
	 */
	_element: null,
	
	/**
	 * Contains the element to which the blocks should be transferred.
	 * @type	jQuery
	 */
	_container: null,
	
	/**
	 * Contains the container ID.
	 * @type	String
	 */
	_containerID: '',
	
	/**
	 * Contains a notification.
	 * @type	WCF.System.Notification
	 */
	_notification: null,
	
	/**
	 * Contains a proxy object.
	 * @type	WCF.Action.Proxy
	 */
	_proxy: null,
	
	/**
	 * Contains a list of all available block options.
	 * @type	Array
	 */
	_optionList: [],
	
	/**
	 * Contains a dialog object.
	 * @type	jQuery
	 */
	_dialog: null,
		
	/**
	 * Initializes the BlockTransfer API.
	 * 
	 * @param	{String}	elementID
	 * @param	{String}	containerID
	 * @param	{String}	className
	 */
	init: function(elementID, containerID, className) {
		this._element = $('#' + $.wcfEscapeID(elementID));
		this._container = $('#' + $.wcfEscapeID(containerID));
		this._containerID = $.wcfEscapeID(containerID);
		this._className = className;
		
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
		this._element.parent('form').submit($.proxy(this._stopFormSubmit, this));
		this._element.find('button[data-type="submit"]').click($.proxy(this._submit, this));
		this._init();
	},
	
	_init: function() {
		$('.jsBlock').on('empty', $.proxy(this._empty, this));
	},
	
	/**
	 * Stops the form submit event.
	 * 
	 * @param	{jQuery.Event}	event
	 * @return	{Boolean}
	 */
	_stopFormSubmit: function(event) {
		event.preventDefault();
		return;
	},
	
	/**
	 * Called each time a menu item is removed with empty().remove().
	 * @param	{jQuery.Event}	event
	 */
	_empty: function(event) {
		if ($('#' + this._containerID).find('.jsBlock').length <= 1) {
			$('#' + this._containerID).find('button[data-type="submit"]').prop('disabled', true).addClass('disabled');
		}
	},
	
	/**
	 * Saves blocks.
	 */
	_submit: function() {
		var $data = {};
		// read form data
		var blockTypeID = $('#selectBlocktype').val();
				
		if (blockTypeID == '0') {
			this._notification = new WCF.System.Notification(WCF.Language.get('wcf.global.form.error'), 'error');
			this._element.find('dl:first').addClass('formError');
			var $html = '<small id="selectBlocktypeError" class="innerError">' + WCF.Language.get('wcf.acp.ultimate.template.selectBlocktype.error.notSelected') + '</small>';
			$('#selectBlocktypeError').empty().remove();
			this._element.find('dl:first > dd').append($html);
			this._notification.show();
			return;
		}
		this._notification = null;
		$('#selectBlocktypeError').empty().remove();
		
		// select blockType specific information
		var $formDataParameters = $.extend(true, {
			data: {
				blockTypeID: blockTypeID
			}
		}, { });
		
		$formData = $.extend(true, {
			actionName: 'getFormDataAJAX',
			className: this._className,
			parameters: $formDataParameters
		}, $data);
		
		var $proxy = new WCF.Action.Proxy({
			success: $.proxy(this._successFormData, this)
		});
		$proxy.setOption('data', $formData);
		$proxy.sendRequest();
	},
	
	/**
	 * Saves the additional block options.
	 */
	_submitFormData: function() {
		// read form data
		var blockTypeID = $('#selectBlocktype').val();		
		var width = $('#width').val();
		var height = $('#height').val();
		var left = $('#left').val();
		var top = $('#topDistance').val();
		var $parameters = $.extend(true, {
			data: {
				blockTypeID: blockTypeID,
				additionalData: {
					width: width,
					height: height,
					left: left,
					top: top
				},
				templateID: $('input[name="id"]').val()
			}
		}, { });
		
		for (var i = 0; i < this._optionList.length; i++) {
			var $item = this._optionList[i];
			var optionName = $item.replace(/-\d/, '');
			var $optionElement = $('#' + $item).val();
			$parameters['data']['additionalData'][optionName] = $optionElement;
		}
		
		// reset form
		$('#selectBlocktype').val('0');
		$('#width').val('1');
		$('#height').val('0');
		$('#left').val('1');
		$('#topDistance').val('0');
		
		// build proxy data
		$data = $.extend(true, {
			actionName: 'createAJAX',
			className: this._className,
			parameters: $parameters			
		}, $data);
		this._proxy.setOption('data', $data);
		
		// send proxy request
		this._proxy.sendRequest();
	},
	
	/**
	 * Shows dialog form.
	 * 
	 * @param	{Object}	data
	 * @param	{String}	textStatus
	 * @param	{jQuery}	jqXHR
	 */
	_successFormData: function(data, textStatus, jqXHR) {
		try {
			var $data = data['returnValues'];
			this._optionList = $data[0];
			$('#blockForm').html($data[1]);
			$('#blockForm').find('form').submit($.proxy(this._stopFormSubmit, this));
			$('#blockSubmitButton').click($.proxy(this._submitFormData, this));
			WCF.TabMenu.reload();
			this._dialog = WCF.showDialog('blockForm', {
				title: WCF.Language.get('wcf.acp.ultimate.template.dialog.additionalOptions')
			});
		}
		catch(e) {
			var $showError = true;
			if ($showError !== false) {
				$('<div class="ajaxDebugMessage"><p>' + e.message + '</p></div>').wcfDialog({ title: WCF.Language.get('wcf.global.error.title') });
			}
		}
	},
	
	/**
	 * Shows notification upon success.
	 * 
	 * @param	{Object}	data
	 * @param	{String}	textStatus
	 * @param	{jQuery}	jqXHR
	 */
	_success: function(data, textStatus, jqXHR) {
		if (this._notification === null) {
			this._notification = new WCF.System.Notification(WCF.Language.get('wcf.global.form.edit.success'));
		}
		try {
			var $data = data['returnValues'];
			var $newHtml = '<li class="jsBlock" data-object-name="' + $data['blockTypeName'] 
				+ '" data-object-id="' + $data['blockID'] + '">';
			$newHtml += '<span><span class="buttons">';
			if (ULTIMATE.Permission.get('admin.content.ultimate.canDeleteBlock')) {
				$newHtml += '<span title="' 
					+ WCF.Language.get('wcf.global.button.delete') 
					+ '" class="icon icon16 icon-remove jsDeleteButton jsTooltip" data-object-id="' +
					+ $data['blockID'] + '" data-confirm-message="' + WCF.Language.get('wcf.acp.ultimate.block.delete.sure') + '"></span>';
			}
			else {
				$newHtml += '<span title="' 
					+ WCF.Language.get('wcf.global.button.delete') + '" class="icon icon16 icon-remove disabled"></span>';
			}
			$newHtml += '</span><span class="title">' + $data['blockTypeName'] 
				+ ' #' + $data['blockID']
				+ '</span></span></li>';
			
			$('#' + this._containerID).find('> ol').append($newHtml);
			if ($('#' + this._containerID).find('button[data-type="submit"]').prop('disabled')) {
				$('#' + this._containerID).find('button[data-type="submit"]').prop('disabled', false).removeClass('disabled');
			}
			if (ULTIMATE.Permission.get('admin.content.ultimate.canDeleteBlock')) {
				new WCF.Action.Delete('ultimate\\data\\block\\BlockAction', $('.jsBlock'));
			}
			this._init();
			this._notification.show();
		}
		catch(e) {
			// call child method if applicable
			var $showError = true;
			if ($showError !== false) {
				$('<div class="ajaxDebugMessage"><p>' + e.message + '</p></div>').wcfDialog({ title: WCF.Language.get('wcf.global.error.title') });
			}
		}
	}
};

/**
 * Global permission storage.
 * 
 * @see	WCF.Dictionary
 * @since	version 1.0.0
 */
ULTIMATE.Permission = {
	/**
	 * Contains the permissions.
	 * @type	WCF.Dictionary
	 */
	_variables: new WCF.Dictionary(),
	
	/**
	 * @param	{String}	key
	 * @param	{Boolean}	value
	 * @see		WCF.Dictionary.add()
	 */
	add: function(key, value) {
		this._variables.add(key, value);
	},
	
	/**
	 * @see	WCF.Dictionary.addObject()
	 */
	addObject: function(object) {
		this._variables.addObject(object);
	},
	
	/**
	 * Retrieves a variable.
	 * 
	 * @param	{String}	key
	 * @return	{Boolean}
	 */
	get: function(key) {
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
 * @namespace
 */
ULTIMATE.Menu = {};

/**
 * Namespace for ULTIMATE.Menu.Item
 * @namespace
 */
ULTIMATE.Menu.Item = {};

/**
 * Creates a new MenuItemTransfer.
 * 
 * @param	{String}	elementID
 * @param	{String}	menuItemListID
 * @param	{String}	className
 * @param	{Integer}	offset
 * @param	{String}	type
 * @class	Adds menu items to a menu item list.
 * @since	version 1.0.0
 */
ULTIMATE.Menu.Item.Transfer = function(elementID, menuItemListID, className, offset, type) { this.init(elementID, menuItemListID, className, offset, type); };
ULTIMATE.Menu.Item.Transfer.prototype = {
	
	/**
	 * Contains the element from which the items should be transferred.
	 * @type	jQuery
	 */
	_element: null,
	
	/**
	 * menu item list id
	 * @type	String
	 */
	_menuItemListID: '',
	
	/**
	 * action class name
	 * @type	String
	 */
	_className: '',
	
	/**
	 * notification object
	 * @type	WCF.System.Notification
	 */
	_notification: null,
	
	/**
	 * show order offset
	 * @type	Integer
	 */
	_offset: 0,
	
	/**
	 * proxy object
	 * @type	WCF.Action.Proxy
	 */
	_proxy: null,
	
	/**
	 * object structure
	 * @type	Object
	 */
	_structure: { },
	
	/**
	 * type of IDs (page, category, content, custom)
	 * @type	String
	 */
	_type: '',
	
	/**
	 * true if the submit is done
	 * @type	Boolean
	 */
	_submitDone: false,
	
	/**
	 * true if the request should be sent
	 * @type	Boolean
	 */
	_sendRequest: false,
	
	
	/**
	 * Initializes a menu item transfer.
	 * 
	 * @param	{String}	elementID
	 * @param	{String}	menuItemListID
	 * @param	{String}	className
	 * @param	{Integer}	offset
	 * @param	{String}	type
	 */
	init: function(elementID, menuItemListID, className, offset, type) {
		this._element = $('#' + $.wcfEscapeID(elementID));
		this._menuItemListID = $.wcfEscapeID(menuItemListID);
		this._className = className;
		this._offset = (offset) ? offset : 0;
		this._type = type;
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
		this._structure = { };
		this._element.parent('form').submit($.proxy(this._stopFormSubmit, this));
		if (this._type != 'custom') {
			this._element.find('input:checkbox').change($.proxy(this._change, this));
			this._element.find('button[data-type="submit"]').click($.proxy(this._submit, this));
		}
		this._init();
	},
	
	/**
	 * Initializes the event handler.
	 */
	_init: function() {
		$('.jsMenuItem').on('remove', $.proxy(this._remove, this));
	},
	
	/**
	 * Called each time a menu item is removed with remove().
	 * @param	{jQuery.Event}	event
	 */
	_remove: function(event) {
		var $target = $(event.target);
		var $elementName = $target.data('objectName');
				
		this._element.find('input:disabled').each($.proxy(function(index, item) {
			var $item = $(item);
			var $itemName = $item.data('name');
			if ($elementName == $itemName) {
				$item.prop('disabled', false).removeClass('disabled');
			}
		}, this));
		
		if ($('#' + this._menuItemListID).find('.jsMenuItem').length <= 1) {
			$('#' + this._menuItemListID).find('button[data-type="submit"]').prop('disabled', true).addClass('disabled');
		}
	},
	
	/**
	 * Changes the state of the buttons.
	 */
	_change: function() {
		var checkedCheckboxes = this._element.find('input:checked').length;
		if (checkedCheckboxes) {
			this._element.find('button[data-type="submit"]').removeClass('disabled').prop('disabled', false);
		}
		else {
			this._element.find('button[data-type="submit"]').addClass('disabled').prop('disabled', true);
		}
	},
	
	/**
	 * Stops the form submit event.
	 * 
	 * @param	{jQuery.Event}	event
	 * @return	{Boolean}
	 */
	_stopFormSubmit: function(event) {
		event.preventDefault();
		if (this._type != 'custom') return;
		if (this._element.find('input[name="title"]').length == 0) {
			this._submit();
		} 
		else if (this._element.find('input[name="title"]').length == 1) {
			this._submit();
		}
	},

	/**
	 * Saves object structure.
	 */
	_submit: function() {
		this._structure = { };
		if (this._type == 'custom') {
			var link = $('#link').val();
			var linkTitleFound = this._element.find('input[name="title"]');
			var linkTitle = '';
			var $data = {};
			// only add title to post values if linkTitle is not i18n
			if (linkTitleFound.length == 1) {
				linkTitle = $('#title').val();
				$data = $.extend(true, {
					title: linkTitle
				}, $data);
			} else if (linkTitleFound.length == 0) {
				// if it is i18n add it to post values accordingly
				var linkTitle_i18n = {};
				var $parent = this._element.parent();
				$parent.find('input[name^="title_i18n"]').each($.proxy(function(index, listItem) {
					var $listItem = $(listItem);
					var $languageID = $listItem.attr('name').substring(11);
					$languageID = $languageID.substr(0, $languageID.length - 1);
					linkTitle_i18n[$languageID] = $listItem.val();
				}, this));
				$data = $.extend(true, {
					title_i18n: linkTitle_i18n
				}, $data);
			}
			this._structure['link'] = link;
			this._structure['linkTitle'] = linkTitle;
			// resets the form
			$('#link').val('http://');
			$('#title').val('');
			// send request
			var $parameters = $.extend(true, {
				data: {
					offset: this._offset,
					structure: this._structure,
					type: this._type,
					menuID: $('input[name="id"]').val()
				}
			}, { });
			
			$data = $.extend(true, {
				actionName: 'createAJAX',
				className: this._className,
				parameters: $parameters			
			}, $data);
			
			this._proxy.setOption('data', $data);
			this._sendRequest = true;
		}
		else {
			this._element.find('dl > dd > ul > li > label > input[type="checkbox"]').each($.proxy(function(index, listItem) {
				var $listItem = $(listItem);
				var $parentID = $listItem.val();
				var $parent = $listItem.parent().parent();
				if ($parentID !== undefined) {
					$checkedParent = $listItem.prop('checked');
					this._getNestedElements($parent, $parentID);
					if (!this._structure[0]) {
						this._structure[0] = [ ];
					}
					if ($checkedParent) {
						this._structure[0].push($parentID);
						this._sendRequest = true;
						$listItem.prop('checked', false).prop('disabled', true).addClass('disabled');
					}
				}
			}, this));
			// send request
			var $parameters = $.extend(true, {
				data: {
					offset: this._offset,
					structure: this._structure,
					type: this._type,
					menuID: $('input[name="id"]').val()
				}
			}, { });
			
			this._proxy.setOption('data', {
				actionName: 'createAJAX',
				className: this._className,
				parameters: $parameters
			});
		}
		if (this._element.find('input:not(:disabled)').length == 0) {
			this._change();
		}
		if (this._sendRequest) {
			this._proxy.sendRequest();
			this._submitDone = true;
		} else {
			this._notification = new WCF.System.Notification(WCF.Language.get('wcf.acp.ultimate.menu.noItemsSelected'));
			this._notification.show();
		}
	},
	
	/**
	 * Builds all nested elements.
	 * 
	 * @param	{jQuery}	$parent
	 * @param	{Integer}	$parentID
	 */
	_getNestedElements: function($parent, $parentID) {
		$parent.find('ul > li > label > input[type="checkbox"]').each($.proxy(function(index, listItem) {
			var $objectID = $(listItem).val();
			var $checked = $(listItem).prop('checked');
			var $__parent = $(listItem).parent().parent();
			
			this._getNestedElements($__parent, $objectID);
			
			if (!this._structure[$parentID]) {
				this._structure[$parentID] = [ ];
			}
			if ($checked) {
				this._structure[$parentID].push($objectID);
				this._sendRequest = true;
				$(listItem).prop('checked', false).prop('disabled', true).addClass('disabled');
			}
			
		}, this));
		
	},
	
	/**
	 * Shows notification upon success.
	 * 
	 * @param	{Object}	data
	 * @param	{String}	textStatus
	 * @param	{jQuery}	jqXHR
	 */
	_success: function(data, textStatus, jqXHR) {
		if (this._notification === null) {
			this._notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success.edit'));
		}
		try {
			var data = data['returnValues'];
			for (var $menuItemID in data) {
				var $newItemHtml = '<li id="' + WCF.getRandomID() + '" class="sortableNode jsMenuItem" data-object-id="' + $menuItemID + '"  data-object-name="' + data[$menuItemID]['menuItemNameRaw'] + '">';
				$newItemHtml += '<span class="sortableNodeLabel"><span class="buttons">';
				if (ULTIMATE.Permission.get('admin.content.ultimate.canDeleteMenuItem')) {
					$newItemHtml += '<span title="' + WCF.Language.get('wcf.global.button.delete') + '" class="icon icon16 icon-remove jsDeleteButton jsTooltip" data-object-id="' + $menuItemID + '" data-confirm-message="' + WCF.Language.get('wcf.acp.ultimate.menu.item.delete.sure') + '"></span>';
				}
				else {
					$newItemHtml += '<span title="' + WCF.Language.get('wcf.global.button.delete') + '" class="icon icon16 icon-remove disabled"></span>';
				}
				if (ULTIMATE.Permission.get('admin.content.ultimate.canEditMenuItem')) {
					$newItemHtml += '&nbsp;<span title="' + ((data[$menuItemID]['isDisabled']) ? WCF.Language.get('wcf.global.button.enable') : WCF.Language.get('wcf.global.button.disable')) + '" class="icon icon16 icon-' + ((data[$menuItemID]['isDisabled']) ? 'off' : 'circle-blank') + ' jsToggleButton jsTooltip" data-object-id="' + $menuItemID + '"></span>';
				}
				else {
					$newItemHtml += '&nbsp;<span title="' + (data[$menuItemID]['isDisabled']) ? WCF.Language.get('wcf.global.button.enable') : WCF.Language.get('wcf.global.button.disable') + '" class="icon icon16 icon-' + (data[$menuItemID]['isDisabled']) ? 'off' : 'circle-blank' + ' disabled"></span>';
				}
				$newItemHtml += '</span>&nbsp;<span class="title">';		
				$newItemHtml += data[$menuItemID]['menuItemName'] + '</span></span><ol class="sortableList" data-object-id="' + $menuItemID + '"></ol></li>';
				
				$('#' + this._menuItemListID + '> .sortableList').append($newItemHtml);
				if ($('#' + this._menuItemListID).find('button[data-type="submit"]').prop('disabled')) {
					$('#' + this._menuItemListID).find('button[data-type="submit"]').prop('disabled', false).removeClass('disabled');
				}
			}
			if (ULTIMATE.Permission.get('admin.content.ultimate.canDeleteMenuItem')) {
				new ULTIMATE.NestedSortable.Delete('ultimate\\data\\menu\\item\\MenuItemAction', $('.jsMenuItem'));
			}
			if (ULTIMATE.Permission.get('admin.content.ultimate.canEditMenuItem')) {
				new WCF.Action.Toggle('ultimate\\data\\menu\\item\\MenuItemAction', $('.jsMenuItem'), '> .buttons > .jsToggleButton');
			}
			this._init();
			this._notification.show();
		}
		// something happened
		catch (e) {
			// call child method if applicable
			var $showError = true;
			if ($showError !== false) {
				$('<div class="ajaxDebugMessage"><p>' + e.message + '</p></div>').wcfDialog({ title: WCF.Language.get('wcf.global.error.title') });
			}
		}
		
	}
};

/**
 * Namespace for ULTIMATE.Widget
 * @namespace
 */
ULTIMATE.Widget = {};

/**
 * Creates a new WidgetEdit.
 * 
 * @param	{jQuery}	containerList
 * @class	Manages the edit process of a widget.
 * @since	version 1.0.0
 */
ULTIMATE.Widget.Edit = function(containerList) { this.init(containerList); };
ULTIMATE.Widget.Edit.prototype = {
	/**
	 * Contains a list of all widgets.
	 * @type	jQuery
	 */
	_containerList: null,
	
	/**
	 * Contains the proxy.
	 * @type	WCF.Action.Proxy
	 */
	_proxy: null,
	
	/**
	 * Contains the dialog
	 * @type	jQuery
	 */
	_dialog: null,
	
	/**
	 * Contains the notification.
	 * @type	WCF.System.Notification
	 */
	_notification: null,
	
	/**
	 * Initializes a widget edit process.
	 * 
	 * @param	{jQuery}	containerList
	 */
	init: function(containerList) {
		this._className = className;
		this._containerList = containerList;
		this._badgeList = badgeList;
		this._proxy = new WCF.Action.Proxy({
			sucess: $.proxy(this._success, this)
		});
		this.initButtons();
	},
	
	/**
	 * Initializes the button event listeners.
	 */
	initButtons: function() {
		this._containerList.each($.proxy(function(index, container) {
			$(container).find('.jsEditButton').bind('click', $.proxy(this._click, this));
		}, this));
	},
	
	/**
	 * Called each time an edit button is clicked.
	 * 
	 * @param	{jQuery.Event}	event
	 */
	_click: function(event) {
		var $target = $(event.target);
		this._initDialog($target);
		
		// initialize dialog handler
		this._dialog.find('button[data-type="submit"]').click($.proxy(this._submit, this));
		this._dialog.find('button[data-type="cancel"]').click($.proxy(function(_event) {
			// close dialog
			this._dialog.ultimateDialog('close');
		}, this));
	},
	
	/**
	 * Initializes the dialog.
	 * 
	 * @param	{jQuery}	$target
	 */
	_initDialog: function($target) {
		this._dialog = null;
		var $widgetTypeID = $('#widgetTypeIDs option:selected').val();
		var options = {
			// dialog
			title: WCF.Language.get('wcf.acp.ultimate.widget.edit'),
			
			// AJAX support
			ajax: true,
			data: {
				parameters: {
					widgetTypeID: $widgetTypeID,
					widgetID: $target.data('objectID')
				},
				actionName: 'loadWidgetOptions'
			},
			type: 'POST',
			url: 'index.php/Widget/?t=' + SECURITY_TOKEN + SID_ARG_2ND,
			
			// event callbacks
			onClose: null,
			onShow: null
		};
		this._dialog = WCF.showAJAXDialog('widgetEditor', true, options);
	},
	
	/**
	 * Called on submitting the dialog form.
	 * 
	 * @param	{jQuery.Event}	event
	 */
	_submit: function(event) {
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
			}
			else {
				$settings[$item.attr('name')] = $value;
			}
		}, this));
		
		var $data = {
			parameters: {
				widgetID: $widgetID,
				settings: $settings
			},
			actionName: 'saveWidgetOptions'
		};
		// send request
		var $proxy = new WCF.Action.Proxy({
			success: $.proxy(this._successEdit, this),
			url: 'index.php/Widget/?t=' + SECURITY_TOKEN + SIG_ARG_2ND,
			data: $data
		});
		$proxy.sendRequest();
	},
	
	/**
	 * Called after successful save operation.
	 * 
	 * @param	{Object}	data
	 * @param	{String}	textStatus
	 * @param	{jQuery}	jqXHR
	 */
	_successEdit: function(data, textStatus, jqXHR) {
		if (this._notification === null) {
			this._notification = new WCF.System.Notification(WCF.Language.get('wcf.global.form.edit.success'));
		}
		this._notification.show();
	}
};

/**
 * Creates a new WidgetTransfer.
 * 
 * @param	{String}	elementID
 * @param	{String}	widgetListID
 * @param	{String}	className
 * @param	{Integer}	offset
 * @class	Adds menu items to a widget list.
 * @since	version 1.0.0
 */
ULTIMATE.Widget.Transfer = function(elementID, widgetListID, className, offset) { this.init(elementID, widgetListID, className, offset); };
ULTIMATE.Widget.Transfer.prototype = {
	/**
	 * Contains the element from which the widgets should be transferred.
	 * @type	jQuery
	 */
	_element: null,
	
	/**
	 * widget list id
	 * @type	String
	 */
	_widgetListID: '',
	
	/**
	 * action class name
	 * @type	String
	 */
	_className: '',
	
	/**
	 * notification object
	 * @type	WCF.System.Notification
	 */
	_notification: null,
	
	/**
	 * show order offset
	 * @type	Integer
	 */
	_offset: 0,
	
	/**
	 * proxy object
	 * @type	WCF.Action.Proxy
	 */
	_proxy: null,
	
	/**
	 * object structure
	 * @type	Object
	 */
	_structure: { },
	
	/**
	 * true if the submit is done
	 * @type	Boolean
	 */
	_submitDone: false,
	
	/**
	 * true if the request should be sent
	 * @type	Boolean
	 */
	_sendRequest: false,
	
	
	/**
	 * Initializes a widget transfer.
	 * 
	 * @param	{String}	elementID
	 * @param	{String}	widgetListID
	 * @param	{String}	className
	 * @param	{Integer}	offset
	 */
	init: function(elementID, widgetListID, className, offset) {
		this._element = $('#' + $.wcfEscapeID(elementID));
		this._widgetListID = $.wcfEscapeID(widgetListID);
		this._className = className;
		this._offset = (offset) ? offset : 0;
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
		this._structure = { };
		this._element.parent('form').submit($.proxy(this._stopFormSubmit, this));
		this._element.find('select').change($.proxy(this._change, this));
		this._element.find('button[data-type="submit"]').click($.proxy(this._submit, this));
		
	},
	
	/**
	 * Changes the state of the buttons.
	 */
	_change: function() {
		var selected = this._element.find('select option:selected');
		if (selected.val()) {
			this._element.find('button[data-type="submit"]').removeClass('disabled').prop('disabled', false);
		}
		else {
			this._element.find('button[data-type="submit"]').addClass('disabled').prop('disabled', true);
		}
	},
	
	/**
	 * Stops the form submit event.
	 * 
	 * @param	{jQuery.Event}	event
	 * @return	{Boolean}
	 */
	_stopFormSubmit: function(event) {
		event.preventDefault();
		return;
	},

	/**
	 * Saves object structure.
	 */
	_submit: function() {
		this._structure = { };
		var $selected = this._element.find('dl > dd > select > option:selected');
		var $objectID = $selected.val();
		if (!this._structure[0]) {
			this._structure[0] = [ ];
		}
		if ($objectID) {
			this._structure[0].push($objectID);
			this._sendRequest = true;
		}
		// send request
		var $parameters = $.extend(true, {
			data: {
				offset: this._offset,
				structure: this._structure,
				widgetAreaID: $('input[name="id"]').val()
			}
		}, { });
		
		this._proxy.setOption('data', {
			actionName: 'createAJAX',
			className: this._className,
			parameters: $parameters
		});
		
		this._change();
		
		if (this._sendRequest) {
			this._proxy.sendRequest();
			this._submitDone = true;
		} else {
			this._notification = new WCF.System.Notification(WCF.Language.get('wcf.acp.ultimate.widgetArea.widgetTypes.noItemsSelected'));
			this._notification.show();
		}
	},
	
	/**
	 * Shows notification upon success.
	 * 
	 * @param	{Object}	data
	 * @param	{String}	textStatus
	 * @param	{jQuery}	jqXHR
	 */
	_success: function(data, textStatus, jqXHR) {
		if (this._notification === null) {
			this._notification = new WCF.System.Notification(WCF.Language.get('wcf.global.form.edit.success'));
		}
		try {
			var $data = data['returnValues'];
			var $widgetID = $data['widgetID'];
			var $newItemHtml = '<li id="' + WCF.getRandomID() + '" class="sortableNode jsMenuItem" data-object-id="' + $widgetID + '"  data-object-name="' + $data[$widgetID]['widgetNameRaw'] + '">';
			$newItemHtml += '<span class="sortableNodeLabel"><span class="buttons">';
			if (ULTIMATE.Permission.get('admin.content.ultimate.canDeleteWidget')) {
				$newItemHtml += '<span title="' + WCF.Language.get('wcf.global.button.delete') + '" class="icon icon16 icon-remove jsDeleteButton jsTooltip" data-object-id="' + $widgetID + '" data-confirm-message="' + WCF.Language.get('wcf.acp.ultimate.widget.delete.sure') + '"></span>';
			}
			else {
				$newItemHtml += '<span title="' + WCF.Language.get('wcf.global.button.delete') + '" class="icon icon16 icon-remove disabled"></span>';
			}
			if (ULTIMATE.Permission.get('admin.content.ultimate.canEditWidget')) {
				$newItemHtml += '&nbsp;<span title="' + (($data[$widgetID]['isDisabled']) ? WCF.Language.get('wcf.global.button.enable') : WCF.Language.get('wcf.global.button.disable')) + '" class="icon icon16 icon-' + (($data[$widgetID]['isDisabled']) ? 'off' : 'circle-blank') + ' jsToggleButton jsTooltip" data-object-id="' + $widgetID + '"></span>';
			}
			else {
				$newItemHtml += '&nbsp;<span title="' + ($data[$widgetID]['isDisabled']) ? WCF.Language.get('wcf.global.button.enable') : WCF.Language.get('wcf.global.button.disable') + '" class="icon icon16 icon-' + (($data[$widgetID]['isDisabled']) ? 'off' : 'circle-blank') + ' disabled"></span>';
			}
			if (ULTIMATE.Permission.get('admin.content.ultimate.canEditWidget')) {
				$newItemHtml += '&nbsp;<span title="' + WCF.Language.get('wcf.global.button.edit') + '" class="icon icon16 icon-pencil jsToggleButton jsTooltip" data-object-id="' + $widgetID + '"></span>';
			}
			else {
				$newItemHtml += '&nbsp;<span title="' + WCF.Language.get('wcf.global.button.edit') + '" class="icon icon16 icon-pencil disabled"></span>';
			}
			$newItemHtml += '</span>&nbsp;<span class="title">';		
			$newItemHtml += $data[$widgetID]['widgetName'] + '</span></span><ol class="sortableList" data-object-id="' + $widgetID + '"></ol></li>';
			
			$('#' + this._widgetListID + '> .sortableList').append($newItemHtml);
			if ($('#' + this._widgetListID).find('button[data-type="submit"]').prop('disabled')) {
				$('#' + this._widgetListID).find('button[data-type="submit"]').prop('disabled', false).removeClass('disabled');
			}
			
			if (ULTIMATE.Permission.get('admin.content.ultimate.canDeleteWidget')) {
				new WCF.Action.Delete('ultimate\\data\\widget\\WidgetAction', $('.jsWidget'));
			}
			if (ULTIMATE.Permission.get('admin.content.ultimate.canEditWidget')) {
				new WCF.Action.Toggle('ultimate\\data\\widget\\WidgetAction', $('.jsWidget'));
				new ULTIMATE.Widget.Edit($('.jsWidget'));
			}
			this._notification.show();
		}
		// something happened
		catch (e) {
			// call child method if applicable
			var $showError = true;
			if ($showError !== false) {
				$('<div class="ajaxDebugMessage"><p>' + e.message + '</p></div>').wcfDialog({ title: WCF.Language.get('wcf.global.error.title') });
			}
		}
		
	}
};

/**
 * Namespace for ULTIMATE.NestedSortable
 * @namespace
 */
ULTIMATE.NestedSortable = {};

/**
 * @see	WCF.Action.Delete
 */
ULTIMATE.NestedSortable.Delete = WCF.Action.Delete.extend({
	/**
	 * @see	WCF.Action.Delete.triggerEffect()
	 */
	triggerEffect: function(objectIDs) {
		for (var $index in this._containers) {
			var $container = $('#' + this._containers[$index]);
			if (WCF.inArray($container.find('.jsDeleteButton').data('objectID'), objectIDs)) {
				// move child categories up
				if ($container.has('ol').has('li')) {
					var $list = $container.find('> ol');
					$container.before($list.contents());
					$list.contents().detach();
					$container.remove();
				}
				else {
					$container.wcfBlindOut('up', function() { $container.remove(); });
				}
			}
		}
	}
});


