{include file='header'}

<header class="boxHeadline">
	<hgroup>
		<h1>{lang}wcf.acp.ultimate.content.assignToCategory{/lang}</h1>
	</hgroup>
</header>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

<form method="post" action="{link controller='UltimateContentAssignToCategory'}{/link}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.content.assignToCategory.markedContents{/lang}</legend>
			
			<div>
				{implode from=$contents item=$content}<a href="{link controller='UltimateContentEdit' id=$content->contentID}{/link}">{$content}</a>{/implode}
			</div>
		</fieldset>
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.content.categories{/lang}</legend>
			
			<dl{if $errorField == 'categoryIDs'} class="formError"{/if}>
				<dd>
					{htmlCheckboxes options=$categories name=categoryIDs selected=$categoryIDs}
					{if $errorField == 'categoryIDs'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.content.categories.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				<dd>
			</dl>
		</fieldset>
	</div>
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
	</div>
</form>

{include file='footer'}
