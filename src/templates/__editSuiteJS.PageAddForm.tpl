<div id="pageJS" data-ajax-only="false">
	<script type="text/javascript">
		initFunction = function initPageAddForm() {
			// date picker
			WCF.Date.Picker.init();
			WCF.DOMNodeRemovedHandler.removeCallback('WCF.Attachment.Upload');
			$(document).off('click', '.jsSmiley');
		};
		
		postInitFunction = function postInitPageAddForm() {
			$(document).off('click', '.jsSmiley');
			new WCF.Message.Smilies('text');
		};
	</script>
</div>
