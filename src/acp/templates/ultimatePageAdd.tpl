{capture assign='pageTitle'}{lang}wcf.acp.ultimate.page.{@$action}{/lang}{/capture}
{include file='header' application='ultimate'}

<script data-relocate="true" type="text/javascript">
	/* <![CDATA[ */
	$(function() {
		WCF.TabMenu.init();
	});
	/* ]]> */
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.page.{@$action}{/lang}</h1>
</header>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link application='ultimate' controller='UltimatePageList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.page.list{/lang}" class="button"><span class="icon icon24 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.page.list{/lang}</span></a></li>
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='ultimate' controller='UltimatePageAdd'}{/link}{else}{link controller='UltimatePageEdit'}{/link}{/if}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.page.general{/lang}</legend>
			<dl{if $errorField == 'pageTitle'} class="formError"{/if}>
				<dt><label for="pageTitle">{lang}wcf.acp.ultimate.page.title{/lang}</label></dt>
				<dd>
					<script data-relocate="true" type="text/javascript">
					//<![CDATA[
						$(function() {
							var $availableLanguages = {literal}{{/literal} {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} {literal}}{/literal};
							var $optionValues = {literal}{{/literal} {implode from=$i18nValues['pageTitle'] key=languageID item=value}'{@$languageID}': '{$value}'{/implode} {literal}}{/literal};
							new WCF.MultipleLanguageInput('pageTitle', false, $optionValues, $availableLanguages);
						});
					//]]>
					</script>
					<input type="text" id="pageTitle" name="pageTitle" value="{$i18nPlainValues['pageTitle']}" placeholder="{lang}wcf.acp.ultimate.page.title.placeholder{/lang}" required="required" class="long" />
					{if $errorField == 'pageTitle'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.page.title.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'pageSlug'} class="formError"{/if}>
				<dt><label for="pageSlug">{lang}wcf.acp.ultimate.page.slug{/lang}</label></dt>
				<dd>
					<input type="text" id="pageSlug" name="pageSlug" value="{@$pageSlug}" required="required" placeholder="{lang}wcf.acp.ultimate.page.slug.placeholder{/lang}" pattern="^[a-z]+(?:\-{literal}{{/literal}1{literal}}{/literal}[a-z]+)*(?:\/{literal}{{/literal}1{literal}}{/literal}[a-z]+(?:\-{literal}{{/literal}1{literal}}{/literal}[a-z]+)*)*$" class="long" />
					{if $errorField == 'pageSlug'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.page.slug.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			{include file='metaInput' application='ultimate' metaDescription=$metaDescription metaKeywords=$metaKeywords errorField=$errorField errorType=$errorType}
			<dl{if $errorField == 'pageParent'} class="formError"{/if}>
				<dt><label for="pageParent">{lang}wcf.acp.ultimate.page.parent{/lang}</label></dt>
				<dd>
					<select name="pageParent">
					<option value="0">{lang}wcf.acp.ultimate.page.parent.none{/lang}</option>
					{ultimateHtmloptions options=$pages selected=$pageParent}
					</select>
					{if $errorField == 'pageParent'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.page.parent.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'content'} class="formError"{/if}>
				<dt><label for="content">{lang}wcf.acp.ultimate.page.content{/lang}</label></dt>
				<dd>
					<select name="content">
						<option value="0">{lang}wcf.acp.ultimate.page.content.select{/lang}</option>
						{ultimateHtmloptions options=$contents selected=$contentID}
					</select>
					{if $errorField == 'content'}
						<small class="innerError">
							{lang}wcf.acp.ultimate.page.content.error.{@$errorType}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
		</fieldset>
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.publish{/lang}</legend>
			<dl{if $errorField == 'status'} class="formError"{/if}>
				<dt><label for="status">{lang}wcf.acp.ultimate.status{/lang}</label></dt>
				<dd>
					<select id="statusSelect" name="status">
					{htmloptions options=$statusOptions selected=$statusID}
					</select>
					<script data-relocate="true" type="text/javascript">
					/* <![CDATA[ */
						$(function() {
							new ULTIMATE.ACP.Button.Replacement('saveButton', 'statusSelect', 'save');
						});
					/* ]]> */
					</script>
					{if $errorField == 'status'}
						<small class="innerError">
							{lang}wcf.acp.ultimate.status.error.{@$errorType}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'visibility'} class="formError"{/if}>
				<dt><label for="visibility">{lang}wcf.acp.ultimate.visibility{/lang}</label></dt>
				<dd>
					<select id="selectVisibility" name="visibility">
					<option value="public"{if $visibility == 'public'} selected="selected"{/if}>{lang}wcf.acp.ultimate.visibility.public{/lang}</option>
					<option value="protected"{if $visibility == 'protected'} selected="selected"{/if}>{lang}wcf.acp.ultimate.visibility.protected{/lang}</option>
					<option value="private"{if $visibility == 'private'} selected="selected"{/if}>{lang}wcf.acp.ultimate.visibility.private{/lang}</option>
					</select>
					<dl id="groupCheckboxes" class="container containerPadding marginTop"{if $visibility != 'protected'} style="display: none;"{/if}>
						<dt><label>{lang}wcf.acp.ultimate.visibility.groupIDs{/lang}</label></dt>
						<dd>
							{htmlcheckboxes name="groupIDs" options=$groups selected=$groupIDs}
							{if $errorField == 'groupIDs'}
								<small class="innerError">
									{lang}wcf.acp.ultimate.visibility.groupIDs.error.{@$errorType}{/lang}
								</small>
							{/if}
						</dd>
					</dl>
					<script data-relocate="true" type="text/javascript">
					/* <![CDATA[ */
					$(function() {
						$('#selectVisibility').change(function () {
							var selectedIndex = $('#selectVisibility').val();
							if (selectedIndex == 'protected') {
								$('#groupCheckboxes').fadeIn(1000, 'swing');
							} else {
								$('#groupCheckboxes').fadeOut(1000, 'swing');
							}
						});
					});
					/* ]]> */
					</script>
					{if $errorField == 'visibility'}
						<small class="innerError">
							{lang}wcf.acp.ultimate.visibility.error.{@$errorType}{/lang}
						</small>
					{/if}
					<small>{lang}wcf.acp.ultimate.page.visibility.description{/lang}</small>
				</dd>
			</dl>
			<dl{if $errorField == 'publishDate'} class="formError"{/if}>
				<dt><label for="publishDate">{lang}wcf.acp.ultimate.publishDate{/lang}</label></dt>
				<dd>
					<input type="datetime" id="publishDateInput" name="publishDate" value="{@$publishDate}" readonly="readonly" class="medium" />
					<script data-relocate="true" type="text/javascript">
					/* <![CDATA[*/
					$(function() {
						//ULTIMATE.Date.Picker.init();
						new ULTIMATE.ACP.Button.Replacement('publishButton', 'publishDateInput', 'publish');
					});
					/* ]]> */
					</script>
					
					{if $errorField == 'publishDate'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.publishDate.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
		</fieldset>
		{event name='fieldsets'}
	</div>
	
	<div class="formSubmit">
		<input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
		<input type="submit"{if $disableSaveButton|isset && $disableSaveButton} class="ultimateHidden" disabled="disabled"{/if} name="save" id="saveButton" value="{if $saveButtonLang|isset}{@$saveButtonLang}{else}{lang}ultimate.button.saveAsDraft{/lang}{/if}" />
		<input type="submit" name="publish" id="publishButton" value="{if $publishButtonLang|isset}{@$publishButtonLang}{else}{lang}ultimate.button.publish{/lang}{/if}" accesskey="s" />
		{@SID_INPUT_TAG}
		<input type="hidden" name="startTime" value="{@$startTime}" />
		<input type="hidden" name="action" value="{@$action}" />
		{if $pageID|isset}<input type="hidden" name="id" value="{@$pageID}" />{/if}
	</div>
</form>

{include file='footer'}