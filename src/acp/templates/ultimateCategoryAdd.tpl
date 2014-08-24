{capture assign='pageTitle'}{lang}wcf.acp.ultimate.category.{@$action}{/lang}{/capture}
{include file='header'}

<script data-relocate="true" type="text/javascript">
	/* <![CDATA[ */
	$(function() {
		WCF.TabMenu.init();
	});
	/* ]]> */
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.category.{@$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link application='ultimate' controller='UltimateCategoryList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.category.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.category.list{/lang}</span></a></li>
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='ultimate' controller='UltimateCategoryAdd'}{/link}{else}{link application='ultimate' controller='UltimateCategoryEdit'}{/link}{/if}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.category.general{/lang}</legend>
			<dl{if $errorField == 'categoryTitle'} class="formError"{/if}>
				<dt><label for="categoryTitle">{lang}wcf.acp.ultimate.category.title{/lang}</label></dt>
				<dd>
					<script data-relocate="true" type="text/javascript">
					//<![CDATA[
						$(function() {
							var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
							var $optionValues = { {implode from=$i18nValues['categoryTitle'] key=languageID item=value}'{@$languageID}': '{$value}'{/implode} };
							new WCF.MultipleLanguageInput('categoryTitle', false, $optionValues, $availableLanguages);
						});
					//]]>
					</script>
					<input type="text" id="categoryTitle" name="categoryTitle" value="{@$i18nPlainValues['categoryTitle']}" class="long" required="required" placeholder="{lang}wcf.acp.ultimate.category.title.placeholder{/lang}" />
					{if $errorField == 'categoryTitle'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.category.title.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'categorySlug'} class="formError"{/if}>
				<dt><label for="categorySlug">{lang}wcf.acp.ultimate.category.slug{/lang}</label></dt>
				<dd>
					<input type="text" id="categorySlug" name="categorySlug" value="{@$categorySlug}" class="long" required="required" pattern="^[a-zA-Z]+(?:\-{literal}{{/literal}1{literal}}{/literal}[a-zA-Z0-9]+)*(?:\_{literal}{{/literal}1{literal}}{/literal}[a-zA-Z]+(?:\-{literal}{{/literal}1{literal}}{/literal}[a-zA-Z0-9]+)*)*$" placeholder="{lang}wcf.acp.ultimate.category.slug.placeholder{/lang}" />
					<small>
						{lang}wcf.acp.ultimate.category.slug.description{/lang}
					</small>
					{if $errorField == 'categorySlug'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.category.slug.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			{include file='metaInput' application='ultimate' metaDescription=$metaDescription metaKeywords=$metaKeywords errorField=$errorField errorType=$errorType}
			<dl{if $errorField == 'categoryParent'} class="formError"{/if}>
				<dt><label for="categoryParent">{lang}wcf.acp.ultimate.category.parent{/lang}</label></dt>
				<dd>
					<select id="categoryParent" name="categoryParent">
					<option value="0">{lang}wcf.acp.ultimate.category.parent.none{/lang}</option>
					{htmlOptions options=$categories selected=$categoryParent}
					</select>
					{if $errorField == 'categoryParent'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.category.parent.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'categoryDescription'} class="formError"{/if}>
				<dt><label for="categoryDescription">{lang}wcf.acp.ultimate.category.description{/lang}</label></dt>
				<dd>
					<script data-relocate="true" type="text/javascript">
					//<![CDATA[
						$(function() {
							var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
							var $optionValues = { {implode from=$i18nValues['categoryDescription'] key=languageID item=value}'{@$languageID}': '{$value}'{/implode} };
							new WCF.MultipleLanguageInput('categoryDescription', false, $optionValues, $availableLanguages);
						});
					//]]>
					</script>
					<input type="text" id="categoryDescription" name="categoryDescription" value="{@$i18nPlainValues['categoryDescription']}" class="long" placeholder="{lang}wcf.acp.ultimate.category.description.placeholder{/lang}" />
					{if $errorField == 'categoryDescription'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.category.description.error.{@$errorType}{/lang}
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
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SID_INPUT_TAG}
		<input type="hidden" name="action" value="{@$action}" />
		{if $categoryID|isset}<input type="hidden" name="id" value="{@$categoryID}" />{/if}
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
