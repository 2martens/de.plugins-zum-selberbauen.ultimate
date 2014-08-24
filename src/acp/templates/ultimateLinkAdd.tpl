{capture assign='pageTitle'}{lang}wcf.acp.ultimate.link.{@$action}{/lang}{/capture}
{include file='header' application='ultimate'}

<script data-relocate="true" type="text/javascript">
	/* <![CDATA[ */
	$(function() {
		WCF.TabMenu.init();
	});
	/* ]]> */
</script>
{include file='multipleLanguageInputJavascript' elementIdentifier='linkName' forceSelection=false}
{include file='multipleLanguageInputJavascript' elementIdentifier='linkDescription' forceSelection=false}
<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.link.{@$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link application='ultimate' controller='UltimateLinkList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.link.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.link.list{/lang}</span></a></li>
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='ultimate' controller='UltimateLinkAdd'}{/link}{else}{link application='ultimate' controller='UltimateLinkEdit'}{/link}{/if}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.link.general{/lang}</legend>
			<dl{if $errorField == 'linkName'} class="formError"{/if}>
				<dt><label for="linkName">{lang}wcf.acp.ultimate.link.name{/lang}</label></dt>
				<dd>
					<input type="text" id="linkName" name="linkName" value="{$i18nPlainValues['linkName']}" placeholder="{lang}wcf.acp.ultimate.link.name.placeholder{/lang}" required="required" class="long" />
					{if $errorField == 'linkName'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.link.name.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'linkURL'} class="formError"{/if}>
				<dt><label for="linkURL">{lang}wcf.acp.ultimate.link.url{/lang}</label></dt>
				<dd>
					<input type="url" id="linkURL" name="linkURL" value="{@$linkURL}" required="required" placeholder="{lang}wcf.acp.ultimate.link.url.placeholder{/lang}" class="long" />
					{if $errorField == 'linkURL'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.link.url.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'linkDescription'} class="formError"{/if}>
				<dt><label for="linkDescription">{lang}wcf.acp.ultimate.link.description{/lang}</label></dt>
				<dd>
					<input type="text" id="linkDescription" name="linkDescription" value="{$i18nPlainValues['linkDescription']}" placeholder="{lang}wcf.acp.ultimate.link.description.placeholder{/lang}" required="required" class="long" />
					{if $errorField == 'linkDescription'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.link.description.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl {if $errorField == 'category'} class="formError"{/if}>
				<dt><label>{lang}wcf.acp.ultimate.link.categories{/lang}</label></dt>
				<dd>
					{htmlCheckboxes options=$categories name=categoryIDs selected=$categoryIDs}
					{if $errorField == 'category'}
						<small class="innerError">
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
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
