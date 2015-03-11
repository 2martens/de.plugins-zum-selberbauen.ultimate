<div id="pageJS" data-ajax-only="false">
	<script type="text/javascript">
		initFunction = function initContentAddForm() {
			// tagging
			{foreach from=$availableLanguages key=languageID item=languageName}
				WCF.Dropdown.removeDropdown('tagSearchInputWrap{$languageID}');
			{/foreach}
			WCF.Dropdown.removeDropdown('tagSearchWrap');
			$('#tagSearchWrap').find('> p').remove();
			
			WCF.DOMNodeRemovedHandler.removeCallback('WCF.Attachment.Upload');
			$(document).off('click', '.jsSmiley');
		};
		
		postInitFunction = function postInitContentAddForm() {
			$(document).off('click', '.jsSmiley');
			new WCF.Message.Smilies('text');
		};
	</script>
</div>
