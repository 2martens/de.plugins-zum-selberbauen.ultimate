<div class="messageInlineEditor">
	<label for="messageEditor{@$content->contentID}"><textarea id="messageEditor{@$content->contentID}" rows="20" cols="40">{@$content->contentText}</textarea></label>
	
	<div class="formSubmit">
		<button class="buttonPrimary" data-type="save">{lang}wcf.global.button.submit{/lang}</button>
		<button data-type="cancel">{lang}wcf.global.button.cancel{/lang}</button>
	</div>
	
	{include file='wysiwyg'}
</div>
