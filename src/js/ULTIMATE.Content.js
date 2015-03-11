'use strict';

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

        if ($.browser.redactor) {
            $message = $('#' + this._messageEditorIDPrefix + $objectID).redactor('getText');
        }
        else {
            $message = $('#' + this._messageEditorIDPrefix + $objectID).val();
        }

        var $parameters = {
            containerID: this._containerID,
            data: {
                message: $message,
                isI18n: $isI18n
            },
            objectID: $objectID
        };

        WCF.System.Event.fireEvent('com.woltlab.wcf.messageOptionsInline', 'submit_' + this._messageEditorIDPrefix + $objectID, $parameters);

        this._proxy.setOption('data', {
            actionName: 'save',
            className: this._getClassName(),
            interfaceName: 'wcf\\data\\IMessageInlineEditorAction',
            parameters: $parameters
        });
        this._proxy.sendRequest();

        this._hideEditor();
	}
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
		return $('.message');
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
	 * @param {jQuery} likeButton
	 * @param {jQuery} dislikeButton
	 * @param {Number} likeStatus
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
