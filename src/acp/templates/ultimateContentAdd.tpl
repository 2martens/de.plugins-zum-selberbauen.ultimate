{capture assign='pageTitle'}{lang}wcf.acp.ultimate.content.{@$action}{/lang}{/capture}
{include file='header' application='ultimate'}

{include file='multipleLanguageInputJavascript' elementIdentifier='subject' forceSelection=false}
{include file='multipleLanguageInputJavascript' elementIdentifier='description' forceSelection=false}
{include file='multipleLanguageInputJavascript' elementIdentifier='text' forceSelection=false}
<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.content.{@$action}{/lang}</h1>
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
			<li><a href="{link application='ultimate' controller='UltimateContentList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.content.list{/lang}" class="button"><span class="icon icon24 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.content.list{/lang}</span></a></li>
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='ultimate' controller='UltimateContentAdd'}{/link}{else}{link application='ultimate' controller='UltimateContentEdit'}{/link}{/if}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.content.general{/lang}</legend>
			<dl{if $errorField == 'subject'} class="formError"{/if}>
				<dt><label for="subject">{lang}wcf.acp.ultimate.content.title{/lang}</label></dt>
				<dd>
					<input type="text" id="subject" name="subject" value="{@$i18nPlainValues['subject']}" class="long" required="required" placeholder="{lang}wcf.acp.ultimate.content.title.placeholder{/lang}" pattern=".{literal}{{/literal}4,{literal}}{/literal}" />
					{if $errorField == 'subject'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.content.title.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'description'} class="formError"{/if}>
				<dt><label for="description">{lang}wcf.acp.ultimate.content.description{/lang}</label></dt>
				<dd>
					<input type="text" id="description" name="description" value="{@$i18nPlainValues['description']}" class="long" required="required" placeholder="{lang}wcf.acp.ultimate.content.description.placeholder{/lang}" pattern=".{literal}{{/literal}4,{literal}}{/literal}" />
					{if $errorField == 'description'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.content.description.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'slug'} class="formError"{/if}>
				<dt><label for="slug">{lang}wcf.acp.ultimate.content.slug{/lang}</label></dt>
				<dd>
					<input type="text" id="slug" name="slug" value="{@$slug}" class="long" required="required" pattern="^[a-z]+(?:\-{literal}{{/literal}1{literal}}{/literal}[a-z]+)*(?:\/{literal}{{/literal}1{literal}}{/literal}[a-z]+(?:\-{literal}{{/literal}1{literal}}{/literal}[a-z]+)*)*$" placeholder="{lang}wcf.acp.ultimate.content.slug.placeholder{/lang}" />
					{if $errorField == 'slug'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.content.slug.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			{include file='metaInput' application='ultimate' metaDescription=$metaDescription metaKeywords=$metaKeywords errorField=$errorField errorType=$errorType}
			<dl{if $errorField == 'category'} class="formError"{/if}>
				<dt><label>{lang}wcf.acp.ultimate.content.categories{/lang}</label></dt>
				<dd>
					{htmlCheckboxes options=$categories name=categoryIDs selected=$categoryIDs}
					{if $errorField == 'category'}
						<small class="innerError">
							{lang}wcf.acp.ultimate.content.categories.error.{@$errorType}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
			
			{* WCF Tagging *}
			<!--<div id="tagContainerContainer">-->
			{foreach from=$availableLanguages key=languageID item=languageName}
				{if $tagsI18n[$languageID]|isset}
					{include file='tagInput' application='ultimate' tags=$tagsI18n[$languageID] languageID=$languageID tagInputSuffix=$languageID}
				{else}
					{include file='tagInput' application='ultimate' languageID=$languageID tagInputSuffix=$languageID}
				{/if}
			{/foreach}
			<dl id="tagContainerReal" class="jsOnly">
				<dd>
					<input id="tagSearchInputReal" type="text" value="" class="long" />
					<small>{lang}wcf.tagging.tags.description{/lang}</small>
				</dd>
			</dl>
			<!--</div>-->
			
			<script type="text/javascript">
			/* <![CDATA[ */
				$(function() {
					var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
					var $optionValues = { {implode from=$tagsI18n key=languageID item=value}'{@$languageID}': "{$value}"{/implode} };
					new ULTIMATE.Tagging.MultipleLanguageInput('tagContainer', 'tagSearchInput', true, $optionValues, $availableLanguages);
				});
			/* ]]> */
			</script>
			{* end WCF Tagging *}
			
			<dl{if $errorField == 'text'} class="formError"{/if}>
				<dt><label for="text">{lang}wcf.acp.ultimate.content.text{/lang}</label></dt>
				<dd>
					<textarea id="text" name="text" rows="15" cols="40" class="long" {* placeholder="{lang}wcf.acp.ultimate.content.text.placeholder{/lang}"*} >{@$i18nPlainValues['text']}</textarea>
					
					{if $errorField == 'text'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.content.text.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
					{include file='messageFormTabs'}
				</dd>
			</dl>
		</fieldset>
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.publish{/lang}</legend>
			<dl{if $errorField == 'status'} class="formError"{/if}>
				<dt><label for="statusSelect">{lang}wcf.acp.ultimate.status{/lang}</label></dt>
				<dd>
					<select id="statusSelect" name="status">
					{htmloptions options=$statusOptions selected=$statusID}
					</select>
					<script type="text/javascript">
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
				<dt><label for="selectVisibility">{lang}wcf.acp.ultimate.visibility{/lang}</label></dt>
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
					<script type="text/javascript">
					/* <![CDATA[ */
					$(function() {
						$('#selectVisibility').change(function () {
							var selectedIndex = $('#selectVisibility option:selected').attr('value');
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
					<small>{lang}wcf.acp.ultimate.content.visibility.description{/lang}</small>
				</dd>
			</dl>
			<dl{if $errorField == 'publishDate'} class="formError"{/if}>
				<dt><label for="publishDateInput">{lang}wcf.acp.ultimate.publishDate{/lang}</label></dt>
				<dd>
					<input type="datetime" id="publishDateInput" name="publishDate" value="{@$publishDate}" readonly="readonly" class="medium" />
					<script type="text/javascript">
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
		<input type="hidden" name="action" value="{@$action}" />
		{if $contentID|isset}<input type="hidden" name="id" value="{@$contentID}" />{/if}
	</div>
</form>

{include file='footer'}
{include file='wysiwyg'}