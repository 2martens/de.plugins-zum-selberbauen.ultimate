{include file='header'}

<header class="boxHeadline">
	<hgroup>
		<h1>{lang}wcf.acp.ultimate.link.assignToCategory{/lang}</h1>
	</hgroup>
</header>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

<form method="post" action="{link controller='UltimateLinkAssignToCategory'}{/link}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.link.assignToCategory.markedLinks{/lang}</legend>
			
			<div>
				{implode from=$links item=$link}<a href="{link controller='UltimateLinkEdit' id=$link->linkID}{/link}">{$link}</a>{/implode}
			</div>
		</fieldset>
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.link.categories{/lang}</legend>
			
			<dl{if $errorField == 'categoryIDs'} class="wcf-formError"{/if}>
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
	</div>
</form>

{include file='footer'}
