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
 * Useful block functions.
 * 
 * @since	version 1.0.0
 */
ULTIMATE.Block = {
	/**
	 * Returns the real block element.
	 * 
	 * @param	{String}	element
	 * @returns	{jQuery}
	 */
	getBlock: function(element) {
		// if invalid selector, do not go any further
		if ( $(element).length === 0 ) {
			return $;
		}
		var block = null;
		// find the actual block node
		if ( $(element).hasClass('block') ) {
			block = $(element);
		} else if ( $(element).parents('.block').length === 1 ) {
			block = $(element).parents('.block');
		} else {
			block = false;
		}
		
		return block;
	},
	
	/**
	 * Returns the blockID.
	 * 
	 * @param	{String}	element
	 * @returns	{Integer}
	 */
	getBlockID: function(element) {
		var block = ULTIMATE.Block.getBlock(element);
		
		if ( !block ) {
			return false;
		}
		
		// pull out ID
		return block.data('id');
	},
	
	/**
	 * Returns the block type.
	 * 
	 * @param	{String}	element
	 * @returns	{String}
	 */
	getBlockType: function(element) {
		var block = ULTIMATE.Block.getBlock(element);
		
		if ( !block ) {
			return false;
		}
		
		var classes = block.attr('class').split(' ');
	    var blockType = '';
		for (i = 0; i <= classes.length - 1; i++){
			if (classes[i].indexOf('block-type-') !== -1) {
				blockType = classes[i].replace('block-type-', '');
			}
		}	
		
		return blockType;	
	},
	
	/**
	 * Returns nicer block type.
	 * 
	 * @param	{String}	type
	 * @returns	{String}
	 */
	getBlockTypeNice: function(type) {
		if ( typeof type != 'string' ) {
			return false;
		}
		return type.replace('-', ' ').capitalize();
	},
	
	/**
	 * Returns the block type object fitting to the given identifier.
	 * 
	 * @param	{String}	blockType
	 * @returns	{Object}
	 */
	getBlockTypeObject: function(blockType) {
		var blockTypes = ULTIMATE.VisualEditor.getVisualEditor().allBlockTypes;
		
		if ( typeof blockTypes[blockType] === 'undefined' )
			return {'fixed-height': false};
		
		return blockTypes[blockType];
	},
	
	/**
	 * Returns the width of the given block in columns.
	 * 
	 * @param	{String}	element
	 * @returns	{Integer}
	 */
	getBlockGridWidth: function(element) {
		var block = ULTIMATE.Block.getBlock(element);
		
		if ( !block ) {
			return false;
		}
			    		
		return block.data('width');
	},
	
	/**
	 * Returns the left offset of the given block in columns.
	 * 
	 * @param	{String}	element
	 * @returns	{Integer}
	 */
	getBlockGridLeft: function(element) {
		var block = ULTIMATE.Block.getBlock(element);
		
		if ( !block ) {
			return false;
		}
		
		return block.data('gridLeft');
	},
	
	/**
	 * Returns the dimensions of the given block.
	 * 
	 * @param	{String}	element
	 * @returns	{Object}	{width, height}
	 */
	getBlockDimensions: function(element) {
		var block = ULTIMATE.Block.getBlock(element);
		
		if ( !block ) {
			return false;
		}
		
		return {
			width: ULTIMATE.Block.getBlockGridWidth(block),
			height: block.data('height')
		};
	},
	
	/**
	 * Returns the block dimensions pixels.
	 * 
	 * @param	{String}	element
	 * @returns	{Object}	{width, height}
	 */
	getBlockDimensionsPixels: function(element) {
		var block = ULTIMATE.Block.getBlock(element);
		
		if ( !block ) {
			return false;
		}
		
		return {
			width: block.width(),
			height: block.height()
		};
	},
	
	/**
	 * Returns the block position.
	 * 
	 * @param	{String}	element
	 * @returns	{Object}	{left, data}
	 */
	getBlockPosition: function(element) {
		var block = ULTIMATE.Block.getBlock(element);
		
		if ( !block ) {
			return false;
		}
		
		return {
			left: ULTIMATE.Block.getBlockGridLeft(block),
			top: block.data('gridTop')
		};
	},
	
	/**
	 * Returns the position of the given block.
	 * 
	 * @param	{String}	element
	 * @returns	{Object}	{left, top}
	 */
	getBlockPositionPixels: function(element) {
		var block = ULTIMATE.Block.getBlock(element);
		
		if ( !block ) {
			return false;
		}
		
		return {
			left: block.position().left,
			top: block.position().top
		};
	},
	
	/**
	 * Updates the hidden position input for the block with the given id.
	 * 
	 * @param	{Integer}	id
	 * @param	{Object}	position
	 * @param	{Integer}	position.left
	 * @param	{Integer}	position.top
	 */
	updateBlockPositionHidden: function(id, position) {
		if ( typeof id === 'string' && id.indexOf('block-') !== -1 ) {
			var id = id.replace('block-', '');
		}
		
		var hiddenInputClass = 'block-' + id + '-position';
		var position = position.left + ',' + position.top;

		// Create input if it doesn't exist—otherwise, update it.
		if ( $('div#hiddenInputs').find('input.' + hiddenInputClass).length === 0 ) {
			$('<input type="hidden" class="' + hiddenInputClass + '" name="blocks[' + id + '][position]" value="' + position + '"  />')
				.appendTo('div#hiddenInputs');
		} else {
			$('div#hiddenInputs').find('input.' + hiddenInputClass).val(position);
		}
	},
	
	/**
	 * Updates the hidden dimensions input for the block with the given id.
	 * 
	 * @param	{Integer}	id
	 * @param	{Object}	dimensions
	 * @param	{Integer}	dimensions.height
	 * @param	{Integer}	dimensions.width
	 */
	updateBlockDimensionsHidden: function(id, dimensions) {
		if ( typeof id === 'string' && id.indexOf('block-') !== -1 ) {
			var id = id.replace('block-', '');
		}
		
		var hiddenInputClass = 'block-' + id + '-dimensions';
		var dimensions = dimensions.width + ',' + dimensions.height;

		// Create input if it doesn't exist—otherwise, update it.
		if ( $('div#hiddenInputs').find('input.' + hiddenInputClass).length === 0 ) {
			
			$('<input type="hidden" class="' + hiddenInputClass + '" name="blocks[' + id + '][dimensions]" value="' + dimensions + '"  />')
				.appendTo('div#hiddenInputs');
			
		} else {
			$('div#hiddenInputs').find('input.' + hiddenInputClass).val(dimensions);
		}
		
	},
	
	/**
	 * Returns an available block id.
	 * 
	 * @returns	{Integer}
	 */
	getAvailableBlockID: function() {
		
		// get the ready block ID
		var readyBlockID = ULTIMATE.VisualEditor.getVisualEditor().availableBlockID;
		
		// Retrieve the block ID that can be used.
		var blockIDBlacklist = [readyBlockID];
		
		ULTIMATE.VisualEditor.getVisualEditor()._i('.block').each(function() {
			blockIDBlacklist.push(ULTIMATE.Block.getBlockID($(this)));
		});
		var $proxy = new WCF.Action.Proxy({
			success: function(data, textStatus, jqXHR) {
				if ( isNaN(data['returnValues']) )
					return;
				ULTIMATE.VisualEditor.getVisualEditor().availableBlockID = response;
			},
			showLoadingOverlay: false,
			data: {
				className: 'ultimate\\data\\block\\BlockAction',
				actionName: 'getAvailableBlockID',
				parameters: {
					data: {
						blockIDBlackList: blockIDBlackList
					}
				}
			}
		})
		$proxy.sendRequest();
		// return the ID stored before
		return readyBlockID;
	},
	
	/**
	 * Returns all unsaved block options.
	 * 
	 * @param	{Integer}	blockID
	 * @returns	{Object}
	 */
	getUnsavedBlockOptionValues: function(blockID) {
		var inputs = $('div#hiddenInputs').find('input[name*="blocks[' + blockID + '][settings]"]');
		var options = {};
					
		// construct the object to be returned
		inputs.each(function() {
			options[$(this).data('option')] = $(this).val();
		});
								
		return Object.keys(options).length > 0 ? options : null;
	}
};

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
	 * @param	{jQuery.Event}	event
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
		this._dialog.find('button[data-type="cancel"]').click($.proxy(function(event) {
			// close dialog
			this._dialog.wcfDialog('close');
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
				$newItemHtml += '<img src="' + WCF.Icon.get('wcf.icon.delete') + '" alt="" title="' + WCF.Language.get('wcf.global.button.delete') + '" class="icon16 jsDeleteButton jsTooltip" data-object-id="' + $widgetID + '" data-confirm-message="' + WCF.Language.get('wcf.acp.ultimate.widget.delete.sure') + '" />';
			}
			else {
				$newItemHtml += '<img src="' + WCF.Icon.get('wcf.icon.delete') + '" alt="" title="' + WCF.Language.get('wcf.global.button.delete') + '" class="icon16 disabled" />';
			}
			if (ULTIMATE.Permission.get('admin.content.ultimate.canEditWidget')) {
				$newItemHtml += '&nbsp;<img src="' + (($data[$widgetID]['isDisabled']) ? WCF.Icon.get('wcf.icon.disabled') : WCF.Icon.get('wcf.icon.enabled')) + '" alt="" title="' + (($data[$widgetID]['isDisabled']) ? WCF.Language.get('wcf.global.button.enable') : WCF.Language.get('wcf.global.button.disable')) + '" class="icon16 jsToggleButton jsTooltip" data-object-id="' + $widgetID + '" />';
			}
			else {
				$newItemHtml += '&nbsp;<img src="' + ($data[$widgetID]['isDisabled']) ? WCF.Icon.get('wcf.icon.disabled') : WCF.Icon.get('wcf.icon.enabled') + '" alt="" title="' + ($data[$widgetID]['isDisabled']) ? WCF.Language.get('wcf.global.button.enable') : WCF.Language.get('wcf.global.button.disable') + '" class="icon16 disabled" />';
			}
			if (ULTIMATE.Permission.get('admin.content.ultimate.canEditWidget')) {
				$newItemHtml += '&nbsp;<img src="' + WCF.Icon.get('wcf.icon.edit') + '" alt="" title="' + WCF.Language.get('wcf.global.button.edit') + '" class="icon16 jsToggleButton jsTooltip" data-object-id="' + $widgetID + '" />';
			}
			else {
				$newItemHtml += '&nbsp;<img src="' + WCF.Icon.get('wcf.icon.edit') + '" alt="" title="' + WCF.Language.get('wcf.global.button.edit') + '" class="icon16 disabled" />';
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
 * Creates a new VisualEditor.
 * 
 * @param	{String}	selectBlockTypeDialogID
 * @param	{Object}	allBlockTypes
 * @class	Represents the VisualEditor.
 * @since	version 1.0.0
 */
ULTIMATE.VisualEditor = function(selectBlockTypeDialogID, allBlockTypes) { this.init(selectBlockTypeDialogID, allBlockTypes); };
ULTIMATE.VisualEditor.prototype = {
	/**
	 * Contains the select block type dialog id.
	 * @type	String
	 */
	_selectBlockTypeDialogID: '',
	
	/**
	 * Contains the current block type dialog.
	 * @type	jQuery
	 */
	_blockTypeDialog: null,
	
	/**
	 * Contains the bottom panel.
	 * @type	ULTIMATE.VisualEditor.BottomPanel
	 */
	_bottomPanel: null,
	
	/**
	 * True if the layoutSelector is hidden.
	 * @type	Boolean
	 */
	_layoutSelectorHidden: false,
	
	/**
	 * Contains the proxy.
	 * @type	WCF.Action.Proxy
	 */
	_proxy: null,
	
	/**
	 * Contains the template class name.
	 * @type	String
	 */
	_templateClassName: 'ultimate\\data\\template\\TemplateAction',
	
	/**
	 * Contains the iframe.
	 * @type	jQuery
	 */
	iframe: null,
	
	/**
	 * Contains all block types.
	 * @type	Object
	 */
	allBlockTypes: {},
	
	/**
	 * Initializes the VisualEditor.
	 * 
	 * @param	{String}	selectBlockTypeDialogID
	 * @param	{Object}	allBlockTypes
	 */
	init: function(selectBlockTypeDialogID, allBlockTypes) {
		this._selectBlockTypeDialogID = $.wcfEscapeID(selectBlockTypeDialogID);
		this.allBlockTypes = allBlockTypes;
		// initialize proxy
		this._proxy = new WCF.Action.Proxy({
			showLoadingOverlay: false
		});
		
		// initialize loading bar
		this._setLoadingBar(20);
		
		// initialize iframe
		this.iframe = $('iframe.content');

		// iframe focusing and blurring
		this.iframe.bind('mouseleave', function() {
			$(this).trigger('blur');
		});

		this.iframe.bind('mouseenter mousedown', function() {
			// if there is another textarea/input that's focused, don't focus the iframe.
			if ( $('textarea:focus, input:focus').length === 1 )
				return;

			$(this).trigger('focus');
		});
		
		this._loadIFrame(this._iFrameCallback);
		
		// initialize LayoutSelector
		$('#layoutSelectorToggle').click($.proxy(this._toggleLayoutSelector, this));
		$('#layoutSelector span.edit').on('click', $.proxy(this._changeLayout, this));
		$('#layoutSelector').delegate('span.addTemplate', 'click', $.proxy(this._addTemplate, this));
		$('#layoutSelector').delegate('span.assignTemplate', 'click', $.proxy(this._assignTemplate, this));
		$('#layoutSelector').delegate('span.removeTemplate', 'click', $.proxy(this._removeTemplate, this));
		$('#layoutSelector span.layout').on({
			'mouseenter': $.proxy(function(event) {
				var $target = $(event.target);
				$target.find('span.button').each($.proxy(function(index, item) {
					$item = $(item);
					$item.removeClass('ultimateHidden');
				}, this));
				$target.find('img').each($.proxy(function(index, item) {
					$item = $(item);
					$item.removeClass('ultimateHidden');
				}, this));
				return true;
			}, this),
			'mouseleave': $.proxy(function(event) {
				var $target = $(event.target);
				if ($target.hasClass('layoutOpen')) return true;
				$target.find('span.button').each($.proxy(function(index, item) {
					$item = $(item);
					$item.addClass('ultimateHidden');
				}, this));
				$target.find('img').each($.proxy(function(index, item) {
					$item = $(item);
					$item.addClass('ultimateHidden');
				}, this));
				return true;
			}, this)
		});
		
		$('#layoutSelector').find('span.layoutOpen > span.smallButton').each($.proxy(function(index, item) {
			var $item = $(item);
			$item.removeClass('ultimateHidden');
		}, this));
		WCF.Collapsible.Simple.init();
		
		if ( $.cookie( 'hideLayoutSelector' ) === 'true' ) {
			this._hideLayoutSelector();
		}
		
		// initialize bottom panel
		var panelMinHeight = 120;
		var panelMaxHeight = function() { return $(window).height() - 275; };
		this._bottomPanel = new ULTIMATE.VisualEditor.BottomPanel(this, 'bottomPanel', panelMinHeight, panelMaxHeight);
		
		// initialize save button
		$('#visualEditorForm').submit($.proxy(this._stopFormSubmit, this));
		//$('#saveButton:enabled').click($.proxy(this.save, this));
	},
	
	/**
	 * Returns an element from the iframe.
	 * 
	 * @param	{String}	element
	 * @returns	{jQuery}
	 */
	_i: function(element) {
		return this.iframe.contents().find(element);
	},
	
	/**
	 * Returns an object of the VisualEditor.
	 * 
	 * @returns {ULTIMATE.VisualEditor}
	 */
	getVisualEditor: function() {
		return this;
	},
	
	/**
	 * Loads the block content.
	 * 
	 * @param	{Object}	args
	 * @returns	{String}
	 */
	loadBlockContent: function(args) {
		var settings = {};
		var defaults = {
			blockElement: false,
			blockSettings: {},
			blockOrigin: false,
			blockDefault: false,
			callback: function(args){},
			callbackArgs: null
		};
		$.extend(settings, defaults, args);
			
		var blockContent = settings.blockElement.find('div.block-content');
		var blockType = ULTIMATE.Block.getBlockType(settings.blockElement);
		
		blockContent.siblings('h3.block-type').hide();
		var $blockContent = '';
		var $proxy = new WCF.Action.Proxy({
			url: 'index.php/Block/?t=' + SECURITY_TOKEN + SID_ARG_2ND,
			showLoadingOverlay: false,
			data: {
				actionName: 'loadBlockContent',
				parameters: {
					unsavedBlockSettings: settings.blockSettings,
					blockOrigin: settings.blockOrigin,
					blockDefault: settings.blockDefault,
					templateID: this.currentLayoutTemplate
				}
			},
			success: $.proxy(function(data, textStatus, jqXHR) {
				$blockContent = data;
				if ( typeof settings.callback == 'function' )
					settings.callback(settings.callbackArgs);
				blockContent.siblings('h3.block-type').show();
			}, this)
		});
		$proxy.sendRequest();
		return blockContent.html($blockContent);
	},
	
	/**
	 * Loads the iframe.
	 * 
	 * @param	{Function}	callback
	 */
	_loadIFrame: function(callback) {
		var iframeURL = RELATIVE_ULTIMATE_DIR + 'index.php/VisualEditor/?visualEditorIFrame=true&layout=' + this.currentLayout;
				
		// since the default iframe load function is used for all modes, we can just pack it in with the normal callback				
		var callback_with_default = function() {
			this._setLoadingBar(85);
						
			if ( typeof callback === 'function' ) {
				callback();
			}
			
			this._defaultIFrameLoad();
		}						
								
		// use iframe plugin so it can detect a timeout.  If there's a timeout, refresh the entire page.
		this.iframe.src(iframeURL, callback_with_default, {
			timeout: $.proxy(function(duration) {
				this._iframeTimeout = true;	
				changeTitle('VisualEditor: ' + WCF.Language.get('ultimate.visualEditor.error') + '!');									
				$('div#loading div.loading-message p.tip').html('<strong>' + WCF.Language.get('ultimate.visualEditor.error') + ':</strong> ' 
					+ WCF.Language.get('ultimate.visualEditor.loadingError'));
				
				$('div#loading div.loading-bar').css('borderColor', '#D8000C');	
				$('div#loading div.loading-bar div.loading-bar-inside').stop(true).css({background: '#D8000C', width: '100%'});	
				$('div#loading div.loading-message p.tip, div#loading div.loading-message p.tip strong').css('color', '#D8000C');
				
				setTimeout(function(){
					window.location.href = unescape(window.location);
				}, 4000);
			
			}, this),
			timeoutDuration: 40000
		});
	},
	
	/**
	 * Sets the loading bar.
	 * 
	 * @param	{Integer}	percent
	 * @param	{Function}	callback
	 */
	_setLoadingBar: function(percent, callback) {
		if ( (typeof this._loadingComplete != 'undefined' && this._loadingComplete == true) || (typeof this._iframeTimeout != 'undefined' && this._iframeTimeout == true) )
			// don't animate again
			return false;
		
		$('div.loading-bar-inside').css({'width': ($('div.loading-bar').width() * (percent/100))});

		if ( typeof callback !== 'function' )
			callback = function(){};

		setTimeout(callback, 120);
		
		if ( percent == 100 )
			this._loadingComplete = true;
	},
	
	/**
	 * Called when IFrame stops loading.
	 */
	_defaultIFrameLoad: function() {
		changeTitle('VisualEditor: ' + this.currentLayoutName);
		$('div#current-layout strong span').text(this.currentLayoutName);
	
		new WCF.Effect.BalloonTooltip();
		
		// handle layout selector cookie
		if ( $.cookie('hideLayoutSelector') === 'true' ) {
			this._hideLayoutSelector();
		}
	
		this._setLoadingBar(100, 'Complete', function(){
			$('div#loading').animate({opacity: 0}, 400, function(){ 
				$(this).remove(); 
			});
		});
		

		this._stylesheet = new ITStylesheet({document: this.iframe.contents()[0], href: RELATIVE_ULTIMATE_DIR + 'style/bootstrapVisualEditor.css'}, 'find');
		this._css = new ITStylesheet({document: this.iframe.contents()[0]}, 'load');
		
		// add the template notice if it's layout mode and a template is active
		if ( this.currentLayoutTemplate ) {
			this._i('body').prepend('<div id="template-notice"><h1>'+ WCF.Language.get('ultimate.visualEditor.layoutSelector.templateActive') + '</h1></div>');
		}
		
		// clear hidden inputs
		$('#hiddenInputs').html('');
		
		// deactivate all links and buttons
		this.iframe.contents().find('body').delegate('a, input[type="submit"], button', 'click', function(event) {
			if ( $(this).hasClass('allow-click') )
				return;
			
			event.preventDefault();
			return false;
		});
		
		// show the load message
		if ( typeof this._iframeLoadNotification !== 'undefined' ) {
			this._iframeLoadNotification.show();
			
			delete this._iframeLoadNotification;
		}
		
		// remove the tabs that are set to close on layout switch
		this._bottomPanel.removeLayoutSwitchPanels();
		
		// show the grid wizard if the current layout isn't customized and not using a tmeplate
		var layoutNode = $('div#layoutSelector span.layout[layout_id="' + this.currentLayout + '"]');
		var layoutLi = layoutNode.parent();
				
		if ( 
			!layoutNode.hasClass('layoutTemplate') 
			&& !layoutLi.hasClass('layout-item-customized') 
			&& !layoutLi.hasClass('layoutItemTemplateUsed')
		) {
			this._bottomPanel.hidePanel();
			openBox('grid-wizard');
		} else {
			closeBox('grid-wizard');
		}
		
		// clear out and disable iframe loading indicator
		$('div#iframeLoadingOverlay').fadeOut(500).html('');
	},
	
	/**
	 * Adds block controls.
	 * 
	 * @param	{Boolean}	showOptions
	 * @param	{Boolean}	showDelete
	 */
	_addBlockControls: function(showOptions, showDelete) {
		if ( typeof showOptions == 'undefined' )
			var showOptions = false;
			
		if ( typeof showDelete == 'undefined' )
			var showDelete = false;
		
		var blocks = this._i('.block');
		
		blocks.each(function() {
			var id = ULTIMATE.Block.getBlockID(this);
			var type = ULTIMATE.Block.getBlockType(this);	
			var typeNice = ULTIMATE.Block.getBlockTypeNice(type);
				
			var idTooltip = 'This is the ID for the block.  The ID of the block is displayed in the WordPress admin panel if it is a widget area or navigation block.  Also, this can be used with advanced developer functions.';
			idTooltip = WCF.Language.get('ultimate.visualEditor.block.idTooltip');
			var changeBlockTypeTooltip = 'Click to change the block type.';
			changeBlockTypeTooltip = WCF.Language.get('ultimate.visualEditor.block.changeBlockTypeTooltip');
			var optionsTooltip = 'Show the options for this block.';
			optionsTooltip = WCF.Language.get('ultimate.visualEditor.block.optionsTooltip');
			var deleteTooltip = 'Delete this block.';
			deleteTooltip = WCF.Language.get('ultimate.visualEditor.block.delete');
			
			$(this).append('\
				<div class="block-info">\
					<span class="id jsTooltip" title="' + idTooltip + '">' + id + '</span>\
					<span class="type type-' + type + ' jsTooltip" title="' + changeBlockTypeTooltip + '">' + typeNice + '</span>\
				</div>');
				
			// Make sure at least one of the buttons in block controls is going to be shown.  If both are hidden, don't add the block controls <div>.
			if ( !(showOptions == false && showDelete == false) ) {
				
				var optionsButton = ( showOptions == true ) ? '<span class="options jsTooltip" title="' + optionsTooltip + '">' + WCF.Language.get('ultimate.visualEditor.block.options') + '</span>' : '';
				var deleteButton = ''; 
				if (showDelete) {
					deleteButton += '<img src="'
					+ WCF.Icon.get('wcf.icon.delete') 
					+ '" alt="" class="jsDeleteButton jsTooltip icon16" title="' 
					+ deleteTooltip 
					+ '" data-object-id="'
					+ id 
					+ '" data-confirm-message="' 
					+ WCF.Language.get('ultimate.visualEditor.block.delete.sure')
					+ '"/>';
				}
				$(this).append('\
					<div class="block-controls">\
						' + optionsButton + '\
						' + deleteButton + '\
					</div>');
					
			}
			new WCF.Action.Delete('ultimate\\data\block\\BlockAction', $('.block'));	
		});
		
		this._bindBlockControls();
	},
	
	/**
	 * Adds a new input field for the deleted block with the given id.
	 * 
	 * @param	{String}	id
	 */
	_addDeleteBlockHidden: function(id) {
		if ( typeof id === 'string' && id.indexOf('block-') !== -1 ) {
			var id = id.replace('block-', '');
		}
		
		var hiddenInputClass = 'block-' + id + '-delete';
		$('<input type="hidden" class="' + hiddenInputClass + '" name="blocks[' + id + '][delete]" value="true"  />')
			.appendTo('div#hiddenInputs');
			
		// remove the hidden input flags that may exist for the block
		$('div#hiddenInputs input.block-' + id + '-new').remove();
		$('div#hiddenInputs input.block-' + id + '-position').remove();
		$('div#hiddenInputs input.block-' + id + '-dimensions').remove();
		
	},
	
	/**
	 * Adds a new hidden input field for the new block with the given id.
	 * 
	 * @param	{String}	id
	 * @param	{String}	type
	 */
	_addNewBlockHidden: function(id, type) {
		
		if ( typeof id === 'string' && id.indexOf('block-') !== -1 ) {
			var id = id.replace('block-', '');
		}
		
		var hiddenInputClass = 'block-' + id + '-new';
		
		$('<input type="hidden" class="' + hiddenInputClass + '" name="blocks[' + id + '][new]" value="' + type + '"  />')
			.appendTo('div#hiddenInputs');
			
		// remove the delete hidden if it exists
		$('div#hiddenInputs input.block-' + id + '-delete').remove();
	},
	
	/**
	 * Binds the block controls.
	 */
	_bindBlockControls: function() {
		
		// block type
		this._i('body').delegate('.block div.block-info span.type', 'click', $.proxy(function(event) {
			var $target = $(event.target);
			var block = ULTIMATE.Block.getBlock($target);
			var blockInfo = $target.parents('.block-info');
			
			var type = ULTIMATE.Block.getBlockType(block);
			
			// if the block info is shown then hide it if they click the same button.  Otherwise show the block info.
			if ( !block.hasClass('block-info-show') ) {
				// force the ID and block type icon to stay visible
				block.addClass('block-info-show');
			
				// keep track of this block so we can remove the block-info-show class later.
				this._blockTypeSwitchBlock = block;
				this.showBlockTypePopup({top: block.position().top + 36, left: block.position().left + 5}, true);
			
				// hide the current block type from the list
				this._blockTypePopup.find('li#block-' + type).addClass('blockTypeHidden');
			} else {			
				this._blockTypeSwitchBlock.removeClass('block-info-show');
				this.hideBlockTypePopup();
				delete this._blockTypeSwitchBlock;
			}			
			event.preventDefault();
			
		}, this));
		
		// Options
		this._i('body').delegate('.block div.block-controls span.options', 'click', $.proxy(function(event) {
			var $target = $(event.target);
			var block = ULTIMATE.Block.getBlock($target);
			
			var blockID = ULTIMATE.Block.getBlockID(block);		    
			var blockType = ULTIMATE.Block.getBlockType(block);		
			var blockTypeName = ULTIMATE.Block.getBlockTypeNice(blockType);
									
			var readyTabs = $.proxy(function(data, textStatus, jqXHR) {
				var tab = $('div#block-' + blockID + '-tab');
				
				// ready tab, sliders, and inputs
				tab.wcfTabs();
				setUpPanelInputs('div#block-' + blockID + '-tab');
				
				// refresh tooltips
				new WCF.Effect.BalloonTooltip();
				
				// Call the open callback for the box panel.
				var callback = eval(tab.find('ul.subTabs').data('openCallback'));
				callback({
					block: block,
					blockID: blockID,
					blockType: blockType
				});
				
				this._bottomPanel.element.find('div#block-' + blockID + '-tab').html(data);
			}, this);						
			
			var blockIDForTab = isNaN(blockID) ? ': ' + blockID : ' #' + blockID;
						
			this._bottomPanel.addPanelTab('block-' + blockID, blockTypeName + ' Block' + blockIDForTab, {
				url: 'index.php/Block/?t=' + SECURITY_TOKEN + SID_ARG_2ND,
				data: {
					actionName: 'loadBlockOptions',
					parameters: {
						blockOrigin: {
							blockType: blockType,
							blockID: blockID
						},
						unsavedBlockOptions: ULTIMATE.Block.getUnsavedBlockOptionValues(blockID),
						templateID: this.currentLayoutTemplate
					}
				}, 
				success: readyTabs}, true, true, 'block-type-' + blockType);
			this.element.wcfTabs('select', 'block-' + blockID + '-tab');
		}, this));
	
		// Block Dimensions
		this._i('body').delegate('.block', 'mouseenter', $.proxy(function(event) {
			var block = ULTIMATE.Block.getBlock(event.target);
			
			var blockWidth = ULTIMATE.Block.getBlockDimensionsPixels(block).width;	
			var blockHeight = ULTIMATE.Block.getBlockDimensionsPixels(block).height;					
			var blockType = ULTIMATE.Block.getBlockType(block);		
			var heighText = '';
			if ( ULTIMATE.Block.getBlockTypeObject(blockType)['fixedHeight'] ) {
				heightText = WCF.Language.get('ultimate.visualEditor.block.height');
			} else {
				heightText = WCF.Language.get('ultimate.visualEditor.block.minHeight');
			}
						
			var height = '<span class="block-height"><strong>' + heightText + ':</strong> ' + blockHeight + '<small>px</small></span>';
			var width = '<span class="block-width"><strong>' + WCF.Language.get('ultimate.visualEditor.block.width') + '</strong> ' + blockWidth + '<small>px</small></span>';

			var fluidMessage = !ULTIMATE.Block.getBlockTypeObject(blockType)['fixedHeight'] ? '<span class="block-fluid-height-message">' + WCF.Language.get('ultimate.visualEditor.block.height.autoExpand') + '</span>'  : '';

			block.attr('title', width + ' <span class="block-dimensions-separator">&#9747;</span> ' + height + fluidMessage);
			new WCF.Effect.BalloonTooltip();
			// if tooltip is hidden
			$('#balloonTooltip').wcfFadeIn();
		}, this));
		
		this._i('body').delegate('.block', 'mouseleave', function(event) {
			clearTimeout($(this).data('hoverWaitTimeout'));
		});
		
		// hide block dimensions if hover over a control or info icon
		this._i('body').delegate('.block-controls, .block-info', 'mouseenter', function(event) {
			var block = ULTIMATE.Block.getBlock(this);	
			$('#balloonTooltip').stop().hide();
			clearTimeout(block.data('hoverWaitTimeout'));
		});
		
		this._i('body').delegate('.block-controls, .block-info', 'mouseleave', function(event) {
			var block = ULTIMATE.Block.getBlock(this);	
			block.data('hoverWaitTimeout', setTimeout(function() {
				$('#balloonTooltip').wcfFadeIn();
			}, 300));
		});

	},
	
	/**
	 * Called on loading of the iframe.
	 */
	_iFrameCallback: function() {
		this.iframe.grid('destroy');
		
		var columns = 24;
		var columnWidth = 20;
		var gutterWidth = 20;	
						
		this.iframe.grid({
			columns: columns,
			container: 'div.grid-container',
			defaultBlockClass: 'block',
			columnWidth: columnWidth,
			gutterWidth: gutterWidth
		});
		
		this._addBlockControls(true, true);
		this._initBlockTypePopup();
	},
	
	/**
	 * Initializes the block type popup.
	 */
	_initBlockTypePopup: function() {
		this._blockTypePopup = $(this.selectBlockTypeDialogID).clone();
		this._blockTypePopup.appendTo(this._i('.grid-container'));
		
		this._i(this._selectBlockTypeDialogID).delegate('li:not(.not-block-type)', 'click', $.proxy(function(event) {			
			var $target = $(event.target);
			var blockType = $target.attr('id').replace('block-type-', '');
			
			// either create a new block or switch the type of the selected block
			if ( this._blockTypeSwitch === 'undefined' || this._blockTypeSwitch === false ) {
				this.iframe.grid('setupBlankBlock', blockType);
			} else {
				
				if ( !confirm(WCF.Language.get('ultimate.visualEditor.block.switchBlockType.sure')) ) {
					this.hideBlockTypePopup();
					return false;
				}
				
				this.switchBlockType(this._blockTypeSwitchBlock, blockType);
			}
			
			// keep it from bubbling
			event.stopPropagation();
			
		}, this));
	},
	
	/**
	 * Shows the block type popup.
	 * 
	 * @param	{jQuery}	position
	 * @param	{Boolean}	blockTypeSwitch
	 */
	showBlockTypePopup: function(position, blockTypeSwitch) {
		
		if ( typeof blockTypeSwitch === 'undefined' || blockTypeSwitch === false ) {
			this._blockTypeSwitch = false;
		} else {
			this._blockTypeSwitch = true;
		}
				
		var blockTypePopupWidth = this._blockTypePopup.width();
		var blockTypePopupHeight = this._blockTypePopup.height();
				
		var bodyWidth = this._i('body').width();
		var bodyHeight = this._i('body').height();
		
		var iframeLeft = parseInt(this.iframe.css('paddingLeft').replace('px', ''));
		var blockTypePopupCSS = {}	
		// if the position is a block object, figure it out from that.
		if ( typeof position.hasClass == 'function' && position.hasClass('block') ) {
			var block = position;
			var rightCutoffOffset = 20;
			var bottomCutoffOffset = 25;
			blockTypePopupCSS = {
				top: block.position().top
			}
		
			// if block type popup runs over right edge, then flip the y-axis that the block type popup sits on			
			if ( block.offset().left + block.width() + blockTypePopupWidth + rightCutoffOffset > bodyWidth ) {
				blockTypePopupCSS.left = block.position().left + block.width() - blockTypePopupWidth - 10;
			} else {
				blockTypePopupCSS.left = block.position().left + block.width() + 10;
			}

			var iframeTop = parseInt(this.iframe.css('paddingTop').replace('px', ''));
				
			// iframeOffset has to be in both of these to offset itself
			var absoluteBottomOfSelector = block.position().top + blockTypePopupHeight + bottomCutoffOffset - this.iframe.contents().scrollTop();
			var screenBottom = this.iframe.height() - iframeTop;
		
			if ( absoluteBottomOfSelector >= screenBottom ) {
				var difference = absoluteBottomOfSelector - screenBottom;
				blockTypePopupCSS.top = block.position().top - difference;
			}
		// we have a pre-defined position
		} else {
			blockTypePopupCSS = {
				top: position.top,
				left: position.left
			}			
		}

		// show all block types again
		this._blockTypePopup.find('.block-type-hidden').removeClass('block-type-hidden');
		this._blockTypePopup.show().css(blockTypePopupCSS);
		$(document).bind('mousedown', {hideBlock: true}, this.hideBlockTypePopup);
		this.iframe.contents().bind('mousedown', {hideBlock: true}, this.hideBlockTypePopup);
	},

	/**
	 * Hides the block type popup.
	 * 
	 * @param	{jQuery.Event}	event
	 */
	hideBlockTypePopup: function(event) {
		if ( typeof event == 'undefined' )
			event = {data: {hideBlock: false}};
		if ( event.data.hideBlock ) {
			// if clicking box, do not hide
			if ( $(event.target).parents('.block').length === 1 )
				return false;
			
			// if the popup isn't visible, don't try to hide
			if ( !this._blockTypePopup.is(':visible') )
				return false;
			
			// if clicking a block type option, do not let this function run
			if ( $(event.target).parents(this._selectBlockTypeDialogID)[0] === this._blockTypePopup[0] )
				return false;
		}
			
		// commence hiding
		this._blockTypePopup.hide();
		
		// delete the block if it exists
		if ( event.data.hideBlock && typeof this._blankBlock !== 'undefined' )
			this._blankBlock.remove();
						
		if ( this._blockTypeSwitch ) {
			this._blockTypeSwitchBlock.removeClass('block-info-show');
			
			delete this._blockTypeSwitch;
		}
		
		$(document).unbind('mousedown', this.hideBlockTypePopup);		
		this.iframe.contents().unbind('mousedown', this.hideBlockTypePopup);
		
		return true;
	},
	
	/**
	 * Switches the block type.
	 * 
	 * @param	{String}	block
	 * @param	{String}	blockType
	 */
	switchBlockType: function(block, blockType) {
		var oldType = ULTIMATE.Block.getBlockType(block);
		var blockID = ULTIMATE.Block.getBlockID(block);
		
		block.removeClass('block-type-' + oldType);
		block.addClass('block-type-' + blockType);

		block.find('.block-info span.type')
			.attr('class', '')
			.addClass('type')
			.addClass('type-' + blockType)
			.html(ULTIMATE.Block.getBlockTypeNice(blockType));
			
		block.find('h3.block-type span').text(ULTIMATE.Block.getBlockTypeNice(blockType));
		
		this.loadBlockContent({
			blockElement: block,
			blockOrigin: {
				blockType: blockType,
				blockID: 0
			},
			blockSettings: {
				dimensions: ULTIMATE.Block.getBlockDimensions(block),
				position: ULTIMATE.Block.getBlockPosition(block)
			},
		});
		
		// set the fluid/fixed height class so the fluid height message is shown correctly
		if ( ULTIMATE.Block.getBlockTypeObject(blockType)['fixedHeight'] === true ) {
			
			block.removeClass('block-fluid-height');
			block.addClass('block-fixed-height');

			if ( block.css('min-height').replace('px', '') != '0' ) {
				block.css({
					height: block.css('min-height')
				});
			}
			
		} else {
			block.removeClass('block-fixed-height');
			block.addClass('block-fluid-height');
			if ( block.css('height').replace('px', '') != 'auto' ) {
				block.css({
					height: block.css('height')
				});
			}
		}

		// hide the block type popup
		this.hideBlockTypePopup();
		
		// prepare for hiddens
		var newBlockID = ULTIMATE.Block.getAvailableBlockID();
		var oldBlockID = blockID;
		
		// delete the old block optiosn tab if it exists
		this._bottomPanel.removePanelTab('block-' + oldBlockID);
		
		// add hiddens to delete old block and add new block in its place
		this._addDeleteBlockHidden(oldBlockID);
		this._addNewBlockHidden(newBlockID, blockType);
		ULTIMATE.Block.updateBlockPositionHidden(newBlockID, ULTIMATE.Block.getBlockPosition(block));
		ULTIMATE.Block.updateBlockDimensionsHidden(newBlockID, ULTIMATE.Block.getBlockDimensions(block));

		// update the ID on the block
		block
			.attr('id', 'block-' + newBlockID)
			.data('id', newBlockID);

		block.find('div.block-info span.id').text(newBlockID);
		
		// allow saving now that the type has been switched
		this.allowSaving();
		new WCF.Effect.BalloonTooltip();
	},
	
	/**
	 * Allowes saving.
	 */
	allowSaving: function() {
		// if there are no blocks on the page, then do not allow saving.
		if ( this._i('.block').length === 0 ) {
			this.disallowSaving();
			return false;
		}				
		// if saving is already allowed, don't do anything else
		if ( typeof this.isSavingAllowed !== 'undefined' && this.isSavingAllowed === true ) {
			return;
		}		
				
		$('#saveButton').removeClass('disabled').prop('disabled', false);
		this.isSavingAllowed = true;
		
		// Set reminder whne trying to leave that there are changes.
		this.prohibitVEClose();
		return true;
	},
	
	/**
	 * Disallowes saving.
	 */
	disallowSaving: function() {
		this.isSavingAllowed = false;
		
		$('#saveButton').addClass('disabled').prop('disabled', true);
		
		// User can safely leave VE now--changes are saved.
		this.allowVEClose();
		return true;
	},
	
	/**
	 * Disallowes the user to close the VisualEditor.
	 */
	prohibitVEClose: function () {	
		$(window).bind('beforeunload', function(event){
			event.returnValue = WCF.Language.get('ultimate.visualEditor.unsavedChangesOnLeaving.sure');
			return WCF.Language.get('ultimate.visualEditor.unsavedChangesOnLeaving.sure');
		});
		
		this.allowVECloseSwitch = false;
	},
	
	/**
	 * Stops the form from being submitted.
	 * 
	 * @param	{jQuery.Event}	event
	 */
	_stopFormSubmit: function(event) {
		event.preventDefault();
		if ($('#visualEditorForm input[name*="_i18n"]').length == 0) {
			if (!$('#saveButton').prop('disabled')) this.save();
		} 
		else if ($('#visualEditorForm input[name*="_i18n"]').length > 0) {
			if (!$('#saveButton').prop('disabled')) this.save();
		}
	},
	
	/**
	 * Saves the blocks and other stuff.
	 */
	save: function() {
		var $target = $('#saveButton');
		// If saving isn't allowed, don't try to save.
		if ( typeof this.isSavingAllowed === 'undefined' || this.isSavingAllowed === false ) {
			return false;
		}
		
		// If currently saving, do not do it again.
		if ( typeof this.currentlySaving !== 'undefined' && this.currentlySaving === true ) {
			return false;
		}
	
		this.currentlySaving = true;
		this.saveButton = $target;
		this.savedTitle = $('title').text();
		$target
			.text(WCF.Language.get('ultimate.visualEditor.saving'))
			.addClass('active')
			.css('cursor', 'wait');
		
		// change the title
		changeTitle('VisualEditor: ' + WCF.Language.get('ultimate.visualEditor.saving'));
		
		// serialize options
		var options = $('div#hiddenInputs input').serialize();
		
		var $data = $.extend(true, {
			actionName: 'saveOptions',
			parameters: {
				templateID: this.currentLayoutTemplate,
				options: options
			},
			success: $.proxy(this._successSaveOptions, this)
		}, { });
		
		// if there are i18n values add them to post values accordingly
		var $i18nExtendData = {};
		$('#visualEditorForm input[name*="_i18n"]').each($.proxy(function(index, listItem) {
			var $listItem = $(listItem);
			var $name = $listItem.attr('name');
			var $languageID = $name.substring($name.indexOf('['));
			$name = $name.substring(0, $name.indexOf('['));
			var i18nValues = {};
			$languageID = $languageID.substr(0, $languageID.length - 1);
			i18nValues[$languageID] = $listItem.val();
			$i18nExtendData[$name] = i18nValues;
		}, this));
		$data = $.extend(true, $i18nExtendData, $data);
		var url = 'index.php/Block/?t=' + SECURITY_TOKEN + SID_ARG_2ND
		this._proxy.setOption('url', url);
		this._proxy.setOption('showLoadingOverlay', true);
		this._proxy.setOption('data', $data);
		this._proxy.sendRequest();
	},
	
	/**
	 * Called after successful saving operation.
	 * 
	 * @param	{Object}	data
	 * @param	{String}	textStatus
	 * @param	{jQuery}	jqXHR
	 */
	_successSaveOptions: function(data, textStatus, jqXHR) {
		delete this.currentlySaving;
		
		// If it's not a successful save, revert the save button to normal and display an alert.
		if ( data !== 'success' ) {
			this.saveButton.text(WCF.Language.get('ultimate.visualEditor.save'));
			this.saveButton.removeClass('active');
			this.saveButton.css('cursor', 'pointer');
			var notification = new WCF.System.Notification(WCF.Language.get('ultimate.visualEditor.save.error'), 'error');
			return notification.show(undefined, 6000);
		// successful save
		} else {
			setTimeout(function() {
				this.saveButton.text(WCF.Language.get('ultimate.visualEditor.save'));
				this.saveButton.removeClass('active');
				
				this.saveButton.css('cursor', 'pointer');
				
				// clear hidden inputs
				$('#hiddenInputs').html('');
				
				// disable button
				this.disallowSaving();				
				
				// reset the title and show the saving complete notification
				setTimeout(function() {
					changeTitle(this.savedTitle);
					var notification = new WCF.System.Notification(WCF.Language.get('ultimate.visualEditor.save.success'));
					notification.show(undefined, 3500);
				}, 150);
			}, 350);

			this.allowVEClose();
		}
	},
	
	/**
	 * Disables some keys which are just annoying.
	 */
	disableBadKeys: function() {
		// disable backspace for normal frame but still keep backspace functionality in inputs.  Also disable enter.
		$(document).bind('keypress', $.proxy(this.disableBadKeysCallback, this));
		$(document).bind('keydown', $.proxy(this.disableBadKeysCallback, this));
	
		// disable backspace and enter for iframe
		this._i('html').bind('keypress', $.proxy(this.disableBadKeysCallback, this));
		this._i('html').bind('keydown', $.proxy(this.disableBadKeysCallback, this));
		
	},
	
	/**
	 * Called each time an annoying key was used.
	 * 
	 * @param	{jQuery.Event}	event
	 */
	disableBadKeysCallback: function(event) {
		// 8 = Backspace
		// 13 = Enter
		var element = $(event.target); 
		if ( event.which === 8 && !element.is('input') && !element.is('textarea') ) {
			event.preventDefault();
			return false;
		}
	
		if ( event.which == 13 && !element.is('textarea') ) {
			event.preventDefault();
			return false;
		}
	},

	/**
	 * Allowes the user to close the VisualEditor.
	 */
	allowVEClose: function() {
		$(window).unbind('beforeunload', function(event){
			event.returnValue = WCF.Language.get('ultimate.visualEditor.unsavedChangesOnLeaving.sure');
			return WCF.Language.get('ultimate.visualEditor.unsavedChangesOnLeaving.sure');
		});
	
		this.allowVECloseSwitch = true;
	},
	
	/**
	 * Toggles the visibility of the layoutSelector.
	 * 
	 * @param	{jQuery.Event}	event
	 * @returns	{Object}
	 */
	_toggleLayoutSelector: function(event) {
		if ( $('div#layoutSelectorOffset').hasClass('open') )
			return this._hideLayoutSelector();
		
		return this._showLayoutSelector();		
	},
	
	/**
	 * Shows the layoutSelector.
	 * 
	 * @returns	{Object}
	 */
	_showLayoutSelector: function() {
		$('div#layoutSelectorOffset').css({left: '-60px'}).addClass('open');
		this.iframe.css({paddingLeft: '295px'});
		$('body').removeClass('layoutSelectorHidden');
		$('span#layoutSelectorToggle').text(WCF.Language.get('ultimate.visualEditor.layoutSelector.hide'));
		$.removeCookie('hideLayoutSelector');
		return $.cookie('hideLayoutSelector', false);
	},
	
	/**
	 * Hides the layoutSelector.
	 * 
	 * @returns	{Object}
	 */
	_hideLayoutSelector: function() {
		$('div#layoutSelectorOffset').css({left: '-350px'}).removeClass('open');
		this.iframe.css({paddingLeft: '0'});
		$('body').addClass('layoutSelectorHidden');
		$('span#layoutSelectorToggle').text(WCF.Language.get('ultimate.visualEditor.layoutSelector.show'));
		$.removeCookie('hideLayoutSelector');
		return $.cookie('hideLayoutSelector', true);
	},
	
	/**
	 * Changes the layout.
	 * 
	 * @param {jQuery.Event}	event
	 */
	_changeLayout: function(event) {
		var $target = $(event.target);
		var $layout = $target.parent();
		this._switchToLayout($layout, true, false);
	},
	
	/**
	 * Switches the layout.
	 * 
	 * @param	{jQuery}	layoutNode
	 * @param	{Boolean}	reloadIframe
	 */
	_switchToLayout: function(layoutNode, reloadIframe) {
		if ( typeof layoutNode == 'object' && !layoutNode.hasClass('layout') )
			layoutNode = layoutNode.find('> span.layout');
			
		if ( layoutNode.length !== 1 )
			return false;
				
		changeTitle('VisualEditor: ' + WCF.Language.get('ultimate.visualEditor.loading'));
		var layout = layoutNode;
		var layoutID = layout.data('layoutID');
		var layoutName = layout.find('strong').text();
				
		// flip classes around
		$('div#layoutSelector').find('.layoutOpen').removeClass('layoutOpen');
		layout.parent('li').addClass('layoutOpen');
		
		// set global variables, these will be used in the next function to switch the iframe
		this.currentLayout = layoutID;
		this.currentLayoutName = layoutName;
		this.currentLayoutTemplate = false;
				
		// check if the layout node has a template assigned to it.  
		var possibleTemplateID = layout.find('.statusTemplate').data('templateID');
						
		if ( typeof possibleTemplateID != 'undefined' && possibleTemplateID != 'none' )
			this.currentLayoutTemplate = possibleTemplateID;
		
		// add the hash of the layout to the URL
		window.location.hash = '#layout=' + this.currentLayout;
		
		// reload iframe and new layout right away
		if ( typeof reloadIframe == 'undefined' || reloadIframe == true ) {
			this._loadIframe(this._iFrameCallback);
		}			
		return true;
	},
	
	/**
	 * Adds a new template.
	 * 
	 * @param	{jQuery.Event}	event
	 */
	_addTemplate: function(event) {
		var $target = $(event.target);
		var $parent = $target.parent();
		var $input = $parent.find('input');
		var $parameters = $.extend(true, {
			data: {
				templateName: $input.val()
			}
		}, { });
		
		var $data = $.extend(true, {
			actionName: 'createAJAX',
			className: this._templateClassName,
			parameters: $parameters			
		}, { });
		
		this._proxy.setOption('data', $data);
		this._proxy.setOption('success', $.proxy(this._successTemplateAdd, this));
		this._proxy.sendRequest();
	},
	
	/**
	 * Assigns a template to a layout.
	 * 
	 * @param	{jQuery.Event}	event
	 */
	_assignTemplate: function(event) {
		var $target = $(event.target);
		var templateNode = $($target.parents('li')[0]);
		var template = $target.parent().data('layoutID').replace('template-', '');

		// if the current layout being edited is a template trigger an error.
		if ( this.currentLayout.indexOf('template-') === 0 ) {
			alert(WCF.Language.get('ultimate.visualEditor.assignTemplate.error'));
			return false;
		}
					
		// do the AJAX request to assign the template
		var $data = $.extend(true, {
			actionName: 'assignTemplate',
			className: 'ultimate\\data\\layout\\LayoutAction',
			parameters: {
				data: {
					layout: this.currentLayout,
					template: template
				}
			},
			success: $.proxy(function(data, textStatus, jqXHR) {
				$('div#layoutSelector').find('li.layoutOpen').addClass('layoutItemTemplateUsed');
				$('div#layoutSelector').find('li.layoutOpen span.statusTemplate').text(data['returnValues']['templateName']);
			
				$('div#iframeLoadingOverlay').fadeIn(500);
				
				// change title to loading
				changeTitle('VisualEditor: ' + WCF.Language.get('ultimate.visualEditor.assignTemplate'));
				this.currentLayoutTemplate = 'template-' + template;
				
				//Reload iframe and new layout
				this._iframeLoadNotification = new WCF.System.Notification(WCF.Language.get('ultimate.visualEditor.assignTemplate.success'));
				this._loadIframe(this._iFrameCallback);
			}, this)
		}, { });
		this._proxy.setOption('data', $data);
		this._proxy.sendRequest();
		
		return false;
	},
	
	/**
	 * Removes a template from a layout.
	 * The template itself remains.
	 * 
	 * @param	{jQuery.Event}	event
	 */
	_removeTemplate: function(event) {
		var $target = $(event.target);
		var layoutNode = $($target.parents('li')[0]);
		var layoutID = $target.parent().data('layoutID');
					
		// Do the AJAX request to assign the template
		var $data = $.extend(true, {
			actionName: 'removeTemplate',
			className: 'ultimate\\data\\layout\\LayoutAction',
			parameters: {
				layout: layoutID
			},
			success: $.proxy(function(data, textStatus, jqXHR) {
				if ( typeof data['returnValues'] === 'undefined' || data['returnValues'] == 'failure' ) {
					var notification = new WCF.System.Notification(WCF.Language.get('ultimate.visualEditor.removeTemplate.error'), 'error');
					notification.show(undefined, 6000);
					return false;
				}
				layoutNode.removeClass('layoutItemTemplateUsed');
				// If the current layout is the one with the template that we're unassigning, we need to reload the iframe.
				if ( layoutID == this.currentLayout ) {
					// Add loading indicator
					$('div#iframeLoadingOverlay').fadeIn(500);
					
					// Change title to loading
					changeTitle('VisualEditor: ' + WCF.Language.get('ultimate.visualEditor.removingTemplate'));
					this.currentLayoutTemplate = false;
					// reload iframe and new layout
					this.iFrameLoadNotification = WCF.Language.get('ultimate.visualEditor.removeTemplate.success');
					this.loadIframe(this.iFrameCallback);
					return true;
				}
				return true;
			}, this)
		}, { });
		this._proxy.setOption('data', $data);
		this._proxy.setOption('showLoadingOverlay', true);
		this._proxy.sendRequest();
		return false;
	},
	
	/**
	 * Adds the new template to the DOM.
	 * 
	 * @param	{Object}	data
	 * @param	{String}	textStatus
	 * @param	{jQuery}	jqXHR
	 */
	_successTemplateAdd: function(data, textStatus, jqXHR) {
		try {
			var $template = data['returnValues'];
			var html = '<li class="layoutItem">\n';
			html += '<span class="layout layoutTemplate" data-layout-id="template-' + $template['templateID'] + '">\n';
			html += '<strong class="templateName">' + $template['templateName'] + '</strong>\n\n';
			if (ULTIMATE.Permission.get('admin.content.ultimate.canDeleteTemplate')) {
				html += '<img src="' + WCF.Icon.get('wcf.icon.delete') + '" alt="" title="' + WCF.Language.get('wcf.global.button.delete') + '" class="icon16 jsDeleteButton jsTooltip" data-object-id="' + $template['templateID'] + '" data-confirm-message="' + WCF.Language.get('ultimate.visualEditor.layoutSelector.button.deleteTemplate.sure') + '" />\n';
			}
			else {
				html += '<img src="' + WCF.Icon.get('wcf.icon.delete') + '" alt="" title="' + WCF.Language.get('wcf.global.button.delete') + '" class="icon16 disabled" />\n';
			}
			html += '<span class="status statusCurrentlyEditing">\n\n';
			html += WCF.Language.get('ultimate.visualEditor.layoutSelector.status.currentlyEditing') + '\n';
			html += '</span>\n';			
			html += '<span class="edit button">\n\n';
			html += WCF.Language.get('ultimate.visualEditor.layoutSelector.button.edit') + '\n';
			html += '</span>\n</span>\n</li>\n';
			
			$('#layoutSelectorTemplates > ul').append(html);
			new WCF.Action.Delete('ultimate\\data\\template\\TemplateAction', $('.jsLayoutItem'));
		}
		catch (e) {}
	},
	
	/**
	 * Shows the IFrameOverlay.
	 */
	showIFrameOverlay: function() {
		var overlay = $('div#iframeOverlay');
		var iframe = this.iframe;
		
		var iframeWidth = iframe.width();
		var iframeHeight = iframe.height() + 41;
		iframe.css({
			'z-index': -1
		})
				
		overlay.css({
			top: iframe.css('paddingTop'),
			left: iframe.css('paddingLeft'),
			width: iframeWidth,
			height: iframeHeight
		});
		
		overlay.show();
	},
	
	/**
	 * Hides the IFrameOverlay.
	 * 
	 * @param	{Integer}	delay
	 */
	hideIFrameOverlay: function(delay) {
		if ( typeof delay != 'undefined' && delay == false )
			return $('div#iframeOverlay').hide();
		this.iframe.css({
			'z-index': 4
		});
		// Add a timeout for intense draggers.
		setTimeout(function(){
			$('div#iframeOverlay').hide();
		}, 250);
	}
};

/**
 * Creates a new VisualEditor panel.
 * 
 * @param	{ULTIMATE.VisualEditor}	visualEditor
 * @param	{String}	elementID
 * @param	{Integer}	minHeight
 * @param	{Object}	maxHeightCallback
 * @class	Basic implementation for a VisualEditor panel.
 * @since	version 1.0.0
 */
ULTIMATE.VisualEditor.Panel = Class.extend({
	/**
	 * Contains the min height of the panel.
	 * @type	Integer
	 */
	_minHeight: 120,
	
	/**
	 * Contains the max height callback.
	 * @type	Object
	 */
	_maxHeightCallback: null,
	
	/**
	 * Contains the element.
	 * @type	jQuery
	 */
	_element: null,
	
	/**
	 * Contains the VisualEditor this panel belongs to.
	 * @type	ULTIMATE.VisualEditor
	 */
	_visualEditor: null,
	
	/**
	 * Initializes the panel.
	 * 
	 * @param	{ULTIMATE.VisualEditor}	visualEditor
	 * @param	{String}	elementID
	 * @param	{Integer}	minHeight
	 * @param	{Object}	maxHeightCallback
	 */
	init: function(visualEditor, elementID, minHeight, maxHeightCallback) {
		this._visualEditor = visualEditor;
		this._element = $('#' + $.wcfEscapeID(elementID));
		this._minHeight = minHeight;
		this._maxHeightCallback = maxHeightCallback;
	},
	
	/**
	 * Shortcut for getting an element from the content iframe.
	 * 
	 * @param	{String}	element
	 * @return	{jQuery}
	 */
	_i: function(element) {
		return this._visualEditor.iframe.contents().find(element);
	},
	
	/**
	 * Resizes the panel.
	 * 
	 * @param	{Integer}	panelHeight
	 * @param	{Boolean}	resizingWindow
	 */
	resizePanel: function(panelHeight, resizingWindow) {
		if ( typeof panelHeight == 'undefined' || panelHeight == false )
			panelHeight = this._element.height();
		
		if ( panelHeight > this._maxHeight )
			panelHeight = (this._maxHeightCallback() > this._minHeight) ? this._maxHeightCallback() : this._minHeight;
						
		if ( panelHeight < this._minHeight )
			panelHeight = this._minHeight;
							
		if ( typeof resizingWindow != 'undefined' && resizingWindow && panelHeight < this._maxHeightCallback() )
			return;

		this._element.css('height', panelHeight);

		if ( this._element.hasClass('panelHidden') )
			this._element.css({'bottom': -this._element.height()});
		
		return panelHeight;
	},
	
	/**
	 * Hides the panel.
	 */
	hidePanel: function() {
		// If the panel is already hidden, don't go through any trouble.
		if ( this._element.hasClass('panelHidden') )
			return false;
									
		var panelCSS = {bottom: -this._element.height()};
		var iframeCSS = {paddingBottom: (this._element.find('> nav.tabMenu ul').outerHeight() + 38)};
		var layoutSelectorCSS = {paddingBottom: this._element.find('> nav.tabMenu ul').outerHeight() + $('nav#layoutSelectorTabs').height() - 3};

		this._element.css(panelCSS).addClass('panelHidden');
		this._visualEditor.iframe.css(iframeCSS);
		$('div#layoutSelectorOffset').css(layoutSelectorCSS);
		//setTimeout(repositionTooltips, 400);

		$('body').addClass('panelHidden');

		// Add class to button
		this._element.find('> nav.tabMenu li#minimize span').addClass('active');
		
		// Hide the panel top handle to disallow resizing while it's hidden
		this._element.find('> .ui-resizable-handle.ui-resizable-n').fadeOut(200);
		
		return true;
		
	},
	
	/**
	 * Shows the panel.
	 */
	showPanel: function() {
		// If the panel is already visible, don't go through any trouble.
		if ( !this._element.hasClass('panelHidden') )
			return false;
		
		var panelCSS = {bottom: 0};
		var iframeCSS = {paddingBottom: (this._element.outerHeight() + 41)};
		var layoutSelectorCSS = {paddingBottom: this._element.outerHeight() + $('nav#layoutSelectorTabs').height()};
				
		this._element.css(panelCSS).removeClass('panelHidden');
		this._visualEditor.iframe.css(iframeCSS);
		$('div#layoutSelectorOffset').css(layoutSelectorCSS);
		//setTimeout(repositionTooltips, 400);
		$('body').removeClass('panelHidden');
		
		// Remove class from button
		this._element.find('> nav.tabMenu li.minimize span').removeClass('active');
		
		// Show the panel top handle to allow resizing again
		this._element.find('> .ui-resizable-handle.ui-resizable-n').fadeIn(200);
		
		return true;
	}
});

/**
 * Creates a new VisualEditor Bottom Panel.
 * 
 * @class	Implementation of the VisualEditor bottom panel.
 * @see		ULTIMATE.VisualEditor.Panel
 * @since	version 1.0.0
 */
ULTIMATE.VisualEditor.BottomPanel = ULTIMATE.VisualEditor.Panel.extend({
	/**
	 * Contains a proxy.
	 * @type	WCF.Action.Proxy
	 */
	_proxy: null,
	
	/**
	 * Contains a notification.
	 * @type	WCF.System.Notification
	 */
	_notification: null,
	
	/**
	 * Initializes the bottom panel.
	 * 
	 * @see		ULTIMATE.VisualEditor.Panel#init
	 */
	init: function(visualEditor, elementID, minHeight, maxHeightCallback) {
		this._super(visualEditor, elementID, minHeight, maxHeightCallback);
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
		var $self = this;
		this._element.resizable({
			handles: 'n',
			minHeight: 120,
			maxHeight: this._maxHeightCallback(),
			resize: function(event, ui) {
				$(this).css({
					width: '100%',
					position: 'fixed',
					bottom: 0,
					top: ''
				})
				$self._visualEditor.iframe.css({'paddingBottom': ($self._element.outerHeight() + 41)});
				$('div#layoutSelectorOffset').css({paddingBottom: $self._element.outerHeight() + $('nav#layoutSelectorTabs').height()});
				$.proxy($self._visualEditor.showIFrameOverlay, $self._visualEditor)();
			},
			start: $.proxy(this._visualEditor.showIFrameOverlay, this._visualEditor),
			stop: function() {
				$.removeCookie('bottomPanelHeight');
				$.cookie('bottomPanelHeight', $(this).height());
				$.proxy($self._visualEditor.hideIFrameOverlay, $self._visualEditor)();
			}
		});
		
		// The max height option on the resizable must be updated if the window is resized.
		$(window).bind('resize', $.proxy(function(event) {
			// For some reason jQuery UI resizable triggers window resize so that it does only fire if window is truly the target.
			if ( event.target != window )
				return;
			this._element.resizable('option', {maxHeight: this._maxHeightCallback()});
			this.resizePanel(false, true);
		}, this));
		this._element.find('> .ui-resizable-handle.ui-resizable-n')
			.attr('id', 'bottomPanelTopHandle')
			.html('<span></span><span></span><span></span>');
		// Resize the panel according to the cookie right on VE load
		if ( $.cookie('bottomPanelHeight') ) this.resizePanel($.cookie('bottomPanelHeight'), false);
		
		// init option menu
		this._initOptionMenu();
		
		// init toggling
		this._element.find('nav.tabMenu > ul').bind('dblclick', $.proxy(function(event) {
			var $target = $(event.target);
			if ( $target.parent().attr('id') != 'bottomPanelTabs' )
				return false;
			this.togglePanel();
		}, this));

		this._element.find('nav.tabMenu > ul li#minimize span').bind('click', $.proxy(function(event) {
			this.togglePanel();
			return false;
		}, this));

		// check for cookie
		if ( $.cookie('hideBottomPanel') === 'true' ) {
			this.hidePanel(true);
		}
		
		// initialize input events
		
		// selects	
		this.element.find('dd.inputSelect select').bind('change', $.proxy(function(event) {
			var $target = $(event.target);
			this.updatePanelInputHidden({input: $target, value: $target.val()});
			this._visualEditor.allowSaving();
		}, this));
		
		this.element.find('dd.inputMultiSelect select').bind('change', $.proxy(function(event) {
			var $target = $(event.target);
			this.updatePanelInputHidden({input: $target, value: $target.val()});
			this._visualEditor.allowSaving();
		}, this));
		
		// text
		this.element.find('dd.inputText input').bind('keyup blur', $.proxy(function(event) {
			var $target = $(event.target);
			this.updatePanelInputHidden({input: $target, value: $target.val()});
			this._visualEditor.allowSaving();
		}, this));
		
		// textarea
		this.element.find('dd.inputTextarea textarea').bind('keyup blur', $.proxy(function(event) {
			var $target = $(event.target);
			this.updatePanelInputHidden({input: $target, value: $target.val()});
			this._visualEditor.allowSaving();
		}, this));
		
		// integer
		this.element.find('dd.inputInteger input').bind('focus', $.proxy(function(event) {
			var $target = $(event.target);
			if ( typeof this.originalValues !== 'undefined' ) {
				delete this.originalValues;
			}
			
			this.originalValues = new Object;		
			this.originalValues[$target.attr('name')] = $target.val();
		}, this));
		
		this.element.find('dd.inputInteger input').bind('keyup blur', $.proxy(function(event) {
			var $target = $(event.target);
			var value = $(this).val();
			if ( event.type == 'keyup' && value == '-' )
				return;
			// validate the value and make sure it's a number
			if ( isNaN(value) ) {
				// take the nasties out to make sure it's a number
				value = value.replace(/[^0-9]*/ig, '');
				// if the value is an empty string, then revert back to the original value
				if ( value === '' ) {
					var value = this.originalValues[$target.attr('name')];
				}
				// set the value of the input to the sanitized value
				$target.val(value);
			}
			
			// remove leading zeroes
			if ( value.length > 1 && value[0] == 0 ) {
				value = value.replace(/^[0]+/g, '');
				// set the value of the input to the sanitized value
				$target.val(value);
			}
			this.updatePanelInputHidden({input: $target, value: value});
			this._visualEditor.allowSaving();
		}, this));
		
		// checkboxes
		this.element.find('dd.inputCheckbox').bind('click', $proxy(function(event) {
			var $target = $(event.target);
			var input = $target.find('input');
			var label = $target.find('label');
			var button = $target.find('img, label');
			
			if ( label.hasClass('checkboxChecked') === true ) {
				button.removeClass('checkboxChecked');
				input.val(false);
				this.updatePanelInputHidden({input: input, value: false});
			} else {
				button.addClass('checkboxChecked');
				input.val(true);
				this.updatePanelInputHidden({input: input, value: true});
			}
			this._visualEditor.allowSaving();
		}, this));
		
		// sliders
		this.element.find('dd > div.sliderBar').each($.proxy(function(index, item) {
			var $item = $(item);
			var value = parseInt($item.parent().find('div.sliderBarText input').val());
			var min = parseInt($item.data('sliderMin'));
			var max = parseInt($item.data('sliderMax'));
			var interval = parseInt($item.data('sliderInterval'));
			$item.slider({
				range: 'min',
				value: value,
				min: min,
				max: max,
				step: interval,
				slide: $.proxy(function( event, ui ) {
					var $target = $(event.target);
					// update visible output
					$target.siblings('div.sliderBarText').find('input.sliderValue').val(ui.value);
					
					// handle hidden input
					this.updatePanelInputHidden({input: $target.parent().find('div.sliderBarText input.sliderValue'), value: ui.value});
					this._visualEditor.allowSaving();
				}, this)
			});
		}, this));
	},
	
	/**
	 * @see	ULTIMATE.VisualEditor.Panel#showPanel
	 */
	showPanel: function() {
		var result = this._super();
		if (result == false) return false;
		// Re-select the block if a block option panel tab is open.
		this._i('#' + this._element.find('nav.tabMenu > ul li.ui-state-active a').attr('href').replace('#', '').replace('-tab', '')).addClass('block-selected block-hover');
		$.removeCookie('hideBottomPanel');
		$.cookie('hideBottomPanel', false);
		this._element.find('nav.tabMenu > ul li#minimize img').attr('src', WCF.Icon.get('wcf.icon.remove'));
		return true;
	},
	
	/**
	 * @see ULTIMATE.VisualEditor.Panel#hidePanel
	 */
	hidePanel: function() {
		var result = this._super();
		if (result === false) return false;
		// De-select the selected block while the panel is hidden
		this._i('.block-selected').removeClass('block-selected block-hover');
		$.removeCookie('hideBottomPanel');
		$.cookie('hideBottomPanel', true);
		this._element.find('nav.tabMenu > ul li#minimize img').attr('src', WCF.Icon.get('wcf.icon.add'));
		return true;
	},
	
	/**
	 * Toggles the visibility of the panel.
	 * 
	 * @return	{Boolean}
	 */
	togglePanel: function() {
		if ( this._element.hasClass('panelHidden') )
			return this.showPanel();
		return this.hidePanel();
	},
	
	/**
	 * Adds a new tab to the bottom panel.
	 * 
	 * @param	{String}	name
	 * @param	{String}	title
	 * @param	{Object}	content
	 * @param	{Boolean}	closable
	 * @param	{Boolean}	closeOnLayoutSwitch
	 * @param	{String}	panelClass
	 * @returns	{jQuery}
	 */
    addPanelTab: function(name, title, content, closable, closeOnLayoutSwitch, panelClass) {
		// If the tab name already exists, don't try making it.
		if ( $('nav#bottomPanelTabs ul li a[href="#' + name + '-tab"]').length !== 0 )
			return false;
		
		// set up default variables
		if ( typeof closable == 'undefined' ) {
			var closable = false;
		}
		
		if ( typeof closeOnLayoutSwitch == 'undefined' ) {
			var closeOnLayoutSwitch = false;
		}
		
		if ( typeof panelClass == 'undefined' ) {
			var panelClass = false;
		}
		
		// add the tab
		var tab = this.element.wcfTabs('add', '#' + name + '-tab', title);
		var panel = this.element.find('div#' +  name + '-tab');
		var tabLink = this.element.find('> nav#bottomPanelTabs ul li a[href="#' + name + '-tab"]');
		
		$(tabLink).bind('click', $.proxy(this.showPanel, this));
		
		this.showPanel();
		
		// add the panel class to the panel
		panel.addClass('panel');
		
		// If the content is static, just throw it in.  Otherwise get the content with AJAX.
		if ( typeof content == 'string' ) {
			panel.html(content);
		} else {
			var options = content || {};
			var $proxy = new WCF.Action.Proxy(options);
			$proxy.sendRequest();
		}
		if ( panelClass )
			panel.addClass('panel-' + panelClass);

		// add delete to tab link if the tab is closable
		if ( closable ) {
			tabLink.parent().append('<span class="close">X</span>');
		}
		// If the panel is set to close on layout switch, add a class to the tab itself so we can target it down the road.
		tabLink.parent().addClass('tab-close-on-layout-switch');
		return tab;
	},
	
	/**
	 * Removes a panel tab.
	 */
	removePanelTab: function(name) {
		// if tab doesn't exist, don't try to delete any tabs
		if ( $('#' + name + '-tab').length === 0 ) {
			return false;
		}
		
		return $('nav#bottomPanelTabs').wcfTabs('remove', name + '-tab');
	},
	
	/**
	 * Initializes the option menu.
	 */
	_initOptionMenu: function() {
		this._element.find('> nav.tabMenu li#options ul').css({
			top: -(this._element.find('> nav.tabMenu li#options ul').height() + 3)
		});
		var $self = this;
		var hideOptions = $.proxy(function(event) {
			if ( $(event.target).parents('li#options').length === 0 ) {
				this._element.find('> nav.tabMenu li#options ul').hide();
				this._element.find('> nav.tabMenu li#options span').removeClass('active');
				$(document).unbind('click', hideOptions);
				this._visualEditor.iframe.contents().unbind('click', hideOptions);
			}
		}, this);

		// Bind button
		this._element.find('> nav.tabMenu li#options span').bind('click', function(){

			// If it's open, close it
			if ( $(this).hasClass('active') ) {
				$(this).siblings('ul').hide();
				$(this).removeClass('active');
				$(document).unbind('click', hideOptions);
				$self._visualEditor.iframe.contents().unbind('click', hideOptions);
			} else {
				$(this).siblings('ul').show();
				$(this).addClass('active');

				$(document).bind('click', hideOptions);
				$self._visualEditor.iframe.contents().bind('click', hideOptions);
			}
		});
		
		// Make buttons in menu close menu when clicked
		this._element.find('> nav.tabMenu li#options ul li').bind('click', function(){
			var $list = $(this).parent();
			var $button = list.siblings('span');
			$list.hide();
			$button.removeClass('active');

			$(document).unbind('click', hideOptions);
			$self._visualEditor.iframe.contents().unbind('click', hideOptions);

		});

		// Bind specific options
		this._element.find('> nav.tabMenu li#options ul li#menuLinkGridWizard').bind('click', $.proxy(function(){
			this.hidePanel();
			//openBox('grid-wizard');
		}, this));
		this._element.find('> nav.tabMenu li#options ul li#menuLinkTour').bind('click', $.proxy(function(){
			//this._visualEditor.startTour();
		}, this));
		this._element.find('> nav.tabMenu li#options ul li#menuLinkLiveCSS').bind('click', function(){
			//openBox('live-css');
			
			// If Live CSS hasn't been set up then initiate CodeMirror or Tabby
			if ( typeof liveCSSInit == 'undefined' || liveCSSInit == false ) {
				// Set up CodeMirror
				if ( $self._disableCodeMirror != true ) {						
					var liveCSSEditor = CodeMirror.fromTextArea($('textarea#liveCSS')[0], {
						lineWrapping: true,
						tabMode: 'shift',
						mode: 'css',
						lineNumbers: true,
						onCursorActivity: function() {
							liveCSSEditor.setLineClass(hlLine, null);
							hlLine = liveCSSEditor.setLineClass(liveCSSEditor.getCursor().line, "activeline");
						},
						onChange: function(instance) {
							var value = instance.getValue();
							$self.updatePanelInputHidden({input: $('textarea#liveCSS'), value: value});
							$('style#liveCSSHolder').html(value);
							$self._visualEditor.allowSaving();
						},
						undoDepth: 80
					});
					liveCSSEditor.setValue($('textarea#liveCSS').val());
					var hlLine = liveCSSEditor.setLineClass(0, "activeline");
				// Set up Tabby and the text area if CodeMirror is disabled
				} else {
					$('textarea#liveCSS').tabby();
					$('textarea#liveCSS').bind('keyup', function(){
						$self.updatePanelInputHidden({input: $(this), value: $(this).val()});
						$self._i('style#liveCSSHolder').html($(this).val());
						$self._visualEditor.allowSaving();
					});

				}
				var liveCSSInit = true;
			}
		});
		this._element.find('> nav.tabMenu li#options ul li#menuLinkClearCache').bind('click', $.proxy(function(){
			// Set up parameters
			var $parameters = {}
			var $data = $.extend(true, {
				actionName: 'clearCache',
				className: 'ultimate\\data\\block\\BlockAction',
				parameters: $parameters			
			}, { });
			this._proxy.setOption('data', $data);
			this._proxy.sendRequest();
		}, this));
	},
	
	/**
	 * Updates hidden input fields for the panel inputs.
	 * 
	 * @param	{Object}	args
	 */
	updatePanelInputHidden: function(args) {
		var originalInput = null;
		var optionID = '';
		var optionGroup = '';
		var optionValue = '';
		var isBlock = 'false';
		var blockID = 0;
		var callback = function(){};
		
		if ( typeof args.input !== 'undefined' && $(args.input).length === 1 ) {
			originalInput = $(args.input);
			optionID = originalInput.attr('name').toLowerCase();
			optionGroup = originalInput.data('group').toLowerCase();
			optionValue = args.value;

			isBlock = originalInput.data('isBlock');
			blockID = originalInput.data('blockID');

			callback = eval(originalInput.data('callback'));
		} else {
			optionID = args.id.toLowerCase();
			optionGroup = (typeof args.group != 'undefined') ? args.group.toLowerCase() : false;
			optionValue = args.value;
			isBlock = args.isBlock;
			blockID = args.blockID;
			callback = (typeof args.callback === 'function') ? args.callback : false;	
		}
			
		// prepare the name and class for the input(s)
		var hiddenInputClass = '';
		var hiddenInputName = '';
		if ( isBlock == 'true' ) {
			hiddenInputClass = 'input-' + blockID + '-' + optionID + '-hidden';
			hiddenInputName = 'blocks[' + blockID + '][settings][' + optionID + ']';
		} else {
			hiddenInputClass = 'input-' + optionGroup + '-' + optionID + '-hidden';
			hiddenInputName = 'options[' + optionGroup + '][' + optionID + ']';
		}
		
		// Remove the existing inputs to keep it simple.
		$('div#hiddenInputs').find('input.' + hiddenInputClass).remove();
					
		// If the value is anything but an object, then one input will do.
		if ( typeof optionValue != 'object' ) {
			$('<input type="hidden" class="' + hiddenInputClass + '" name="' + hiddenInputName + '" />')
				.val(optionValue)
				.appendTo('div#hiddenInputs');

		// If the value is an object/array, then create multiple hidden inputs.	
		} else {
			if ( optionValue !== null ) {
				$.each(optionValue, function(index, propertyValue) {
					$('<input type="hidden" class="' + hiddenInputClass + '" name="' + hiddenInputName + '[]" />')
						.val(propertyValue)
						.data('arrayInput', 'true')
						.appendTo('div#hiddenInputs');
				});
			} else {
				$('<input type="hidden" class="' + hiddenInputClass + '" name="' + hiddenInputName + '" />')
					.val('')
					.appendTo('div#hiddenInputs');
			}
		}
		
		// Retrieve the hidden inputs again so they can be manipulated.
		var hiddenInputs = $('input.' + hiddenInputClass);
						
		// If it's a block hidden input, add option ID and block IDs for updating block content next.
		if ( isBlock == 'true' ) {
			hiddenInputs.attr('data-option', optionID);
			hiddenInputs.attr('data-block-id', blockID);
		}
			
 		// if it's a block input then update the block content then run the callback
		if ( isBlock == 'true' ) {
			
			// flood control
			if ( typeof this.updateBlockContentFloodTimeout != 'undefined' )
				return;

			var blockElement = this._i('.block[data-id="' + blockID + '"]');
			var newBlockSettings = {};
			
			$('div#hiddenInputs').find('input[data-block-id="' + blockID + '"]').each(function() {

				// Handle regular inputs.
				if ( typeof $(this).data('arrayInput') == 'undefined' || $(this).data('arrayInput') != 'true' ) {
					newBlockSettings[$(this).data('option')] = $(this).val();
				// Multi-selects and multi-image inputs.	
				} else {
					if ( typeof newBlockSettings[$(this).data('option')] == 'undefined' )
						newBlockSettings[$(this).data('option')] = [];
					newBlockSettings[$(this).data('option')].push($(this).val());
				}
			});
			
			// update the block content
			this._visualEditor.loadBlockContent({
				blockElement: blockElement,
				blockSettings: {
					settings: newBlockSettings,
					dimensions: ULTIMATE.Block.getBlockDimensions(blockElement),
					position: ULTIMATE.Block.getBlockPosition(blockElement)
				},
				blockOrigin: {
					blockID: blockID,
					blockType: ULTIMATE.Block.getBlockType(blockElement)
				},
				blockDefault: {
					type: ULTIMATE.Block.getBlockType(blockElement),
					id: 0
				},
				callback: callback,
				callbackArgs: args
			});
			
			this.updateBlockContentFloodTimeout = setTimeout(function() {
				delete this.updateBlockContentFloodTimeout;
			}, 500);
			
		// Else if it's not a block input (just a regular panel input), then run the callback right away.
		} else {
			if ( typeof callback == 'function' )
				callback(args);		
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
		this._notification.show();
	},
	
	/**
	 * @see	ULTIMATE.VisualEditor.Panel#resizePanel
	 */
	resizePanel: function(panelHeight, resizingWindow) {
		var panelHeight = this._super(panelHeight, resizingWindow);
		
		var iframeBottomPadding = this._element.hasClass('panelHidden') ? (this._element.find('> nav.tabMenu > ul').outerHeight() + 41) : (this._element.outerHeight() + 41);
		var layoutSelectorBottomPadding = this._element.hasClass('panelHidden') ? this._element.find('> nav.tabMenu > ul').outerHeight()  + $('#layoutSelectorTabs').height() : this._element.outerHeight() + $('#layoutSelectorTabs').height();
		this._visualEditor.iframe.css({paddingBottom: iframeBottomPadding});
		$('#layoutSelectorOffset').css({paddingBottom: layoutSelectorBottomPadding});
		$.removeCookie('bottomPanelHeight');
		return $.cookie('bottomPanelHeight', panelHeight);
	}
});

// grid widget
/**
 * ULTIMATE CMS Grid
 * @since	version 1.0.0
 */
(function ($, undefined) {
	$.widget('ui.grid', $.ui.mouse, {
		options: {
			columns: null,
			columnWidth: null,
			gutterWidth: null,
			yGridInterval: 10,
			minBlockHeight: 40,
			selectedBlocksContainerClass: 'selectedBlocksContainer',
			defaultBlockClass: 'block',
			defaultBlockContentClass: 'block-content',
			iframe: $('iframe.content')
		},
		
		/**
		 * Contains the container.
		 * @type	jQuery
		 */
		container: null,
		
		/**
		 * Contains the contents of the element this widget was created on.
		 * @type	jQuery
		 */
		contents: null,
		
		/**
		 * True if an element is focused.
		 * @type	Boolean
		 */
		focused: false,
		
		/**
		 * True if an element is dragged.
		 * @type	Boolean
		 */
		dragged: false,
		
		/**
		 * Contains the helper element.
		 * @type	jQuery
		 */
		helper: null,
		
		/**
		 * Contains the offset of the container.
		 * @type	Integer
		 */
		offset: 0,
		
		/**
		 * Contains the top offset at the beginning of a drag operation.
		 * @type	Integer
		 */
		beginTop: 0,
		
		/**
		 * Contains the left offset at the beginning of a drag operation.
		 * @type	Integer
		 */
		beginLeft: 0,
		
		/**
		 * Contains the left offsets of all dragged blocks in mass block selection mode.
		 * @type	Array
		 */
		posLeftArray: [],
		
		/**
		 * Contains the top offsets of all dragged blocks in mass block selection mode.
		 * @type	Array
		 */
		posTopArray: [],
		
		/**
		 * Contains a blank block.
		 * @type	jQuery
		 */
		blankBlock: null,
		
		/**
		 * Returns a child element of the iframe that fits to the selector.
		 * 
		 * @param	{String}	element
		 * @returns	{jQuery}
		 */
		_i: function (element) {
			return this.options.iframe.contents().find(element);
		},
		
		/**
		 * Creates the ULTIMATE CMS grid.
		 */
		_create: function () {
			if (!this.options.columns || !this.options.columnWidth || this.options.gutterWidth === null) {
				return console.error('The grid widget was not supplied with all of the required arguments.', this.element, this.options)
			}
			this.container = $(this.element).contents().find(this.options.container);
			this.contents = $(this.element).contents();
			this.helper = $('<div class="ui-grid-helper block"></div>');
			this.offset = this.container.offset();
			this.container.addClass('ui-grid');
			this.container.disableSelection();
			this._initResizable(this.container.children('.' + this.options.defaultBlockClass.replace('.', '')));
			this._initDraggable(this.container.children('.' + this.options.defaultBlockClass.replace('.', '')));
			this._bindDoubleClick();
			this._bindIFrameMouse();
		},
		
		/**
		 * Destroys the grid functionality.
		 * 
		 * @returns	{Object}
		 */
		destroy: function () {
			this.element.removeClass('ui-grid ui-grid-disabled').removeData('grid').unbind('.grid');
			this._mouseDestroy();
			this.contents.unbind('mousedown', this._iFrameMouseDown);
			this.contents.unbind('mouseup', this._iFrameMouseUp);
			this.contents.unbind('mousemove', this._iFrameMouseMove);
			this.element.unbind('mouseleave', this._iFrameMouseUp);
			$.Widget.prototype.destroy.apply(this, arguments);
			return this;
		},
		
		/**
		 * Returns an element of the iFrame. 
		 * 
		 * @param	{String}	selector
		 * @returns	{jQuery}
		 */
		iframeElement: function (selector) {
			return $(this.element).contents().find(selector);
		},
		
		/**
		 * Resets both the draggable and resizable functionality.
		 */
		resetDraggableResizable: function () {
			this._initResizable(this.container.children('.' + this.options.defaultBlockClass.replace('.', '')));
			this._initDraggable(this.container.children('.' + this.options.defaultBlockClass.replace('.', '')));
		},
		
		/**
		 * Binds mouse events for the iFrame.
		 */
		_bindIFrameMouse: function () {
			this.contents.bind('mousedown', $.proxy(this._iFrameMouseDown, this));
			this.contents.bind('mouseup', $.proxy(this._iFrameMouseUp, this));
			this.element.bind('mouseleave', $.proxy(this._iFrameMouseUp, this));
		},
		
		/**
		 * Called each time a mousedown event triggers inside the grid.
		 * 
		 * @param	{jQuery.Event}	event
		 */
		_iFrameMouseDown: function (event) {
			if (event.which !== 1) {
				return false;
			}
			this.element.focus();
			this.mouseEventDown = event;
			this.mouseEventElement = $(this.mouseEventDown.originalEvent.target);
			if (typeof this.bindMouseMove === 'undefined') {
				this.contents.mousemove(this._iFrameMouseMove);
				this.bindMouseMove = true;
			}
			if (this.mouseEventElement.hasClass('ui-resizable-handle')) {
				ULTIMATE.Block.getBlock(this.mouseEventElement).data('resizable')._mouseDown(event);
			} else {
				if (ULTIMATE.Block.getBlock(this.mouseEventElement) && ULTIMATE.Block.getBlock(this.mouseEventElement).hasClass(this.options.defaultBlockClass.replace('.', ''))) {
					if (ULTIMATE.Block.getBlock(this.mouseEventElement).data('draggable')) {
						ULTIMATE.Block.getBlock(this.mouseEventElement).data('draggable')._mouseDown(event);
					}
				} else {
					if (this.element.data('grid') && (this.mouseEventElement[0] == this.container[0] || this.mouseEventElement[0] == this.container.parents('div.wrapper')[0])) {
						this.element.data('grid')._mouseDown(event);
					}
				}
			}
		},
		
		/**
		 * Called each time a mousemove event triggers inside the grid.
		 * 
		 * @param	{jQuery.Event}	event
		 */
		_iFrameMouseMove: function (event) {
			if (typeof this.mouseEventDown !== 'undefined') {
				if (this.mouseEventElement.hasClass('ui-resizable-handle')) {
					ULTIMATE.Block.getBlock(this.mouseEventElement).data('resizable')._mouseMove(event);
				} else {
					if (getBlock(this.mouseEventElement) && getBlock(this.mouseEventElement).hasClass(this.options.defaultBlockClass.replace('.', ''))) {
						if (ULTIMATE.Block.getBlock(this.mouseEventElement).data('draggable')) {
							ULTIMATE.Block.getBlock(this.mouseEventElement).data('draggable')._mouseMove(event);
						}
					} else {
						if (this.element.data('grid') && (this.mouseEventElement[0] == this.container[0] || this.mouseEventElement[0] == this.container.parents('div.wrapper')[0])) {
							this.element.data('grid')._mouseMove(event);
						}
					}
				}
			} else {
				if (typeof this.doingHoverBlockToTop == 'undefined') {
					this.doingHoverBlockToTop = true;
					setTimeout(function () {
						var $blocks = [];
						var $pageX = c.pageX;
						var $pageY = c.pageY;
						this._i('.block').each(function () {
							var $offsetLeft = $(this).offset().left;
							var $offsetTop = $(this).offset().top;
							var $width = $offsetLeft + $(this).width();
							var $height = $offsetTop + $(this).height();
							if ($pageX < $offsetLeft || $pageX > $width) {
								return;
							}
							if ($pageY < $offsetTop || $pageY > $height) {
								return;
							}
							$blocks.push($(this));
						});
						$blocks.sort(function (h, g) {
							if (g.width() * g.height() > h.width() * h.height()) {
								return 1;
							}
							return 0;
						});
						this.sendBlockToTop($($blocks.pop()));
						delete this.doingHoverBlockToTop;
					}, 50)
				}
			}
		},
		
		/**
		 * Called each time a mouseup event triggers inside the grid.
		 * 
		 * @param	{jQuery.Event}	event
		 */
		_iFrameMouseUp: function (event) {
			if (typeof this.mouseEventDown !== 'undefined') {
				var block = ULTIMATE.Block.getBlock(this.mouseEventElement);
				if (block && typeof block.data('resizable') != 'undefined') {
					block.data('resizable')._mouseUp(event);
				}
				if (block && typeof block.data('draggable') != 'undefined') {
					block.data('draggable')._mouseUp(event);
				}
				if (typeof block != 'undefined' && typeof block.data('grid') != 'undefined') {
					block.data('grid')._mouseUp(event);
				}
				delete this.mouseEventDown;
			}
		},
		
		/**
		 * Called each time a mousestart event occurs.
		 * 
		 * @param	{jQuery.Event}	event
		 */
		_mouseStart: function (event) {
			if (!event || this.container.hasClass('grouping-active')) {
				return;
			}
			this.mouseStartPosition = [event.pageX - this.container.offset().left, event.pageY - this.container.offset().top];
			this._trigger('start', event);
			$(this.container).append(this.helper);
			this.helper.css({
				width: this.options.columnWidth,
				height: 0,
				top: 0,
				left: 0,
				display: 'none'
			});
			return true;
		},
		
		/**
		 * Called each time a mousedrag event occurs.
		 * 
		 * @param	{jQuery.Event}	event
		 */
		_mouseDrag: function (event) {
			if (!event || $grid.container.hasClass('grouping-active')) {
				return;
			}
			this.dragged = true;
			var leftMousePosition = this.mouseStartPosition[0];
			var topMousePosition = this.mouseStartPosition[1];
			var offsetLeft = event.pageX - $(this.container).offset().left;
			var offsetTop = event.pageY - $(this.container).offset().top;
			if (leftMousePosition > offsetLeft) {
				var tmpLeft = offsetLeft;
				offsetLeft = leftMousePosition;
				leftMousePosition = tmpLeft;
			}
			if (topMousePosition > offsetTop) {
				var tmpTop = offsetTop;
				offsetTop = topMousePosition;
				topMousePosition = tmpTop;
			}
			var offsetLeftNew = $(this.container).offset().left;
			var offsetTopNew = $(this.container).offset().top;
			var height = $(this.container).height();
			var width = $(this.container).width();
			if (offsetLeft >= width && mouseLeftPosition >= width) {
				return;
			}
			if (offsetTop >= height && mouseTopPosition >= height) {
				return;
			}
			if (mouseLeftPosition < 0) {
				mouseLeftPosition = 0;
			}
			if (mouseTopPosition < 0) {
				mouseTopPosition = 0;
			}
			if (offsetTop > height) {
				offsetTop = height;
			}
			var leftBlock = mouseLeftPosition.toNearest(this.options.columnWidth + this.options.gutterWidth);
			var topBlock = mouseTopPosition.toNearest(this.options.yGridInterval);
			var widthBlock = offsetLeft.toNearest(this.options.columnWidth + this.options.gutterWidth) - leftBlock - this.options.gutterWidth;
			var heightBlock = offsetTop.toNearest(this.options.yGridInterval) - mouseTopPosition.toNearest(this.options.yGridInterval);
			var css = {
				display: 'block',
				left: leftBlock,
				top: topBlock,
				width: widthBlock,
				height: heightBlock
			};
			if (leftBlock + widthBlock > (this.options.columns * (this.options.columnWidth + this.options.gutterWidth))) {
				css.width = width - css.left;
			}
			if (event.pageY > (offsetTopNew + height)) {
				css.height = height - topBlock;
			}
			this.helper.css(css);
			if (css.height < this.options.minBlockHeight) {
				this.helper.addClass('block-error');
			} else {
				if (this.helper.hasClass('block-error')) {
					this.helper.removeClass('block-error');
				}
			}
			this._trigger('drag', event);
			return false;
		},
		
		/**
		 * Called each time a mousetop event occurs.
		 * 
		 * @param	{jQuery.Event}	event
		 */
		_mouseStop: function (event) {
			if (!event || this.container.hasClass('grouping-active')) {
				return;
			}
			this.dragged = false;
			this._trigger('stop', event);
			var css = {
				width: this.helper.width(),
				height: this.helper.height(),
				top: this.helper.position().top,
				left: this.helper.position().left
			};
			this.helper.remove();
			if (css.width < this.options.columnWidth || css.height < this.options.minBlockHeight) {
				return false;
			}
			if (css.left + css.width > this.options.columns * (this.options.columnWidth + this.options.gutterWidth) + 20) {
				var overlap = (css.left + css.width) - (this.options.columns * (this.options.columnWidth + this.options.gutterWidth) - 20);
				css.width = css.width - overlap;
			}
			if (css.width < this.options.columnWidth) {
				css.width = this.options.columnWidth;
			}
			this.addBlankBlock(css);
			this.mouseStartPosition = false;
			return false;
		},
		
		/**
		 * Called each time a mouseup event occurs.
		 * 
		 * @param	{jQuery.Event}	event
		 */
		_mouseUp: function (event) {
			if (!event || this.container.hasClass('grouping-active')) {
				return;
			}
			$(document).unbind('mousemove.' + this.widgetName, this._mouseMoveDelegate).unbind('mouseup.' + this.widgetName, this._mouseUpDelegate);
			if (this._mouseStarted) {
				this._mouseStarted = false;
				if (event.target == this._mouseDownEvent.target) {
					$.data(event.target, this.widgetName + '.preventClickEvent', true);
				}
				this._mouseStop(event);
			}
			return false;
		},
		
		/**
		 * Initializes a resizable
		 * 
		 * @param	{jQuery}	element
		 */
		_initResizable: function (element) {
			if (typeof element == 'string') {
				element = $(element);
			}
			if (typeof element.resizable === 'function') {
				element.resizable('destroy');
			}
			element.resizable({
				handles: 'n, e, s, w, ne, se, sw, nw',
				grid: [this.options.columnWidth + this.options.gutterWidth, this.options.yGridInterval],
				containment: this.container,
				minHeight: this.options.minBlockHeight,
				maxWidth: this.options.columns * (this.options.columnWidth + this.options.gutterWidth),
				start: $.proxy(this._resizableStart, this),
				resize: $.proxy(this._resizableResize, this),
				stop: $.proxy(this._resizableStop, this)
			});
		},
		
		/**
		 * Called each time a resize process starts.
		 * 
		 * @param	{jQuery.Event}	event
		 * @param	{Object}		ui
		 */
		_resizableStart: function (event, ui) {
			var block = getBlock(ui.element);
			var minHeight = parseInt(block.css('minHeight').replace('px', ''));
			var height = block.height();
			if (minHeight <= height) {
				block.css('minHeight', 0);
			}
			block.addClass('block-hover');
			block.qtip('option', 'hide.delay', 10000);
			block.qtip('show');
			block.qtip('reposition');
		},
		
		/**
		 * Called each time a resizable is resized.
		 * 
		 * @param	{jQuery.Event}	event
		 * @param	{Object}		ui
		 */
		_resizableResize: function (event, ui) {
			var block = getBlock(ui.element);
			block.qtip('show');
			block.qtip('reposition');
		},
		
		/**
		 * Called each time a resize process stops.
		 * 
		 * @param	{jQuery.Event}	event
		 * @param	{Object}		ui
		 */
		_resizableStop: function (event, ui) {
			var block = ULTIMATE.Block.getBlock(ui.element);
			var gridWidth = Math.ceil(block.width() / (this.options.columnWidth + this.options.gutterWidth));
			var gridLeft = Math.ceil(block.position().left / (this.options.columnWidth + this.options.gutterWidth));
			var oldGridWidth = ULTIMATE.Block.getBlockGridWidth(block);
			var oldGridLeft = ULTIMATE.Block.getBlockGridLeft(block);
			block.removeClass('grid-width-' + oldGridWidth);
			block.addClass('grid-width-' + gridWidth);
			block.removeClass('grid-left-' + oldGridLeft);
			block.addClass('grid-left-' + gridLeft);
			block.data({
				'gridLeft': gridLeft,
				'gridTop': ULTIMATE.Block.getBlockPositionPixels(block).top,
				'width': gridWidth,
				'height': ULTIMATE.Block.getBlockDimensionsPixels(block).height
			});
			block.css('width', '');
			block.css('left', '');
			ULTIMATE.Block.updateBlockDimensionsHidden(ULTIMATE.Block.getBlockID(block), ULTIMATE.Block.getBlockDimensions(block));
			ULTIMATE.Block.updateBlockPositionHidden(ULTIMATE.Block.getBlockID(block), ULTIMATE.Block.getBlockPosition(block));
			
			ULTIMATE.VisualEditor.getVisualEditor().allowSaving();
			new WCF.Effect.BalloonTooltip();
			block.removeClass('block-hover');
		},
		
		/**
		 * Initializes a draggable.
		 * 
		 * @param	{jQuery}	element
		 */
		_initDraggable: function (element) {
			if (typeof element == 'string') {
				element = $(element);
			}
			if (typeof element.draggable === 'function') {
				element.draggable('destroy');
			}
			element.css('cursor', 'move').draggable({
				grid: [this.options.columnWidth + this.options.gutterWidth, this.options.yGridInterval],
				containment: this.iframeElement(this.options.container),
				scrollSpeed: 40,
				start: $.proxy(this._draggableStart, this),
				stop: $.proxy(this._draggableStop, this),
				drag: $.proxy(this._draggableDrag, this)
			});
		},
		
		/**
		 * Called each time a drag process starts.
		 * 
		 * @param	{jQuery.Event}	event
		 * @param	{Object}		ui
		 */
		_draggableStart: function (event, ui) {
			var $target = $(event.target);
			if ($(event.originalEvent.target).parents('.block-controls').length === 1 || $(event.originalEvent.target).parents('.block-info').length === 1) {
				$target.draggable('stop');
				return false;
			}
			$target.data('dragging', true);
			if ($target.hasClass('grouped-block')) {
				this.container.find('.grouped-block').each($.proxy(function (index, item) {
					var $item = $(item);
					if (event.srcElement == $item) {
						return;
					}
					this.posTopArray[index] = parseInt($item.css('top').replace('px', '')) || 0;
					this.posLeftArray[index] = parseInt($item.css('left').replace('px', '')) || 0;
				}, this));
				this.sendBlockToTop(this.container.find('.grouped-block'));
			} else {
				this.container.removeClass('grouping-active');
				this.container.find('.grouped-block').removeClass('grouped-block');
			}
			this.beginTop = $target.offset().top;
			this.beginLeft = $target.offset().left;
		},
		
		/**
		 * Called each time a draggable is dragged.
		 * 
		 * @param	{jQuery.Event}	event
		 * @param e
		 */
		_draggableDrag: function (event, e) {
			var $target = $(event.target);
			var changeTop = $target.offset().top - this.beginTop;
			var changeLeft = $target.offset().left - this.beginLeft;
			if ($target.hasClass('grouped-block')) {
				this.container.find('.grouped-block').each($.proxy(function (index, item) {
					if (event.srcElement == $(item)) {
						return;
					}
					$(item).css('top', this.posTopArray[index] + changeTop);
					$(item).css('left', this.posLeftArray[index] + changeLeft);
				}, this));
			} else {
				this.container.find('.grouped-block').removeClass('grouped-block');
			}
		},
		
		/**
		 * Called each time a drag process stops.
		 * 
		 * @param	{jQuery.Event}	event
		 * @param	{Object}		ui
		 */
		_draggableStop: function (event, ui) {
			var $target = $(event.target);
			$target.data('dragging', false);
			var blocks = null;
			if (this.container.find('.grouped-block').length) {
				blocks = this.container.find('.grouped-block');
			} else {
				blocks = ULTIMATE.Block.getBlock(ui.helper);
			}
			blocks.each(function (index, item) {
				var $item = $(item);
				var gridOffsetLeft = Math.ceil($item.position().left / (this.options.columnWidth + this.options.gutterWidth));
				var oldGridOffsetLeft = ULTIMATE.Block.getBlockGridLeft($item);
				$item.removeClass('grid-left-' + oldGridOffsetLeft);
				$item.addClass('grid-left-' + gridOffsetLeft);
				$item.data({
					'gridLeft': gridOffsetLeft,
					'gridTop': ULTIMATE.Block.getBlockPositionPixels($item).top
				});
				$item.css('left', '');
				ULTIMATE.Block.updateBlockPositionHidden(ULTIMATE.Block.getBlockID($item), ULTIMATE.Block.getBlockPosition($item));
				ULTIMATE.VisualEditor.getVisualEditor().allowSaving();
			});
			$(document).focus();
			$target.data('hoverWaitTimeout', setTimeout(function () {
				new WCF.Effect.BalloonTooltip();
			}, 300));
		},
		
		/**
		 * Binds the double click.
		 */
		_bindDoubleClick: function () {
			$grid = this;
			this.container.delegate('.' + this.options.defaultBlockClass.replace('.', ''), 'dblclick', $.proxy(function (event) {
				var $target = $(event.target);
				if ($(event.target).parents('.block-info').length == 1 || $(event.target).parents('.block-controls').length == 1) {
					return false;
				}
				if ($target.hasClass('grouped-block') && this.container.find('.grouped-block').length === 1) {
					$target.removeClass('grouped-block');
					this.container.removeClass('grouping-active');
				} else {
					if ($target.hasClass('grouped-block')) {
						$target.removeClass('grouped-block');
					} else {
						$target.addClass('grouped-block');
						this.container.addClass('grouping-active');
						
						var $notification = new WCF.System.Notification(WCF.Language.get('ultimate.visualEditor.massBlockSelectionMode'), 'info');
						$notification.show($.proxy(function () {
							this._i('.grouped-block').removeClass('grouped-block');
							this.options.iframe.data('grid').container.removeClass('grouping-active');
						}, this));
					}
				}
			}, this));
		},
		
		/**
		 * Adds a blank block.
		 * 
		 * @param	{Object}	data
		 * @param	{Boolean}	f
		 * @param	{Boolean}	isOldBlock
		 * @returns	{jQuery}
		 */
		addBlankBlock: function (data, f, g) {
			var defaultData = {
				top: 0,
				left: 0,
				width: 140,
				height: this.options.minBlockHeight,
				id: null
			};
			var data = $.extend(true, defaultData, data);
			if (typeof f == 'undefined') {
				var f = true;
			}
			if (typeof newBlock == 'undefined') {
				var isOldBlock = false;
			}
			var id = (data.id == false || data.id == null) ? ULTIMATE.Block.getAvailableBlockID() : data.id;
			if (typeof id === 'undefined' || !id) {
				return false;
			}
			var blankBlockHTML = '<div>';
			blankBlockHTML += '<div class="block-content-fade block-content"></div>';
			blankBlockHTML += '<h3 class="block-type" style="display: none;">';
			blankBlockHTML += '<span></span></h3>';
			blankBlockHTML += '</div>';
			var $blankBlock = $(blankBlockHTML)
				.data('id', id)
				.attr('id', 'block-' + id)
				.addClass(this.options.defaultBlockClass.replace('.', ''));
			
			var idTooltip = WCF.Language.get('ultimate.visualEditor.block.idTooltip');
			var changeBlockTypeTooltip = WCF.Language.get('ultimate.visualEditor.block.changeBlockTypeTooltip');
			var optionsTooltip = WCF.Language.get('ultimate.visualEditor.block.optionsTooltip');
			var deleteTooltip = WCF.Language.get('ultimate.visualEditor.block.delete');
			
			$blankBlock.addClass('blank-block').addClass('jsBlockItem');
			var innerHTML = '<div class="block-info">';
			innerHTML += '<span class="id jsTooltip" title="' + idTooltip + '">' + id + '</span>';
			innerHTML += '<span class="type type-unknown jsTooltip" title="' + changeBlockTypeTooltip + '">' + WCF.Language.get('ultimate.visualEditor.block.typeUnknown') +'</span>';
			innerHTML += '</div>';
			$blankBlock.append(innerHTML);
			
			innerHTML = '<div class="block-controls">';
			innerHTML += '<span class="options jsTooltip" title="' + optionsTooltip + '">' + WCF.Language.get('ultimate.visualEditor.block.options') + '</span>';
			innerHTML += '<img src="' 
				+ WCF.Icon.get('wcf.icon.delete') 
				+ '" alt="" class="jsDeleteButton jsTooltip icon16" title="' 
				+ deleteTooltip 
				+ '" data-object-id="'
				+ id 
				+ '" data-confirm-message="' 
				+ WCF.Language.get('ultimate.visualEditor.block.delete.sure')
				+ '"/>';
			innerHTML += '</div>';
			$blankBlock.append(innerHTML);
			
			var newBlock = $blankBlock;
			newBlock.css({
				width: parseInt(data.width),
				height: parseInt(data.height),
				top: parseInt(data.top),
				left: parseInt(data.left),
				position: 'absolute',
				visibility: 'hidden'
			});
			newBlock.appendTo(this.container);
			var gridWidth = 0;
			var gridLeft = 0;
			if (f) {
				var width = String(newBlock.width()).replace('px', '');
				gridWidth = Math.ceil(width / (this.options.columnWidth + this.options.gutterWidth));
				var left = String(newBlock.position().left).replace('px', '');
				gridLeft = Math.ceil(left / (this.options.columnWidth + this.options.gutterWidth))
			} else {
				gridWidth = parseInt(data.width);
				gridLeft = parseInt(data.left)
			}
			newBlock.data({
				'width': gridWidth,
				'height': parseInt(data.height),
				'gridTtop': parseInt(data.top),
				'gridLeft': gridLeft
			});
			newBlock.css('width', '').addClass('grid-width-' + gridWidth);
			newBlock.css('left', '').addClass('grid-left-' + gridLeft);
			newBlock.css('visibility', 'visible');
			this._initResizable(newBlock);
			this._initDraggable(newBlock);
			blockIntersectCheck(newBlock);
			if (isOldBlock == false) {
				new WCF.Effect.BalloonTooltip();
				ULTIMATE.VisualEditor.getVisualEditor().showBlockTypePopup($($blankBlock));
			}
			this.blankBlock = $blankBlock;
			return newBlock;
		},
		
		/**
		 * Configure a blank block.
		 * 
		 * @param	{String}	blockType
		 * @param	{Boolean}	showBlockTypePopup
		 * @returns	{jQuery}
		 */
		setupBlankBlock: function (blockType, showBlockTypePopup) {
			if (typeof isNewBlock == 'undefined') {
				var showBlockTypePopup = false
			}
			this.blankBlock.removeClass('blank-block');
			this.blankBlock.addClass('block-type-' + blockType);
			this.blankBlock.find('.block-info span.type').attr('class', '').addClass('type').addClass('type-' + blockType).html(ULTIMATE.Block.getBlockTypeNice(blockType));
			ULTIMATE.VisualEditor.getVisualEditor().loadBlockContent({
				blockElement: this.blankBlock,
				blockOrigin: {
					blockType: blockType,
					id: 0,
					layout: ULTIMATE.VisualEditor.getVisualEditor().currentLayout
				},
				blockSettings: {
					dimensions: ULTIMATE.Block.getBlockDimensions(this.blankBlock),
					position: ULTIMATE.Block.getBlockPosition(this.blankBlock)
				},
			});
			if (ULTIMATE.Block.getBlockTypeObject(blockType)['fixedHeight'] === true) {
				this.blankBlock.addClass('block-fixed-height');
			} else {
				this.blankBlock.addClass('block-fluid-height');
			}
			this.blankBlock.find('h3.block-type span').text(ULTIMATE.Block.getBlockTypeNice(blockType));
			this.blankBlock.find('h3.block-type').show();
			if (isNewBlock == false) {
				ULTIMATE.VisualEditor.getVisualEditor().hideBlockTypePopup();
			}
			ULTIMATE.VisualEditor.getVisualEditor()._addNewBlockHidden(ULTIMATE.Block.getBlockID(this.blankBlock), ULTIMATE.Block.getBlockType(this.blankBlock));
			ULTIMATE.Block.updateBlockPositionHidden(ULTIMATE.Block.getBlockID(this.blankBlock), ULTIMATE.Block.getBlockPosition(this.blankBlock));
			ULTIMATE.Block.updateBlockDimensionsHidden(ULTIMATE.Block.getBlockID(this.blankBlock), ULTIMATE.Block.getBlockDimensions(this.blankBlock));
			
			ULTIMATE.VisualEditor.getVisualEditor().allowSaving();
			var block = this.blankBlock;
			delete this.blankBlock;
			delete this.blankBlockOptions;
			if (showBlockTypePopup == false) {
				new WCF.Effect.BalloonTooltip();
			}
			new WCF.Action.Delete('ultimate\\data\\block\\BlockAction', '.jsBlockItem');
			return block;
		},
		
		/**
		 * Adds a new block with the given data.
		 * 
		 * @param	{Object}	data
		 */
		addBlock: function (data) {
			var defaultData = {
				top: 0,
				left: 0,
				width: 1,
				height: this.options.minBlockHeight,
				type: null,
				id: null,
				settings: []
			};
			var data = $.extend(true, defaultData, data);
			if (this.addBlankBlock(data, false, true)) {
				var block = this.setupBlankBlock(data.type, true);
				var id = ULTIMATE.Block.getBlockID(block);
				$.each(data.settings, $.proxy(function (index, item) {
					var $item = $(item);
					ULTIMATE.Block.updatePanelInputHidden({
						id: index,
						value: item,
						group: 'general',
						isBlock: 'true',
						blockID: id
					});
				}, this));
			} else {
				return false;
			}
		},
		
		/**
		 * Sends the given block to the top.
		 * 
		 * @param	{jQuery}	element
		 */
		sendBlockToTop: function (element) {
			if (typeof element == 'string') {
				var element = ULTIMATE.Block.getBlock(element);
			}
			if (!element || !element.length) {
				return;
			}
			this._i('.block').css('zIndex', 1);
			element.css('zIndex', 2);
		},
		
		/**
		 * Removes all layout switch panels.
		 */
		removeLayoutSwitchPanels: function() {
			$('li.tab-close-on-layout-switch').each(function(){
				var id = $(this).find('a').attr('href').replace('#', '');
				this.element.wcfTabs('remove', id);
			});
		}
	});
	$.extend($.ui.grid, {
		version: '1.0.0'
	})
})(jQuery);

/**
 * Simple rounding function.
 * 
 * @param	{Number}	num
 * @returns {Number}
 */
Number.prototype.toNearest = function(num){
	return Math.round(this / num) * num;
}

/**
 * Changes the document title.
 * 
 * @param	{String}	newTitle
 */
function changeTitle(newTitle) {
	var oldTitle = $('title').text();
	var pageURLPart = oldTitle.split(' - ')[1];
	newTitle += ' - ' + pageURLPart;
	$('title').text(newTitle);
	console.debug('oldTitle: ' + oldTitle + ', newTitle: ' + newTitle);
};


/* Simple JavaScript Inheritance
 * By John Resig http://ejohn.org/
 * MIT Licensed.
 */
// Inspired by base2 and Prototype
(function(){
  var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;

  // The base Class implementation (does nothing)
  this.Class = function(){};
 
  // Create a new Class that inherits from this class
  Class.extend = function(prop) {
    var _super = this.prototype;
   
    // Instantiate a base class (but only create the instance,
    // don't run the init constructor)
    initializing = true;
    var prototype = new this();
    initializing = false;
   
    // Copy the properties over onto the new prototype
    for (var name in prop) {
      // Check if we're overwriting an existing function
      prototype[name] = typeof prop[name] == "function" &&
        typeof _super[name] == "function" && fnTest.test(prop[name]) ?
        (function(name, fn){
          return function() {
            var tmp = this._super;
           
            // Add a new ._super() method that is the same method
            // but on the super-class
            this._super = _super[name];
           
            // The method only need to be bound temporarily, so we
            // remove it when we're done executing
            var ret = fn.apply(this, arguments);       
            this._super = tmp;
           
            return ret;
          };
        })(name, prop[name]) :
        prop[name];
    }
   
    // The dummy class constructor
    function Class() {
      // All construction is actually done in the init method
      if ( !initializing && this.init )
        this.init.apply(this, arguments);
    }
   
    // Populate our constructed prototype object
    Class.prototype = prototype;
   
    // Enforce the constructor to be what we expect
    Class.prototype.constructor = Class;

    // And make this class extendable
    Class.extend = arguments.callee;
   
    return Class;
  };
})();

