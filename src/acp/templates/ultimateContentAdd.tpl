{include file='header'}
{include file='wysiwyg'} 

{include file='multipleLanguageInputJavascript' elementIdentifier='subject'}
{include file='multipleLanguageInputJavascript' elementIdentifier='description'}
{include file='multipleLanguageInputJavascript' elementIdentifier='text'}
<header class="boxHeadline">
	<hgroup>
		<h1>{lang}wcf.acp.ultimate.content.{@$action}{/lang}</h1>
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
			<li><a href="{link controller='UltimateContentList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.content.list{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/list.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.menu.link.ultimate.content.list{/lang}</span></a></li>
			
			{event name='largeButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='UltimateContentAdd'}{/link}{else}{link controller='UltimateContentEdit'}{/link}{/if}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.content.general{/lang}</legend>
			<dl{if $errorField == 'subject'} class="wcf-formError"{/if}>
				<dt><label for="subject">{lang}wcf.acp.ultimate.content.title{/lang}</label></dt>
				<dd>
					<input type="text" id="subject" name="subject" value="{@$i18nPlainValues['subject']}" class="long" required="required" placeholder="{lang}wcf.acp.ultimate.content.title.placeholder{/lang}" />
					{if $errorField == 'subject'}
						<small class="wcf-innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.content.title.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'description'} class="wcf-formError"{/if}>
				<dt><label for="description">{lang}wcf.acp.ultimate.content.description{/lang}</label></dt>
				<dd>
					<input type="text" id="description" name="description" value="{@$i18nPlainValues['description']}" class="long" required="required" placeholder="{lang}wcf.acp.ultimate.content.description.placeholder{/lang}" />
					{if $errorField == 'description'}
						<small class="wcf-innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.content.description.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'slug'} class="wcf-formError"{/if}>
				<dt><label for="slug">{lang}wcf.acp.ultimate.content.slug{/lang}</label></dt>
				<dd>
					<input type="text" id="slug" name="slug" value="{@$slug}" class="long" required="required" pattern="^[a-z]+(?:\-{literal}{{/literal}1{literal}}{/literal}[a-z]+)*(?:\/{literal}{{/literal}1{literal}}{/literal}[a-z]+(?:\-{literal}{{/literal}1{literal}}{/literal}[a-z]+)*)*$" placeholder="{lang}wcf.acp.ultimate.content.slug.placeholder{/lang}" />
					{if $errorField == 'slug'}
						<small class="wcf-innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.content.slug.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl {if $errorField == 'category'} class="wcf-formError"{/if}>
				<dt><label>{lang}wcf.acp.ultimate.content.categories{/lang}</label></dt>
				<dd>
					{htmlCheckboxes options=$categories name=categoryIDs selected=$categoryIDs}
					{if $errorField == 'category'}
						<small class="wcf-innerError">
							{lang}wcf.acp.ultimate.content.categories.error.{@$errorType}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
			<dl {if $errorField == 'tags'} class="wcf-formError"{/if}>
				<dt><label for="tags">{lang}wcf.acp.ultimate.content.tags{/lang}</label></dt>
				<dd>
					<script type="text/javascript">
					/* <![CDATA[ */
						$(function() {
							var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
							var $optionValues = { {implode from=$tagsI18n key=languageID item=value}'{@$languageID}': "{$value}"{/implode} };
							new WCF.MultipleLanguageInput('tags', true, $optionValues, $availableLanguages);
							var $availableTags = { };
							{foreach from=$availableLanguages key=languageID item=languageName}
								$availableTags[{$languageID}] = [ {implode from=$availableTags[$languageID] item=tag}"{@$tag->name}"{/implode} ];
							{/foreach}
							function split( val ) {
								return val.split( /,\s*/ );
							}
							function extractLast( term ) {
								return split( term ).pop();
							}
							$('#tags').bind( "keydown", function( event ) {
								if ( event.keyCode === $.ui.keyCode.TAB &&
									$( this ).data( "autocomplete" ).menu.active ) {
									event.preventDefault();
								}
							})
							.autocomplete({
								minLength: 1,
								source: function( request, response ) {
									// delegate back to autocomplete, but extract the last term
									var $currentLanguageID = $('#wcf3 > .dropdownMenu > .active').data('languageID');
									response( $.ui.autocomplete.filter(
										$availableTags[$currentLanguageID], extractLast( request.term ) ) );
								},
								focus: function() {
									// prevent value inserted on focus
									return false;
								},
								select: function( event, ui ) {
									var terms = split( this.value );
									// remove the current input
									terms.pop();
									// add the selected item
									terms.push( ui.item.value );
									// add placeholder to get the comma-and-space at the end
									terms.push( "" );
									this.value = terms.join( ", " );
									return false;
								}
							});
						});
					/* ]]> */
					</script>
					<input type="text" name="tags" id="tags" class="long" value="{@$tags}" />
					{if $errorField == 'tags'}
						<small class="wcf-innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.content.tags.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			
			<dl{if $errorField == 'text'} class="wcf-formError"{/if}>
				<dt><label for="text">{lang}wcf.acp.ultimate.content.text{/lang}</label></dt>
				<dd>
					<textarea id="text" name="text" rows="15" cols="40" class="long" {* placeholder="{lang}wcf.acp.ultimate.content.text.placeholder{/lang}"*} >{@$i18nPlainValues['text']}</textarea>
					
					{if $errorField == 'text'}
						<small class="wcf-innerError">
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
			<dl{if $errorField == 'status'} class="wcf-formError"{/if}>
				<dt><label for="status">{lang}wcf.acp.ultimate.status{/lang}</label></dt>
				<dd>
					<select id="statusSelect" name="status">
					{htmloptions options=$statusOptions selected=$statusID}
					</select>
					<script type="text/javascript">
					/* <![CDATA[ */
					$(function() {
						new ULTIMATE.Button.Replacement('saveButton', 'statusSelect', 'save');
					});
					/* ]]> */
					</script>
					{if $errorField == 'status'}
						<small class="wcf-innerError">
							{lang}wcf.acp.ultimate.status.error.{@$errorType}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'visibility'} class="wcf-formError"{/if}>
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
								<small class="wcf-innerError">
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
						<small class="wcf-innerError">
							{lang}wcf.acp.ultimate.visibility.error.{@$errorType}{/lang}
						</small>
					{/if}
					<small>{lang}wcf.acp.ultimate.content.visibility.description{/lang}</small>
				</dd>
			</dl>
			<dl{if $errorField == 'publishDate'} class="wcf-formError"{/if}>
				<dt><label for="publishDate">{lang}wcf.acp.ultimate.publishDate{/lang}</label></dt>
				<dd>
					<input type="datetime" id="publishDateInput" name="publishDate" value="{@$publishDate}" readonly="readonly" class="medium" required="required" />
					<script type="text/javascript">
					/* <![CDATA[*/
					$(function() {
						
						$.timepicker.setDefaults( $.timepicker.regional[ "{if $__wcf->getLanguage()->languageCode == 'en'}en-GB{else}{@$__wcf->getLanguage()->languageCode}{/if}" ] );
						$.datepicker.setDefaults( $.datepicker.regional[ "{if $__wcf->getLanguage()->languageCode == 'en'}en-GB{else}{@$__wcf->getLanguage()->languageCode}{/if}" ] );
						$('#publishDateInput').datetimepicker( {
							showOn: 'focus',
							showOtherMonths: true,
							selectOtherMonths: true,
							showAnim: 'fadeIn',
							timeFormat: 'hh:mm'
						} );
						var $dateFormat = $('#publishDateInput').datetimepicker( 'option', 'dateFormat');
						$('#dateFormatInput').val($dateFormat);
						new ULTIMATE.Button.Replacement('publishButton', 'publishDateInput', 'publish');
						$('form').submit(function() {
							$('#publishDateInput').datetimepicker( 'option', 'dateFormat', 'yy-mm-dd' );
						});
					});
					/* ]]> */
					</script>
					
					{if $errorField == 'publishDate'}
						<small class="wcf-innerError">
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
		<input type="hidden" name="parseURL" value="1" />
		<input type="hidden" name="enableSmilies" value="1" />
		<input type="hidden" name="enableHtml" value="0" />
		<input type="hidden" name="enableBBCodes" value="1" />
		{if $contentID|isset}<input type="hidden" name="id" value="{@$contentID}" />{/if}
	</div>
</form>

{include file='footer'} 