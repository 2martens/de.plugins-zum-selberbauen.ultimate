{capture assign='pageTitle'}{lang}wcf.acp.ultimate.link.assignToCategory{/lang}{/capture}
{include file='header' application='ultimate'}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.link.assignToCategory{/lang}</h1>
</header>

{include file='formError'}

<form method="post" action="{link application='ultimate' controller='UltimateLinkAssignToCategory'}{/link}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.link.assignToCategory.markedLinks{/lang}</legend>
			
			<div>
				{implode from=$links item=$link}<a href="{link controller='UltimateLinkEdit' id=$link->linkID}{/link}">{$link}</a>{/implode}
			</div>
		</fieldset>
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.link.categories{/lang}</legend>
			
			<dl{if $errorField == 'categoryIDs'} class="formError"{/if}>
				<dd>
					{htmlCheckboxes options=$categories name=categoryIDs selected=$categoryIDs}
					{if $errorField == 'categoryIDs'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.link.categories.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				<dd>
			</dl>
		</fieldset>
	</div>
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
