/**
 * Class and function collection for ULTIMATE CMS Edit Suite
 * 
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 */

/**
 * Namespace for ULTIMATE.EditSuite 
 * @namespace
 */
ULTIMATE.EditSuite = {};

/**
 * Handles EditSuite sidebar menu.
 * 
 * Copied mainly from WCF.ACP.Menu
 * 
 * @param {Array} activeMenuItems
 * @constructor
 */
ULTIMATE.EditSuite.SidebarMenu = Class.extend({
	/**
	 * Contains the complete sidebar navigation.
	 * 
	 * @type jQuery
	 */
	_sidebarNavigation : null,
	
	/**
	 * Contains the active menu items.
	 * 
	 * @type Array
	 */
	_activeMenuItems : [],
	
	/**
	 * Initializes EditSuite sidebar menu.
	 * 
	 * @param {Array} activeMenuItems
	 */
	init : function(activeMenuItems) {
		this._sidebarNavigation = $('aside.collapsibleMenu > div');
		this._activeMenuItems = activeMenuItems;
		this._prepareElements(activeMenuItems);
	},
	
	/**
	 * Updates the active menu items.
	 * 
	 * @param {Array} activeMenuItems
	 */
	updateActiveItems : function(activeMenuItems) {
		this._activeMenuItems = activeMenuItems;
		this._renderSidebar(activeMenuItems);
	},
	
	/**
	 * Returns the active menu items.
	 * 
	 * @return {Array}
	 */
	getActiveMenuItems : function() {
		return this._activeMenuItems;
	},
	
	/**
	 * Resets all elements and binds event listeners.
	 */
	_prepareElements : function(activeMenuItems) {
		this._sidebarNavigation.find('legend').each($.proxy(function(index, menuHeader) {
			$(menuHeader).click($.proxy(this._toggleItem, this));
		}, this));
		
		// close all navigation groups
		this._sidebarNavigation.find('nav ul').each(function() {
			$(this).hide();
		});
		
		if (activeMenuItems.length === 0) {
			this._renderSidebar([]);
		}
		else {
			this._renderSidebar(activeMenuItems);
		}
	},
	
	/**
	 * Toggles a navigation group entry.
	 */
	_toggleItem : function(event) {
		var $menuItem = $(event.currentTarget);
		
		$menuItem.parent().find('nav ul').stop(true, true).toggle('blind', { }, 200).end();
		$menuItem.toggleClass('active');
	},
	
	/**
	 * Renders sidebar including highlighting of currently active menu items.
	 * 
	 * @param {Array} activeMenuItems
	 */
	_renderSidebar : function(activeMenuItems) {
		this._sidebarNavigation.find('li').removeClass('active');
		this._sidebarNavigation.find('legend').removeClass('active');
		this._sidebarNavigation.find('nav ul').each(function() {
			$(this).hide();
		});
		// reset visible and active items
		for (var $i = 0, $size = activeMenuItems.length; $i < $size; $i++) {
			var $item = activeMenuItems[$i];
			
			if ($.wcfIsset($item)) {
				var $menuItem = $('#' + $.wcfEscapeID($item));
				
				if ($menuItem.getTagName() === 'ul') {
					$menuItem.show().parents('fieldset').children('legend').addClass('active');
				}
				else {
					$menuItem.addClass('active');
				}
			}
		}
	}
});

/**
 * Dictionary for identifiers of anchors, that should be handled
 * via AJAX.
 * 
 * @see WCF.Dictionary
 * @since version 1.1.0
 */
ULTIMATE.EditSuite.AJAXIdentifiers = {
	/**
	 * Contains the identifiers.
	 * 
	 * @type WCF.Dictionary
	 */
	_identifiers : new WCF.Dictionary(),
	
	/**
	 * @param {String} key
	 * @param {String} value
	 * @see WCF.Dictionary.add()
	 */
	add : function(key, value) {
		this._identifiers.add(key, value);
	},

	/**
	 * @see WCF.Dictionary.addObject()
	 */
	addObject : function(object) {
		this._identifiers.addObject(object);
	},

	/**
	 * Retrieves an identifier.
	 * 
	 * @param {String} key
	 * @return {String}
	 */
	get : function(key) {
		var value = this._identifiers.get(key);

		if (value === null) {
			// return key again
			return key;
		}

		return value;
	}
};

/**
 * Handles EditSuite AJAX loading.
 * 
 * @param {String} pageContainer
 * @param {String} pageJSContainer
 * @param {ULTIMATE.EditSuite.SidebarMenu} menuSidebar
 * @constructor
 */
ULTIMATE.EditSuite.AJAXLoading = Class.extend({
	/**
	 * Contains the page container.
	 * 
	 * @type jQuery
     * @private
	 */
	_pageContainer : null,
	
	/**
	 * Contains the page JS container.
	 * 
	 * @type jQuery
     * @private
	 */
	_pageJSContainer : null,
	
	/**
	 * Contains a proxy object.
	 * 
	 * @type WCF.Action.Proxy
     * @private
	 */
    _proxy : null,
    
    /**
	 * Contains a proxy object for jSAJAX requests.
	 * 
	 * @type WCF.Action.Proxy
     * @private
	 */
    _jsAJAXProxy : null,
    
    /**
     * Contains the cached HTML.
     * 
     * @type Object
     * @private
     */
    _cachedData : {},
    
    /**
     * Contains the sidebar menu.
     * @type ULTIMATE.EditSuite.SidebarMenu
     * @private
     */
    _sidebarMenu : null,
	
	/**
	 * Initializes EditSuite AJAXLoading.
	 * 
	 * @param {String} pageContainer
	 * @param {String} pageJSContainer
	 * @param {ULTIMATE.EditSuite.SidebarMenu} sidebarMenu
     * @public
	 */
	init : function(pageContainer, pageJSContainer, sidebarMenu) {
		this._pageContainer = $('#' + $.wcfEscapeID(pageContainer));
		this._pageJSContainer = $('#' + $.wcfEscapeID(pageJSContainer));
		this._proxy = new WCF.Action.Proxy({
		    success : $.proxy(this._success, this),
		    url : 'index.php/AJAXEditSuite/?t=' + SECURITY_TOKEN + SID_ARG_2ND
	    });
		
		this._jsAJAXProxy = new WCF.Action.Proxy({
			success : $.proxy(this._successJSAJAX, this),
		    url : 'index.php/AJAXEditSuite/?t=' + SECURITY_TOKEN + SID_ARG_2ND
		});
		this._sidebarMenu = sidebarMenu;
		this._initLinks();
		this._initCache();
	},
	
	/**
	 * Initializes the links.
     * @private
	 */
	_initLinks : function() {
		$('nav.menuGroupItems a').on('click', $.proxy(this._eventClick, this));
		$('#pageContentContainer').on('click', '#pageContent a[data-controller]', $.proxy(this._eventClick, this));
		$(window).on('popstate', $.proxy(this._eventPopstate, this));
	},
	
	/**
	 * Event method for anchor clicks.
	 * 
	 * @param {Event} event
     * @private
	 */
	_eventClick : function(event) {
		var $target= $(event.currentTarget);
		// Prevent the usual navigation behavior
		event.preventDefault();
		
		// change address bar
		var href = $target.attr('href');
		href = href.replace(/^.*\/\/[^\/]+/, '');
		var stateObject = {
			controller : $target.data('controller'),
			requestType : $target.data('requestType'),
            url: href
		};
		history.pushState(stateObject, '', href);
		
		// fire request
		if (this._cachedData[$target.data('controller')] != null) {
			if (this._cachedData[$target.data('controller')]['jsAJAXOnly']) {
				this._fireRequest($target.data('controller'), $target.data('requestType'), href, 'jsOnly');
			}
			else if (this._cachedData[$target.data('controller')]['ajaxOnly']) {
				this._fireRequest($target.data('controller'), $target.data('requestType'), href, 'fullHTML');
			}
			else {
				this._replaceHTML($target.data('controller'));
			}
		}
		else {
			this._fireRequest($target.data('controller'), $target.data('requestType'), href, 'fullHTML');
		}
	},
	
	/**
	 * Event method for popstate event.
	 * 
	 * @param {jQuery.Event} event
     * @private
	 */
	_eventPopstate : function(event) {
		var controller = null;
		var requestType = null;
        var url = '';
		
		if (event.originalEvent.state == null) {
			controller = this._pageContainer.data('initialController');
			requestType = this._pageContainer.data('initialRequestType');
            url = this._pageContainer.data('initialUrl');
		}
		else if (event.originalEvent.state.controller != null) {
			controller = event.originalEvent.state.controller;
			requestType = event.originalEvent.state.requestType;
            url = event.originalEvent.state.url;
		}
		
		if (controller != null) {
			// load the content
			if (this._cachedData[controller] != null) {
				if (this._cachedData[controller]['jsAJAXOnly']) {
					this._fireRequest(controller, requestType, url, 'jsOnly');
				}
				else if (this._cachedData[controller]['ajaxOnly']) {
					this._fireRequest(controller, requestType, url, 'fullHTML');
				}
				else {
					this._replaceHTML(controller);
				}
			}
			else {
				this._fireRequest(controller, requestType, url, 'fullHTML');
			}
		}
	},
	
	/**
	 * Initiates the cache.
     * @private
	 */
	_initCache : function() {
		var controller = this._pageContainer.find('#pageContent').data('controller');
		var requestType = this._pageContainer.find('#pageContent').data('requestType');
        var $pageContent = $('#pageContent');
        var $pageJS = $('#pageJS');
		this._cachedData[controller] = {
			html : '<div id="pageContent" data-controller="' + controller + '" data-request-type="' + requestType + '">'
				+ $pageContent.html()
				+ '</div>',
			activeMenuItems : this._sidebarMenu.getActiveMenuItems(),
			js : $pageJS.html(),
			jsAJAXOnly : $pageJS.data('ajaxOnly'),
			ajaxOnly : $pageContent.data('ajaxOnly')
		};
	},
	
	/**
	 * Fires an AJAX request to load the form/page content.
	 * 
	 * @param {String} controller
	 * @param {String} requestType
	 * @param {String} url
	 * @param {String} actionName
     * @private
	 */
	_fireRequest : function(controller, requestType, url, actionName) {
		// get query data
		var queryData = url.substr(url.indexOf('?') + 1).split('&');
		var queryDataObject = $.getQueryData(queryData);
        var urlWithoutQuery = (url.indexOf('?') != -1 ? url.substr(0, url.indexOf('?')) : url);
        var idRegex = /\/(\d+)\/$/;
        var id = idRegex.exec(urlWithoutQuery);
        if (id !== null) {
            id = id[1];
        }
        if (id) {
            queryDataObject['id'] = id;
        }
		
		// build proxy data
	    var $data = $.extend(true, {
	        controller : controller,
	        requestType : requestType,
	        actionName : actionName,
	        queryData : queryDataObject
	    }, {});
	    
	    if (actionName == 'jsOnly') {
	    	this._jsAJAXProxy.setOption('data', $data);
	    	// send proxy request
		    this._jsAJAXProxy.sendRequest();
	    }
	    else {
	    	this._proxy.setOption('data', $data);
	    	// send proxy request
		    this._proxy.sendRequest();
	    }
	},
	
	/**
	 * Displays HTML content.
	 * 
	 * @param {Object} data
	 * @param {String} textStatus
	 * @param {jQuery} jqXHR
     * @private
	 */
	_success : function(data, textStatus, jqXHR) {
		var $html = $(data.html);
		var $js = $(data.js);
		this._cachedData[data.controller] = {};
		this._cachedData[data.controller]['activeMenuItems'] = data.activeMenuItems;
		this._cachedData[data.controller]['html'] = '<div id="pageContent" data-controller="' + data.controller + '" data-request-type="' + data.requestType + '">'
		+ $html.find('#pageContent').html()
		+ '</div>';
		this._cachedData[data.controller]['js'] = $js.html();
		this._cachedData[data.controller]['jsAJAXOnly'] = $js.data('ajaxOnly');
		this._cachedData[data.controller]['ajaxOnly'] = $html.find('#pageContent').data('ajaxOnly');
		this._replaceHTML(data.controller);
	},
	
	/**
	 * Displays HTML content.
	 * 
	 * @param {Object} data
	 * @param {String} textStatus
	 * @param {jQuery} jqXHR
     * @private
	 */
	_successJSAJAX : function(data, textStatus, jqXHR) {
		var $js = $(data.js);
		this._cachedData[data.controller]['js'] = $js.html();
		this._replaceHTML(data.controller);
	},
	
	/**
	 * Replaces the HTML.
	 * 
	 * @param {String} controller
     * @private
	 */
	_replaceHTML : function(controller) {
		var script = $(this._cachedData[controller]['js']);
		var scriptContent = script.html();
		eval(scriptContent);
		var rawFunctionName = 'init' + controller;
		eval(rawFunctionName + '()');
		
		this._pageContainer.html(this._cachedData[controller]['html']);
//		this._pageJSContainer.html(this._cachedData[controller]['js']);
		this._sidebarMenu.updateActiveItems(this._cachedData[controller]['activeMenuItems']);
		
		script = $(this._cachedData[controller]['js']);
		scriptContent = script.html();
		eval(scriptContent);
		rawFunctionName = 'postInit' + controller;
		eval(rawFunctionName + '()');
	}
});

/**
 * Extends the WCF.Clipboard API with functionality
 * to handle AJAX requests.
 *
 * @param {String} page
 * @param {Number} hasMarkedItems
 * @param {Object} actionObjects
 * @param {Number} pageObjectID
 * @constructor
 */
ULTIMATE.EditSuite.Clipboard = {
    /**
     * action proxy object
     * @type WCF.Action.Proxy
     */
    _actionProxy : null,

    /**
     * action objects
     * @type Object
     */
    _actionObjects : {},

    /**
     * list of clipboard containers
     * @type jQuery
     */
    _containers : null,

    /**
     * container meta data
     * @type Object
     */
    _containerData : { },

    /**
     * user has marked items
     * @type Boolean
     */
    _hasMarkedItems : false,

    /**
     * list of ids of marked objects grouped by object type
     * @type Object
     */
    _markedObjectIDs : { },

    /**
     * current page
     * @type String
     */
    _page : '',

    /**
     * current page's object id
     * @type Number
     */
    _pageObjectID : 0,

    /**
     * proxy object
     * @type WCF.Action.Proxy
     */
    _proxy : null,

    /**
     * list of elements already tracked for clipboard actions
     * @type Object
     */
    _trackedElements : { },

    /**
     * counter for markAll calls
     * @type Number
     */
    _markAllCalls : 0,

    /**
     * Initializes the clipboard API.
     *
     * @param {String} page
     * @param {Number} hasMarkedItems
     * @param {Object} actionObjects
     * @param {Number} pageObjectID
     */
    init : function(page, hasMarkedItems, actionObjects, pageObjectID) {
        this._page = page;
        this._actionObjects = actionObjects || { };
        this._hasMarkedItems = (hasMarkedItems > 0);
        this._pageObjectID = parseInt(pageObjectID) || 0;

        this._actionProxy = new WCF.Action.Proxy({
            success : $.proxy(this._actionSuccess, this),
            url : 'index.php/ClipboardProxy/?t=' + SECURITY_TOKEN + SID_ARG_2ND
        });

        this._proxy = new WCF.Action.Proxy({
            success : $.proxy(this._success, this),
            url : 'index.php/Clipboard/?t=' + SECURITY_TOKEN + SID_ARG_2ND
        });

        // init containers first
        this._containers = $('.jsClipboardContainer').each($.proxy(function(index, container) {
            this._initContainer(container);
        }, this));

        // loads marked items
        if (this._hasMarkedItems && this._containers.length) {
            this._loadMarkedItems();
        }

        var self = this;
        $('#pageContentContainer').on('click', '.jsClipboardContainer .jsClipboardMarkAll', $.proxy(this._markAll, this));
        WCF.DOMNodeInsertedHandler.addCallback('ULTIMATE.EditSuite.Clipboard', function() {
//            self._trackedElements = {};
            self._containers = $('.jsClipboardContainer').each($.proxy(function(index, container) {
                self._initContainer(container);
            }, self));
        });
    },

    /**
     * Loads marked items on init.
     */
    _loadMarkedItems : function() {
        new WCF.Action.Proxy({
            autoSend : true,
            data : {
                containerData : this._containerData,
                pageClassName : this._page,
                pageObjectID : this._pageObjectID
            },
            success : $.proxy(this._loadMarkedItemsSuccess, this),
            url : 'index.php/ClipboardLoadMarkedItems/?t=' + SECURITY_TOKEN + SID_ARG_2ND
        });
    },

    /**
     * Reloads the list of marked items.
     */
    reload : function() {
        if (this._containers === null) {
            return;
        }
        
        this._loadMarkedItems();
    },

    /**
     * Marks all returned items as marked
     *
     * @param {Object} data
     * @param {String} textStatus
     * @param {jQuery} jqXHR
     */
    _loadMarkedItemsSuccess : function(data, textStatus, jqXHR) {
        this._resetMarkings();
        var $typeName;

        for ($typeName in data['markedItems']) {
            if (data['markedItems'].hasOwnProperty($typeName)) {
                if (!this._markedObjectIDs[$typeName]) {
                    this._markedObjectIDs[$typeName] = [ ];
                }
    
                var $objectData = data['markedItems'][$typeName];
                for (var $i in $objectData) {
                    if ($objectData.hasOwnProperty($i)) {
                        this._markedObjectIDs[$typeName].push($objectData[$i]);
                    }
                }
    
                // loop through all containers
                this._containers.each($.proxy(function(index, container) {
                    var $container = $(container);
    
                    // typeName does not match, continue
                    if ($container.data('type') !== $typeName) {
                        return;
                    }
    
                    // mark items as marked
                    $container.find('input.jsClipboardItem').each($.proxy(function(innerIndex, item) {
                        var $item = $(item);
                        if (WCF.inArray($item.data('objectID'), this._markedObjectIDs[$typeName])) {
                            $item.prop('checked', true);
    
                            // add marked class for element container
                            $item.parents('.jsClipboardObject').addClass('jsMarked');
                        }
                    }, this));
    
                    // check if there is a markAll-checkbox
                    $container.find('input.jsClipboardMarkAll').each(function(innerIndex, markAll) {
                        var $allItemsMarked = true;
    
                        $container.find('input.jsClipboardItem').each(function(itemIndex, item) {
                            var $item = $(item);
                            if (!$item.prop('checked')) {
                                $allItemsMarked = false;
                            }
                        });
    
                        if ($allItemsMarked) {
                            $(markAll).prop('checked', true);
                        }
                    });
                }, this));
            }
        }

        // call success method to build item list editors
        this._success(data, textStatus, jqXHR);
    },

    /**
     * Resets all checkboxes.
     */
    _resetMarkings : function() {
        this._containers.each($.proxy(function(index, container) {
            var $container = $(container);

            this._markedObjectIDs[$container.data('type')] = [ ];
            $container.find('input.jsClipboardItem, input.jsClipboardMarkAll').prop('checked', false);
            $container.find('.jsClipboardObject').removeClass('jsMarked');
        }, this));
    },

    /**
     * Initializes a clipboard container.
     *
     * @param {Object} container
     */
    _initContainer : function(container) {
        var $container = $(container);
        var $containerID = $container.wcfIdentify();
        if (!this._trackedElements[$containerID]) {
            $container.find('.jsClipboardMarkAll').data('hasContainer', $containerID);

            this._markedObjectIDs[$container.data('type')] = [ ];
            this._containerData[$container.data('type')] = {};
            $.each($container.data(), $.proxy(function(index, element) {
                if (index.match(/^type(.+)/)) {
                    this._containerData[$container.data('type')][WCF.String.lcfirst(index.replace(/^type/, ''))] = element;
                }
            }, this));
    
            this._trackedElements[$containerID] = [ ];
        }
        else {
            $container.find('.jsClipboardMarkAll').data('hasContainer', $containerID);
        }

        // track individual checkboxes
        $container.find('input.jsClipboardItem').each($.proxy(function(index, input) {
            var $input = $(input);
            var $inputID = $input.wcfIdentify();

            if (!WCF.inArray($inputID, this._trackedElements[$containerID])) {
                this._trackedElements[$containerID].push($inputID);
    
                $input.data('hasContainer', $containerID).click($.proxy(this._click, this));
            }
        }, this));
    },

    /**
     * Processes change checkbox state.
     *
     * @param {Object} event
     */
    _click : function(event) {
        var $item = $(event.target);
        var $objectID = $item.data('objectID');
        var $isMarked = ($item.prop('checked')) ? true : false;
        var $objectIDs = [ $objectID ];
        var $type;
        if ($item.data('hasContainer')) {
            var $container = $('#' + $item.data('hasContainer'));
            $type = $container.data('type');
        }
        else {
            $type = $item.data('type');
        }

        if ($isMarked) {
            this._markedObjectIDs[$type].push($objectID);
            $item.parents('.jsClipboardObject').addClass('jsMarked');
        }
        else {
            this._markedObjectIDs[$type] = $.removeArrayValue(this._markedObjectIDs[$type], $objectID);
            $item.parents('.jsClipboardObject').removeClass('jsMarked');
        }

        // item is part of a container
        if ($item.data('hasContainer')) {
            // check if all items are marked
            var $markedAll = true;
            $container.find('input.jsClipboardItem').each(function(index, containerItem) {
                var $containerItem = $(containerItem);
                if (!$containerItem.prop('checked')) {
                    $markedAll = false;
                }
            });

            // simulate a ticked 'markAll' checkbox
            $container.find('.jsClipboardMarkAll').each(function(index, markAll) {
                if ($markedAll) {
                    $(markAll).prop('checked', true);
                }
                else {
                    $(markAll).prop('checked', false);
                }
            });
        }

        this._saveState($type, $objectIDs, $isMarked);
    },

    /**
     * Marks all associated clipboard items as checked.
     *
     * @param {Event} event
     */
    _markAll : function(event) {
        var $item = $(event.target);
        var $objectIDs = [ ];
        var $isMarked = true;
        var $type;
        
        // this event handler has been called once before this time around
        if (this._markAllCalls) {
            return;
        } 
        this._markAllCalls = 1;

        // if markAll object is a checkbox, allow toggling
        if ($item.is('input')) {
            $isMarked = $item.prop('checked');
        }

        if ($item.data('hasContainer')) {
            var $container = $('#' + $item.data('hasContainer'));
            $type = $container.data('type');
        }
        else {
            $type = $item.data('type');
        }

        // handle item containers
        if ($item.data('hasContainer')) {
            // toggle state for all associated items
            $container.find('input.jsClipboardItem').each($.proxy(function(index, containerItem) {
                var $containerItem = $(containerItem);
                var $objectID = $containerItem.data('objectID');
                if ($isMarked) {
                    if (!$containerItem.prop('checked')) {
                        $containerItem.prop('checked', true);
                        this._markedObjectIDs[$type].push($objectID);
                        $objectIDs.push($objectID);
                    }
                }
                else {
                    if ($containerItem.prop('checked')) {
                        $containerItem.prop('checked', false);
                        this._markedObjectIDs[$type] = $.removeArrayValue(this._markedObjectIDs[$type], $objectID);
                        $objectIDs.push($objectID);
                    }
                }
            }, this));

            if ($isMarked) {
                $container.find('.jsClipboardObject').addClass('jsMarked');
            }
            else {
                $container.find('.jsClipboardObject').removeClass('jsMarked');
            }
            // save new status
            this._saveState($type, $objectIDs, $isMarked);
        }
    },

    /**
     * Saves clipboard item state.
     *
     * @param {String}  type
     * @param {Array}   objectIDs
     * @param {Boolean} isMarked
     */
    _saveState : function(type, objectIDs, isMarked) {
        this._proxy.setOption('data', {
            action : (isMarked) ? 'mark'  : 'unmark',
            containerData : this._containerData,
            objectIDs : objectIDs,
            pageClassName : this._page,
            pageObjectID : this._pageObjectID,
           type : type
        });
        this._proxy.sendRequest();
    },

    /**
     * Updates editor options.
     *
     * @param {Object} data
     * @param {String} textStatus
     * @param {jQuery} jqXHR
     */
    _success : function(data, textStatus, jqXHR) {
        // clear all editors first
        var $containers = {};
        $('.jsClipboardEditor').each(function(index, container) {
            var $container = $(container);
            var $types = eval($container.data('types'));
            for (var $i = 0, $length = $types.length; $i < $length; $i++) {
                var $typeName = $types[$i];
                $containers[$typeName] = $container;
            }

            var $containerID = $container.wcfIdentify();
            WCF.CloseOverlayHandler.removeCallback($containerID);

            $container.empty();
        });

        // do not build new editors
        if (!data.items) return;

        // rebuild editors
        for (var $typeName in data.items) {
            if (data.items.hasOwnProperty($typeName)) {
                if (!$containers[$typeName]) {
                    continue;
                }
    
                // create container
                var $container = $containers[$typeName];
                var $list = $container.children('ul');
                if ($list.length == 0) {
                    $list = $('<ul />').appendTo($container);
                }
    
                var $editor = data.items[$typeName];
                var $label = $('<li class="dropdown"><span class="dropdownToggle button">' + $editor.label + '</span></li>').appendTo($list);
                var $itemList = $('<ol class="dropdownMenu"></ol>').appendTo($label);
    
                // create editor items
                for (var $itemIndex in $editor.items) {
                    if (!$editor.items.hasOwnProperty($itemIndex)) {
                        continue;
                    }
                    var $item = $editor.items[$itemIndex];
    
                    var $listItem = $('<li><span>' + $item.label + '</span></li>').appendTo($itemList);
                    $listItem.data('container', $container);
                    $listItem.data('objectType', $typeName);
                    $listItem.data('actionName', $item.actionName).data('parameters', $item.parameters);
                    $listItem.data('internalData', $item.internalData).data('url', $item.url).data('type', $typeName);
    
                    // bind event
                    $listItem.click($.proxy(this._executeAction, this));
                }
    
                // add 'unmark all'
                $('<li class="dropdownDivider" />').appendTo($itemList);
                $('<li><span>' + WCF.Language.get('wcf.clipboard.item.unmarkAll') + '</span></li>').appendTo($itemList).click($.proxy(function() {
                    this._proxy.setOption('data', {
                        action : 'unmarkAll',
                       type : $typeName
                    });
                    this._proxy.setOption('success', $.proxy(function(data, textStatus, jqXHR) {
                        this._containers.each($.proxy(function(index, container) {
                            var $container = $(container);
                            if ($container.data('type') == $typeName) {
                                $container.find('.jsClipboardMarkAll, .jsClipboardItem').prop('checked', false);
                                $container.find('.jsClipboardObject').removeClass('jsMarked');
    
                                return false;
                            }
                        }, this));
    
                        // call and restore success method
                        this._success(data, textStatus, jqXHR);
                        this._proxy.setOption('success', $.proxy(this._success, this));
                    }, this));
                    this._proxy.sendRequest();
                }, this));
    
                WCF.Dropdown.initDropdown($label.children('.dropdownToggle'), false);
            }
        }
        // reset call count to 0
        this._markAllCalls = 0;
    },

    /**
     * Closes the clipboard editor item list.
     */
    _closeLists : function() {
        $('.jsClipboardEditor ul').removeClass('dropdownOpen');
    },

    /**
     * Executes a clipboard editor item action.
     *
     * @param {Event} event
     */
    _executeAction : function(event) {
        var $listItem = $(event.currentTarget);
        var $url = $listItem.data('url');
        if ($url) {
            window.location.href = $url;
        }

        if ($listItem.data('parameters').className && $listItem.data('parameters').actionName) {
            if ($listItem.data('parameters').actionName === 'unmarkAll' || $listItem.data('parameters').objectIDs) {
                var $confirmMessage = $listItem.data('internalData')['confirmMessage'];
                if ($confirmMessage) {
                    var $template = $listItem.data('internalData')['template'];
                    if ($template) $template = $($template);

                    WCF.System.Confirmation.show($confirmMessage, $.proxy(function(action) {
                        if (action === 'confirm') {
                            var $data = { };

                            if ($template && $template.length) {
                                $('#wcfSystemConfirmationContent').find('input, select, textarea').each(function(index, item) {
                                    var $item = $(item);
                                    $data[$item.prop('name')] = $item.val();
                                });
                            }

                            this._executeAJAXActions($listItem, $data);
                        }
                    }, this), '', $template);
                }
                else {
                    this._executeAJAXActions($listItem, { });
                }
            }
        }

        // fire event
        $listItem.data('container').trigger('clipboardAction', [ $listItem.data('type'), $listItem.data('actionName'), $listItem.data('parameters') ]);
    },

    /**
     * Executes the AJAX actions for the given editor list item.
     *
     * @param {jQuery} listItem
     * @param {Object} data
     */
    _executeAJAXActions : function(listItem, data) {
        data = data || { };
        var $objectIDs = [];
        if (listItem.data('parameters').actionName !== 'unmarkAll') {
            $.each(listItem.data('parameters').objectIDs, function(index, objectID) {
                $objectIDs.push(parseInt(objectID));
            });
        }

        var $parameters = {
            data : data,
            containerData : this._containerData[listItem.data('type')]
        };
        var $__parameters = listItem.data('internalData')['parameters'];
        if ($__parameters !== undefined) {
            for (var $key in $__parameters) {
                if (!$__parameters.hasOwnProperty($key)) {
                    continue;
                }
                $parameters[$key] = $__parameters[$key];
            }
        }

        new WCF.Action.Proxy({
            autoSend : true,
            data : {
                actionName : listItem.data('parameters').actionName,
                className : listItem.data('parameters').className,
                objectIDs : $objectIDs,
                parameters : $parameters
            },
            success : $.proxy(function(data) {
                if (listItem.data('parameters').actionName !== 'unmarkAll') {
                    listItem.data('container').trigger('clipboardActionResponse', [ data, listItem.data('type'), listItem.data('actionName'), listItem.data('parameters') ]);
                }

                this._loadMarkedItems();
            }, this)
        });

        if (this._actionObjects[listItem.data('objectType')] && this._actionObjects[listItem.data('objectType')][listItem.data('parameters').actionName]) {
            this._actionObjects[listItem.data('objectType')][listItem.data('parameters').actionName].triggerEffect($objectIDs);
        }
    },

    /**
     * Sends a clipboard proxy request.
     *
     * @param {Object} item
     */
    sendRequest : function(item) {
        var $item = $(item);

        this._actionProxy.setOption('data', {
            parameters : $item.data('parameters'),
            typeName : $item.data('type')
        });
        this._actionProxy.sendRequest();
    }
};

/**
 * Namespace for ULTIMATE.ACP.Button
 *
 * @namespace
 */
ULTIMATE.EditSuite.Button = {};

/**
 * Handles button replacements.
 *
 * @param {String} buttonID
 * @param {String} checkElementID
 * @param {String} action
 * @constructor
 * @since version 1.0.0
 */
ULTIMATE.EditSuite.Button.Replacement = function(buttonID, checkElementID, action) {
    this.init(buttonID, checkElementID, action);
};
ULTIMATE.EditSuite.Button.Replacement.prototype = {
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
     * @type Number
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
     * @param {String} buttonID
     * @param {String} checkElementID
     * @param {String} action
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
