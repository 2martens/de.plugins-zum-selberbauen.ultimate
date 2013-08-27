/**
 * Class and function collection for ULTIMATE CMS ACP
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 */

/**
 * Initialize ULTIMATE.ACP namespace
 * @namespace
 */
ULTIMATE.ACP = {};

/**
 * Namespace for ULTIMATE.ACP.Button
 * 
 * @namespace
 */
ULTIMATE.ACP.Button = {};

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
ULTIMATE.ACP.Button.Replacement = function(buttonID, checkElementID, action) {
	this.init(buttonID, checkElementID, action);
};
ULTIMATE.ACP.Button.Replacement.prototype = {
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
 * Namespace for ULTIMATE.ACP.Block
 * 
 * @namespace
 */
ULTIMATE.ACP.Block = {};

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
ULTIMATE.ACP.Block.Transfer = function(elementID, containerID, className) {
	this.init(elementID, containerID, className);
};
ULTIMATE.ACP.Block.Transfer.prototype = {
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
							var $languageID = $listItem.attr('name').mb_substring(
									$item.length + 6);
							$languageID = $languageID.mb_substr(0,
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
			
			$('#blockForm input[type="checkbox"]').change(function(event) {
				var $target = $(event.currentTarget);
				var checked = $target.prop('checked');
				if (checked) {
					$target.val('1');
				} else {
					$target.val('0');
				}
			});

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
			
			$('#blockForm input[type="checkbox"]').change(function(event) {
				var $target = $(event.currentTarget);
				var checked = $target.prop('checked');
				if (checked) {
					$target.val('1');
				} else {
					$target.val('0');
				}
			});

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
 * Namespace for ULTIMATE.ACP.Menu
 * 
 * @namespace
 */
ULTIMATE.ACP.Menu = {};

/**
 * Namespace for ULTIMATE.ACP.Menu.Item
 * 
 * @namespace
 */
ULTIMATE.ACP.Menu.Item = {};

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
ULTIMATE.ACP.Menu.Item.Transfer = function(elementID, menuItemListID, className,
		offset, type) {
	this.init(elementID, menuItemListID, className, offset, type);
};
ULTIMATE.ACP.Menu.Item.Transfer.prototype = {

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
			var linkType = $('input[name="linkType"]').val();
			var url = $('#url').val();
			var controller = $('#controller').val();
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
							var $languageID = $listItem.attr('name').mb_substring(
									11);
							$languageID = $languageID.mb_substr(0,
									$languageID.length - 1);
							linkTitle_i18n[$languageID] = $listItem.val();
						}, this));
				$data = $.extend(true, {
					title_i18n : linkTitle_i18n
				}, $data);
			}
			this._structure['url'] = url;
			this._structure['controller'] = controller;
			this._structure['linkType'] = linkType;
			this._structure['linkTitle'] = linkTitle;
			// resets the form
			$('#url').val('http://');
			$('#controller').val('');
			$('input[name="linkType"]').val('controller');
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
					$newItemHtml += '&nbsp;<span class="icon icon16 icon-remove'
							+ ' disabled" ' + 'title="'
							+ WCF.Language.get('wcf.global.button.delete')
							+ '"></span>';
				}
				$newItemHtml += '</span></span><ol class="sortableList" data-object-id="' + $menuItemID + '"></ol></li>';
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