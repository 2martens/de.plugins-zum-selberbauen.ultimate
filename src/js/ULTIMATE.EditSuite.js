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
 */
ULTIMATE.EditSuite.AJAXLoading = Class.extend({
	/**
	 * Contains the page container.
	 * 
	 * @type jQuery
	 */
	_pageContainer : null,
	
	/**
	 * Contains the page JS container.
	 * 
	 * @type jQuery
	 */
	_pageJSContainer : null,
	
	/**
	 * Contains a proxy object.
	 * 
	 * @type WCF.Action.Proxy
	 */
    _proxy : null,
    
    /**
	 * Contains a proxy object for jSAJAX requests.
	 * 
	 * @type WCF.Action.Proxy
	 */
    _jsAJAXProxy : null,
    
    /**
     * Contains the cached HTML.
     * 
     * @type Object
     */
    _cachedData : {},
    
    /**
     * Contains the sidebar menu.
     * @type ULTIMATE.EditSuite.SidebarMenu
     */
    _sidebarMenu : null,
	
	/**
	 * Initializes EditSuite AJAXLoading.
	 * 
	 * @param {String} pageContainer
	 * @param {String} pageJSContainer
	 * @param {ULTIMATE.EditSuite.SidebarMenu} sidebarMenu
	 */
	init : function(pageContainer, pageJSContainer, sidebarMenu) {
		this._pageContainer = $('#' + $.wcfEscapeID(pageContainer));
		this._pageJSContainer = $('#' + $.wcfEscapeID(pageJSContainer));
		this._proxy = new WCF.Action.Proxy({
		    success : $.proxy(this._success, this),
		    url: 'index.php/AJAXEditSuite/?t=' + SECURITY_TOKEN + SID_ARG_2ND
	    });
		
		this._jsAJAXProxy = new WCF.Action.Proxy({
			success : $.proxy(this._successJSAJAX, this),
		    url: 'index.php/AJAXEditSuite/?t=' + SECURITY_TOKEN + SID_ARG_2ND
		});
		this._sidebarMenu = sidebarMenu;
		this._initLinks();
		this._initCache();
	},
	
	/**
	 * Initializes the links.
	 */
	_initLinks : function() {
		$('nav.menuGroupItems a').on('click', $.proxy(this._eventClick, this));
		$('#pageContentContainer').on('click', '#pageContent a', $.proxy(this._eventClick, this));
		$(window).on('popstate', $.proxy(this._eventPopstate, this));
	},
	
	/**
	 * Event method for anchor clicks.
	 * 
	 * @param {Event} event
	 */
	_eventClick : function(event) {
		var $target= $(event.currentTarget);
		// Prevent the usual navigation behavior
		event.preventDefault();
		
		// change address bar
		var href = $target.attr('href');
		href = href.replace(/^.*\/\/[^\/]+/, '');
		var stateObject = {
			controller: $target.data('controller'),
			requestType: $target.data('requestType')
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
	 */
	_eventPopstate : function(event) {
		var controller = null;
		var requestType = null;
		
		if (event.originalEvent.state == null) {
			controller = this._pageContainer.data('initialController');
			requestType = this._pageContainer.data('initialRequestType');
		}
		else if (event.originalEvent.state.controller != null) {
			controller = event.originalEvent.state.controller;
			requestType = event.originalEvent.state.requestType;
		}
		
		if (controller != null) {
			// load the content
			if (this._cachedData[controller] != null) {
				if (this._cachedData[controller]['jsAJAXOnly']) {
					this._fireRequest(controller, requestType, 'jsOnly');
				}
				else if (this._cachedData[controller]['ajaxOnly']) {
					this._fireRequest(controller, requestType, 'fullHTML');
				}
				else {
					this._replaceHTML(controller);
				}
			}
			else {
				this._fireRequest(controller, requestType, 'fullHTML');
			}
		}
	},
	
	/**
	 * Initiates the cache.
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
	 */
	_fireRequest : function(controller, requestType, url, actionName) {
		// get query data
		var queryData = url.substr(url.indexOf('?') + 1).split('&');
		var queryDataObject = $.getQueryData(queryData);
		
		// build proxy data
	    var $data = $.extend(true, {
	        controller: controller,
	        requestType: requestType,
	        actionName: actionName,
	        queryData: queryDataObject
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
