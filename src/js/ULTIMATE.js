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
	 * the initial timestamp
	 * @type	Integer
	 */
	_initialValueDateTime: 0,
	
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
			this._initialDateTime = WCF.Date.Util.gmdate($dateObj);
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
				this._button.attr('disabled', 'disabled');
				this._button.addClass('ultimateHidden');
			} else if ($currentValue == 0 || $currentValue == 1) {
				this._button.removeClass('ultimateHidden');
				this._button.removeAttr('disabled');
				this._button.val($languageOutput);
			}
		} else if (this._action == 'publish') {
			var $dateObj = this._checkElement.datetimepicker( 'getDate' );
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
	get: function(key, parameters) {
		// initialize parameters with an empty object
		if (typeof parameters === 'undefined') var parameters = {};
		
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
	 * @tspe	jQuery
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
		$('.jsMenuItem').on('empty', $.proxy(this._empty, this));
	},
	
	/**
	 * Called each time a menu item is removed with empty().remove().
	 * @param	{jQuery.event}	event
	 */
	_empty: function(event) {
		var $target = $(event.target);
		var $parent = $target.parent();
		var $index = $parent.index($target);
		var $before = $parent.get($index - 1);
		var usePrepend = false;
		if ($before == undefined) {
			usePrepend = true;
		}
		var $elementName = $target.data('objectName');
		this._element.find('input:disabled').each($.proxy(function(index, item) {
			var $item = $(item);
			var $itemName = $item.data('name');
			if ($elementName == $itemName) {
				$item.prop('disabled', false).removeClass('disabled');
			}
		}, this));
		$target.children('.sortableList').each($.proxy(function(index, item) {
			$list = $(item);
			$list.children('.jsMenuItem').each($.proxy(function(index, listItem) {
				$listItem = $(listItem);
				$listItem.detach();
				if (usePrepend) $listItem.prependTo($parent);
				else $listItem.insertAfter($before);
			}, this));
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
	 * @param	{jQuery.event}	event
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
			this._notification = new WCF.System.Notification(WCF.Language.get('wcf.global.form.edit.success'));
		}
		try {
			var data = data['returnValues'];
			for (var $menuItemID in data) {
				var $newItemHtml = '<li id="' + WCF.getRandomID() + '" class="sortableNode jsMenuItem" data-object-id="' + $menuItemID + '"  data-object-name="' + data[$menuItemID]['menuItemNameRaw'] + '">';
				$newItemHtml += '<span class="sortableNodeLabel"><span class="buttons">';
				if (ULTIMATE.Permission.get('admin.content.ultimate.canDeleteMenuItem')) {
					$newItemHtml += '<img src="' + WCF.Icon.get('wcf.icon.delete') + '" alt="" title="' + WCF.Language.get('wcf.global.button.delete') + '" class="icon16 jsDeleteButton jsTooltip" data-object-id="' + $menuItemID + '" data-confirm-message="' + WCF.Language.get('wcf.acp.ultimate.menu.item.delete.sure') + '" />';
				}
				else {
					$newItemHtml += '<img src="' + WCF.Icon.get('wcf.icon.delete') + '" alt="" title="' + WCF.Language.get('wcf.global.button.delete') + '" class="icon16 disabled" />';
				}
				if (ULTIMATE.Permission.get('admin.content.ultimate.canEditMenuItem')) {
					$newItemHtml += '&nbsp;<img src="' + ((data[$menuItemID]['isDisabled']) ? WCF.Icon.get('wcf.icon.disabled') : WCF.Icon.get('wcf.icon.enabled')) + '" alt="" title="' + ((data[$menuItemID]['isDisabled']) ? WCF.Language.get('wcf.global.button.enable') : WCF.Language.get('wcf.global.button.disable')) + '" class="icon16 jsToggleButton jsTooltip" data-object-id="' + $menuItemID + '" />';
				}
				else {
					$newItemHtml += '&nbsp;<img src="' + (data[$menuItemID]['isDisabled']) ? WCF.Icon.get('wcf.icon.disabled') : WCF.Icon.get('wcf.icon.enabled') + '" alt="" title="' + (data[$menuItemID]['isDisabled']) ? WCF.Language.get('wcf.global.button.enable') : WCF.Language.get('wcf.global.button.disable') + '" class="icon16 disabled" />';
				}
				$newItemHtml += '</span>&nbsp;<span class="title">';		
				$newItemHtml += data[$menuItemID]['menuItemName'] + '</span></span><ol class="sortableList" data-object-id="' + $menuItemID + '"></ol></li>';
				
				$('#' + this._menuItemListID + '> .sortableList').append($newItemHtml);
				if ($('#' + this._menuItemListID).find('button[data-type="submit"]').prop('disabled')) {
					$('#' + this._menuItemListID).find('button[data-type="submit"]').prop('disabled', false).removeClass('disabled');
				}
			}
			if (ULTIMATE.Permission.get('admin.content.ultimate.canDeleteMenuItem')) {
				new WCF.Action.Delete('ultimate\\data\\menu\\item\\MenuItemAction', $('.jsMenuItem'));
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
 * Creates a new VisualEditor.
 * 
 * @param	{String}	elementID
 * @class	Represents the VisualEditor.
 */
ULTIMATE.VisualEditor = function(elementID) { this.init(elementID); };
ULTIMATE.VisualEditor.prototype = {
	/**
	 * Contains the element.
	 * @type jQuery
	 */
	_element: null,
	
	/**
	 * Initializes the VisualEditor.
	 * 
	 * @param	{String}	elementID
	 */
	init: function(elementID) {
		this._element = $('#' + $.wcfEscapeID(elementID));
		this._element.selectArea({
			select: function(top, left, bottom, right, width, height) {
				
			}
		});
	}
};
