<div id="pageJS" data-ajax-only="false">
	<script type="text/javascript">
		function initPageAddForm() {
			// date picker
			WCF.Date.Picker.init();
			WCF.DOMNodeRemovedHandler.removeCallback('WCF.Attachment.Upload');
			$(document).off('click', '.jsSmiley');
		}
		
		function postInitPageAddForm() {
			$(document).off('click', '.jsSmiley');
			new WCF.Message.Smilies('text');
		}

        function initPageEditForm() {
            // date picker
            WCF.Date.Picker.init();
            WCF.DOMNodeRemovedHandler.removeCallback('WCF.Attachment.Upload');
            $(document).off('click', '.jsSmiley');
        }

        function postInitPageEditForm() {
            $(document).off('click', '.jsSmiley');
            new WCF.Message.Smilies('text');
        }
	</script>
</div>
