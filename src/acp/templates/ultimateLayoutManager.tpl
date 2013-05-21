{include file='header'}

<script type="text/javascript">
	/* <![CDATA[ */
	$(function() {
		WCF.TabMenu.init();
	});
	/* ]]> */
</script>

<header class="boxHeadline">
	<hgroup>
		<h1>{lang}wcf.acp.ultimate.layoutManager{/lang}</h1>
	</hgroup>
</header>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.edit{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			{event name='largeButtons'}
		</ul>
	</nav>
</div>

{if $templates|count}
<form method="post" action="{link application='ultimate' controller='UltimateLayoutManager'}{/link}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.layoutManager.layouts{/lang}</legend>
			<dl{if $errorField == 'layout1'} class="formError"{/if}>
				<dt><label for="layout1">{lang}wcf.acp.ultimate.layoutManager.index{/lang}</label></dt>
				<dd>
					<select id="layout1">
						<option label="{lang}wcf.acp.ultimate.layoutManager.selectTemplate.none{/lang}" value="0">{lang}wcf.acp.ultimate.layoutManager.selectTemplate.none{/lang}</option>
						{if $layouts[1]->template|isset}
						{htmlOptions options=$templates selected=$layouts[1]->template->templateID}
						{else}
						{htmlOptions options=$templates}
						{/if}
					</select>
					{if $errorField == 'layout1'}
						<small class="innerError">
							{lang}wcf.acp.ultimate.layoutManager.selectTemplate.error.{@$errorType}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'layout2'} class="formError"{/if}>
				<dt><label for="layout2">{lang}wcf.acp.ultimate.layoutManager.category{/lang}</label></dt>
				<dd>
					<select id="layout2">
						<option label="{lang}wcf.acp.ultimate.layoutManager.selectTemplate.none{/lang}" value="0">{lang}wcf.acp.ultimate.layoutManager.selectTemplate.none{/lang}</option>
						{if $layouts[2]->template|isset}
						{htmlOptions options=$templates selected=$layouts[2]->template->templateID}
						{else}
						{htmlOptions options=$templates}
						{/if}
					</select>
					{if $errorField == 'layout2'}
						<small class="innerError">
							{lang}wcf.acp.ultimate.layoutManager.selectTemplate.error.{@$errorType}{/lang}
						</small>
					{/if}
					<dl{if $errorField == 'layout2-child-layout' || $errorField == 'layout2-child-template'} class="formError"{/if}>
						<dt>
							<label for="layout2-child-layout">{lang}wcf.acp.ultimate.layoutManager.childLayout{/lang}</label>
							<label for="layout2-child-template">{lang}wcf.acp.ultimate.layoutManager.childTemplate{/lang}</label>
						</dt>
						<dd>
							<select id="layout2-child-layout">
								<option label="{lang}wcf.acp.ultimate.layoutManager.selectLayout.none{/lang}" value="0">{lang}wcf.acp.ultimate.layoutManager.selectLayout.none{/lang}</option>
								{htmlOptions options=$categoryLayouts}
							</select>
							{if $errorField == 'layout2-child-layout'}
								<small class="innerError">
									{lang}wcf.acp.ultimate.layoutManager.selectLayout.error.{@$errorType}{/lang}
								</small>
							{/if}
							
							<select id="layout2-child-template">
								<option label="{lang}wcf.acp.ultimate.layoutManager.selectTemplate.inherit{/lang}" value="0">{lang}wcf.acp.ultimate.layoutManager.selectTemplate.inherit{/lang}</option>
								{htmlOptions options=$templates}
							</select>
							{if $errorField == 'layout2-child-template'}
								<small class="innerError">
									{lang}wcf.acp.ultimate.layoutManager.selectTemplate.error.{@$errorType}{/lang}
								</small>
							{/if}
						</dd>
					</dl>
				</dd>
			</dl>
			<dl{if $errorField == 'layout3'} class="formError"{/if}>
				<dt><label for="layout3">{lang}wcf.acp.ultimate.layoutManager.content{/lang}</label></dt>
				<dd>
					<select id="layout3">
						<option label="{lang}wcf.acp.ultimate.layoutManager.selectTemplate.none{/lang}" value="0">{lang}wcf.acp.ultimate.layoutManager.selectTemplate.none{/lang}</option>
						{if $layouts[3]->template|isset}
						{htmlOptions options=$templates selected=$layouts[3]->template->templateID}
						{else}
						{htmlOptions options=$templates}
						{/if}
					</select>
					{if $errorField == 'layout3'}
						<small class="innerError">
							{lang}wcf.acp.ultimate.layoutManager.selectTemplate.error.{@$errorType}{/lang}
						</small>
					{/if}
					<dl{if $errorField == 'layout3-child-layout' || $errorField == 'layout3-child-template'} class="formError"{/if}>
						<dt>
							<label for="layout3-child-layout">{lang}wcf.acp.ultimate.layoutManager.childLayout{/lang}</label>
							<label for="layout3-child-template">{lang}wcf.acp.ultimate.layoutManager.childTemplate{/lang}</label>
						</dt>
						<dd>
							<select id="layout3-child-layout">
								<option label="{lang}wcf.acp.ultimate.layoutManager.selectLayout.none{/lang}" value="0">{lang}wcf.acp.ultimate.layoutManager.selectLayout.none{/lang}</option>
								{htmlOptions options=$contentLayouts}
							</select>
							{if $errorField == 'layout3-child-layout'}
								<small class="innerError">
									{lang}wcf.acp.ultimate.layoutManager.selectLayout.error.{@$errorType}{/lang}
								</small>
							{/if}
							
							<select id="layout3-child-template">
								<option label="{lang}wcf.acp.ultimate.layoutManager.selectTemplate.inherit{/lang}" value="0">{lang}wcf.acp.ultimate.layoutManager.selectTemplate.inherit{/lang}</option>
								{htmlOptions options=$templates}
							</select>
							{if $errorField == 'layout3-child-template'}
								<small class="innerError">
									{lang}wcf.acp.ultimate.layoutManager.selectTemplate.error.{@$errorType}{/lang}
								</small>
							{/if}
						</dd>
					</dl>
				</dd>
			</dl>
			<dl{if $errorField == 'layout4'} class="formError"{/if}>
				<dt><label for="layout4">{lang}wcf.acp.ultimate.layoutManager.page{/lang}</label></dt>
				<dd>
					<select id="layout4">
						<option label="{lang}wcf.acp.ultimate.layoutManager.selectTemplate.none{/lang}" value="0">{lang}wcf.acp.ultimate.layoutManager.selectTemplate.none{/lang}</option>
						{if $layouts[4]->template|isset}
						{htmlOptions options=$templates selected=$layouts[4]->template->templateID}
						{else}
						{htmlOptions options=$templates}
						{/if}
					</select>
					{if $errorField == 'layout4'}
						<small class="innerError">
							{lang}wcf.acp.ultimate.layoutManager.selectTemplate.error.{@$errorType}{/lang}
						</small>
					{/if}
					<dl{if $errorField == 'layout4-child-layout' || $errorField == 'layout4-child-template'} class="formError"{/if}>
						<dt>
							<label for="layout4-child-layout">{lang}wcf.acp.ultimate.layoutManager.childLayout{/lang}</label>
							<label for="layout4-child-template">{lang}wcf.acp.ultimate.layoutManager.childTemplate{/lang}</label>
						</dt>
						<dd>
							<select id="layout4-child-layout">
								<option label="{lang}wcf.acp.ultimate.layoutManager.selectLayout.none{/lang}" value="0">{lang}wcf.acp.ultimate.layoutManager.selectLayout.none{/lang}</option>
								{htmlOptions options=$pageLayouts}
							</select>
							{if $errorField == 'layout4-child-layout'}
								<small class="innerError">
									{lang}wcf.acp.ultimate.layoutManager.selectLayout.error.{@$errorType}{/lang}
								</small>
							{/if}
							
							<select id="layout4-child-template">
								<option label="{lang}wcf.acp.ultimate.layoutManager.selectTemplate.inherit{/lang}" value="0">{lang}wcf.acp.ultimate.layoutManager.selectTemplate.inherit{/lang}</option>
								{htmlOptions options=$templates}
							</select>
							{if $errorField == 'layout4-child-template'}
								<small class="innerError">
									{lang}wcf.acp.ultimate.layoutManager.selectTemplate.error.{@$errorType}{/lang}
								</small>
							{/if}
						</dd>
					</dl>
				</dd>
			</dl>
		</fieldset>
		{event name='fieldsets'}
	</div>
	<div class="formSubmit">
		<input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SID_INPUT_TAG}
		{foreach from=$layouts key=layoutID item=layout}
			<input type="hidden" id="layout{$layoutID}-hidden" name="layout{$layoutID}" value="{if $layout->template|isset}{$layout->template->templateID}{else}0{/if}" />
		{/foreach}
	</div>
</form>

<script type="text/javascript">
	/* <![CDATA[ */
		$(function() {
			$('#layout1').change(function(event) {
				var $templateID = $('#layout1').val();
				$('#layout1-hidden').val($templateID);
			});
			$('#layout2').change(function(event) {
				var $templateID = $('#layout2').val();
				$('#layout2-hidden').val($templateID);
			});
			$('#layout2-child-layout').change(function(event) {
				var $layoutID = $('#layout2-child-layout').val();
				var $templateID = $('#layout' + $layoutID + '-hidden').val();
				$('#layout2-child-template').val($templateID);
			});
			$('#layout2-child-template').change(function(event) {
				var $templateID = $('#layout2-child-template').val();
				var $layoutID = $('#layout2-child-layout').val();
				$('#layout' + $layoutID + '-hidden').val($templateID);
			});
			$('#layout3').change(function(event) {
				var $templateID = $('#layout3').val();
				$('#layout3-hidden').val($templateID);
			});
			$('#layout3-child-layout').change(function(event) {
				var $layoutID = $('#layout3-child-layout').val();
				var $templateID = $('#layout' + $layoutID + '-hidden').val();
				$('#layout3-child-template').val($templateID);
			});
			$('#layout3-child-template').change(function(event) {
				var $templateID = $('#layout3-child-template').val();
				var $layoutID = $('#layout3-child-layout').val();
				$('#layout' + $layoutID + '-hidden').val($templateID);
			});
			$('#layout4').change(function(event) {
				var $templateID = $('#layout4').val();
				$('#layout4-hidden').val($templateID);
			});
			$('#layout4-child-layout').change(function(event) {
				var $layoutID = $('#layout4-child-layout').val();
				var $templateID = $('#layout' + $layoutID + '-hidden').val();
				$('#layout4-child-template').val($templateID);
			});
			$('#layout4-child-template').change(function(event) {
				var $templateID = $('#layout4-child-template').val();
				var $layoutID = $('#layout4-child-layout').val();
				$('#layout' + $layoutID + '-hidden').val($templateID);
			});
		});
	/* ]]> */
</script>
{else}
<p id="errorMessage" class="error">
	{lang}ultimate.error.noExistingTemplates{/lang}
</p>
{/if}

{include file='footer'}