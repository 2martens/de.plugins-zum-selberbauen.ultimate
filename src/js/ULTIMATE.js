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

/**
 * Global permission storage.
 * 
 * @see	WCF.Dictionary
 */
ULTIMATE.Permission = {
	_variables: new WCF.Dictionary(),
	
	/**
	 * @param string  key
	 * @param boolean value
	 * @see	WCF.Dictionary.add()
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
	 * @param	string		key
	 * @return	boolean
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
 */
ULTIMATE.Menu = {};

/**
 * Namespace for ULTIMATE.Menu.Item
 */
ULTIMATE.Menu.Item = {};

/**
 * Adds menu items to a menu item list.
 * 
 * @param string  elementID
 * @param string  menuItemListID
 * @param string  className
 * @param integer offset
 * @param string  type
 */
ULTIMATE.Menu.Item.Transfer = function(elementID, menuItemListID, className, offset, type) { this.init(elementID, menuItemListID, className, offset, type); };
ULTIMATE.Menu.Item.Transfer.prototype = {
	
	/**
	 * Contains the element from which the items should be transferred.
	 * @var jQuery
	 */
	_element: null,
	
	/**
	 * menu item list id
	 * @var string
	 */
	_menuItemListID: '',
	
	/**
	 * action class name
	 * @var	string
	 */
	_className: '',
	
	/**
	 * notification object
	 * @var	WCF.System.Notification
	 */
	_notification: null,
	
	/**
	 * show order offset
	 * @var	integer
	 */
	_offset: 0,
	
	/**
	 * proxy object
	 * @var	WCF.Action.Proxy
	 */
	_proxy: null,
	
	/**
	 * object structure
	 * @var	object
	 */
	_structure: { },
	
	/**
	 * type of IDs (page, category, content, custom)
	 * @var string
	 */
	_type: '',
	
	/**
	 * true if the submit is done
	 * @var boolean
	 */
	_submitDone: false,
	
	/**
	 * Initializes a menu item transfer.
	 * 
	 * @param string elementID
	 * @param string menuItemListID
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
			this._element.find('button[data-type="submit"]').click($.proxy(this._submit, this));
		}
	},
	
	/**
	 * Stops the form submit event.
	 * 
	 * @param jQuery.Event event
	 * @return boolean
	 */
	_stopFormSubmit: function(event) {
		if (this._type != 'custom') return false;
		if (this._element.find('input[name="title"]').length == 0) {
			this._submit();
		} 
		else if (this._element.find('input[name="title"]').length == 1) {
			this._submit();
		}
		return false;
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
				this._element.find('input[name^="title_i18n"]').each($.proxy(function(index, listItem) {
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
		}
		else {
			this._element.find('dl > dd > ul > li > label > input[type="checkbox"]').each($.proxy(function(index, listItem) {
				var $listItem = $(listItem);
				var $parentID = $listItem.val();
				var $parent = $listItem.parent().parent();
				if ($parentID !== undefined) {
					$checkedParent = $listItem.prop('checked');
					this._getNestedElements($parent, $parentID);
					if ($checkedParent) this._structure[0].push($parentID);
					$listItem.prop('checked', false);
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
			alert($.param($parameters));
			this._proxy.setOption('data', {
				actionName: 'createAJAX',
				className: this._className,
				parameters: $parameters
			});
		}
		this._proxy.sendRequest();
		this._submitDone = true;
	},
	
	/**
	 * Builds all nested elements.
	 * 
	 * @param jQuery  $parent
	 * @param integer $parentID
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
			if ($checked) this._structure[$parentID].push($objectID);
			$(listItem).prop('checked', false);
		}, this));
	},
	
	/**
	 * Shows notification upon success.
	 * 
	 * @param	object		data
	 * @param	string		textStatus
	 * @param	jQuery		jqXHR
	 */
	_success: function(data, textStatus, jqXHR) {
		if (this._notification === null) {
			this._notification = new WCF.System.Notification(WCF.Language.get('wcf.global.form.edit.success'));
		}
		try {
			var dataResponse = $.parseJSON(data);
			var data = dataResponse['returnValues'];
			for (var $menuItemID in data) {
				var $newItemHtml = '<li id="' + WCF.getRandomID() + '" class="sortableNode jsMenuItem" data-object-id="' + $menuItemID + '">';
				$newItemHtml = $newItemHtml + '<span class="sortableNodeLabel"><span class="buttons">';
				if (ULTIMATE.Permission.get('admin.content.ultimate.canDeleteMenuItem')) {
					$newItemHtml = $newItemHtml + '<img src="' + WCF.Icon.get('wcf.icon.delete') + '" alt="" title="' + WCF.Language.get('wcf.global.button.delete') + '" class="icon16 jsDeleteButton jsTooltip" data-object-id="' + $menuItemID + '" data-confirm-message="' + WCF.Language.get('wcf.acp.ultimate.menu.item.delete.sure') + '" />';
				}
				else {
					$newItemHtml = $newItemHtml + '<img src="' + WCF.Icon.get('wcf.icon.delete') + '" alt="" title="' + WCF.Language.get('wcf.global.button.delete') + '" class="icon16 disabled" />';
				}
				if (ULTIMATE.Permission.get('admin.content.ultimate.canEditMenuItem')) {
					$newItemHtml = $newItemHtml + '<img src="' + (data[$menuItemID][isDisabled]) ? WCF.Icon.get('wcf.icon.disabled') : WCF.Icon.get('wcf.icon.enabled') + '" alt="" title="' + (data[$menuItemID][isDisabled]) ? WCF.Language.get('wcf.global.button.enable') : WCF.Language.get('wcf.global.button.disable') + '" class="icon16 jsToggleButton jsTooltip" data-object-id="' + $menuItemID + '" />';
				}
                else {
                	$newItemHtml = $newItemHtml + '<img src="' + (data[$menuItemID][isDisabled]) ? WCF.Icon.get('wcf.icon.disabled') : WCF.Icon.get('wcf.icon.enabled') + '" alt="" title="' + (data[$menuItemID][isDisabled]) ? WCF.Language.get('wcf.global.button.enable') : WCF.Language.get('wcf.global.button.disable') + '" class="icon16 disabled" />';
                }
                $newItemHtml = $newItemHtml + '</span><span class="title">';                
                $newItemHtml = $newItemHtml + data[$menuItemID][menuItemName] + '</span></span></li>';
                
                $(this._menuItemListID + '> .sortableList').append($newItemHtml);
			}
			
			this._notification.show();
		}
		// failed to parse JSON
		catch (e) {
			// call child method if applicable
			var $showError = true;
			if ($.isFunction(this.options.failure)) {
				$showError = this.options.failure(jqXHR, textStatus, errorThrown, jqXHR.responseText);
			}
			
			if (!this._suppressErrors && $showError !== false) {
				$('<div class="ajaxDebugMessage"><p>' + jqXHR.responseText + '</p></div>').wcfDialog({ title: WCF.Language.get('wcf.global.error.title') });
			}
		}
		
	}
};

