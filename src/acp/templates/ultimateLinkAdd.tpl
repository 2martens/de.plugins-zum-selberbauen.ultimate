{include file='header'}

<script type="text/javascript">
	/* <![CDATA[ */
	$(function() {
		WCF.TabMenu.init();
	});
	/* ]]> */
</script>
{include file='multipleLanguageInputJavascript' elementIdentifier='linkName'}
{include file='multipleLanguageInputJavascript' elementIdentifier='linkDescription'}
<header class="boxHeadline">
	<hgroup>
		<h1>{lang}wcf.acp.ultimate.link.{@$action}{/lang}</h1>
	</hgroup>
</header>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{lang}wcf.global.form.{@$action}.success{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='UltimateLinkList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.link.list{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/list.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.menu.link.ultimate.link.list{/lang}</span></a></li>
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='UltimateLinkAdd'}{/link}{else}{link controller='UltimateLinkEdit'}{/link}{/if}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.link.general{/lang}</legend>
			<dl{if $errorField == 'linkName'} class="wcf-formError"{/if}>
				<dt><label for="linkName">{lang}wcf.acp.ultimate.link.name{/lang}</label></dt>
				<dd>
					<input type="text" id="linkName" name="linkName" value="{$i18nPlainValues['linkName']}" placeholder="{lang}wcf.acp.ultimate.link.name.placeholder{/lang}" required="required" class="long" />
					{if $errorField == 'linkName'}
						<small class="wcf-innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.link.name.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'linkURL'} class="wcf-formError"{/if}>
				<dt><label for="linkURL">{lang}wcf.acp.ultimate.link.url{/lang}</label></dt>
				<dd>
					<input type="url" id="linkURL" name="linkURL" value="{@$linkURL}" required="required" placeholder="{lang}wcf.acp.ultimate.link.url.placeholder{/lang}" class="long" />
					{if $errorField == 'linkURL'}
						<small class="wcf-innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.link.url.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'linkDescription'} class="wcf-formError"{/if}>
				<dt><label for="linkDescription">{lang}wcf.acp.ultimate.link.description{/lang}</label></dt>
				<dd>
					<input type="text" id="linkDescription" name="linkDescription" value="{$i18nPlainValues['linkDescription']}" placeholder="{lang}wcf.acp.ultimate.link.description.placeholder{/lang}" required="required" class="long" />
					{if $errorField == 'linkDescription'}
						<small class="wcf-innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.link.description.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl {if $errorField == 'category'} class="wcf-formError"{/if}>
				<dt><label>{lang}wcf.acp.ultimate.link.categories{/lang}</label></dt>
				<dd>
					{htmlCheckboxes options=$categories name=categoryIDs selected=$categoryIDs}
					{if $errorField == 'category'}
						<small class="wcf-innerError">
							{lang}wcf.acp.ultimate.link.categories.error.{@$errorType}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
		</fieldset>
		{event name='fieldsets'}
	</div>
	
	<div class="formSubmit">
		<input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SID_INPUT_TAG}
		<input type="hidden" name="action" value="{@$action}" />
		{if $linkID|isset}<input type="hidden" name="id" value="{@$linkID}" />{/if}
	</div>
</form>

{include file='footer'}