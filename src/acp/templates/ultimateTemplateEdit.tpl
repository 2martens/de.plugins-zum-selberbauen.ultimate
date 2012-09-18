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
		<h1>{lang}wcf.acp.ultimate.template.{@$action}{/lang}</h1>
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
			<li><a href="{link controller='UltimateTemplateList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.appearance.template.list{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/list.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.menu.link.ultimate.appearance.template.list{/lang}</span></a></li>
			
			{event name='largeButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{link controller='UltimateTemplateEdit'}{/link}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.template.general{/lang}</legend>
			<dl{if $errorField == 'templateName'} class="wcf-formError"{/if}>
				<dt><label for="templateName">{lang}wcf.acp.ultimate.template.name{/lang}</label></dt>
				<dd>
					<input type="text" id="templateName" name="templateName" value="{$ultimateTemplateName}" placeholder="{lang}wcf.acp.ultimate.template.name.placeholder{/lang}" required="required" class="long" />
					{if $errorField == 'templateName'}
						<small class="wcf-innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.template.name.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'showWidgetArea'} class="wcf-formError"{/if}>
				<dt><label for="showWidgetArea">{lang}wcf.acp.ultimate.template.showWidgetArea{/lang}</label></dt>
				<dd>
					<input type="checkbox" id="showWidgetArea" name="showWidgetArea" value="{$showWidgetArea}"{if $showWidgetArea} checked="checked"{/if} />
					{if $errorField == 'showWidgetArea'}
						<small class="wcf-innerError">
							{lang}wcf.acp.ultimate.template.showWidgetArea.error.{@$errorType}{/lang}
						</small>
					{/if}
					<small>
						{lang}wcf.acp.ultimate.template.showWidgetArea.description{/lang}
					</small>
				</dd>
			</dl>
			<dl{if $errorField == 'widgetAreaSide'} class="wcf-formError"{/if}>
				<dt><label for="widgetAreaSide">{lang}wcf.acp.ultimate.template.widgetAreaSide{/lang}</label></dt>
				<dd>
					<select type="checkbox" id="widgetAreaSide" name="widgetAreaSide">
						<option label="{lang}wcf.acp.ultimate.template.widgetAreaSide.left{/lang}" value="left"{if $widgetAreaSide == 'left'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.widgetAreaSide.left{/lang}</option>
						<option label="{lang}wcf.acp.ultimate.template.widgetAreaSide.right{/lang}" value="right"{if $widgetAreaSide == 'right'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.widgetAreaSide.right{/lang}</option>
					</select>
					{if $errorField == 'widgetAreaSide'}
						<small class="wcf-innerError">
							{lang}wcf.acp.ultimate.template.widgetAreaSide.error.{@$errorType}{/lang}
						</small>
					{/if}
					<script type="text/javascript">
					/* <![CDATA[ */
					$(function() {
						var $initialValue = $('#showWidgetArea').val();
						if ($initialValue) {
							$('#widgetAreaSide').removeClass('disabled').prop('disabled', false);
						} else {
							$('#widgetAreaSide').addClass('disabled').prop('disabled', true);
						}
						$('#showWidgetArea').change(function(event) {
							var $value = $(this).val();
							if ($value) {
								$('#widgetAreaSide').removeClass('disabled').prop('disabled', false);
							} else {
								$('#widgetAreaSide').addClass('disabled').prop('disabled', true);
							}
						});
					});
					/* ]]> */
					</script>
				</dd>
			</dl>
			<dl{if $errorField == 'selectWidgetArea'} class="wcf-formError"{/if}>
				<dt><label for="selectWidgetArea">{lang}wcf.acp.ultimate.template.selectWidgetArea{/lang}</label></dt>
				<dd>
					<select id="selectWidgetArea" name="selectWidgetArea">
						<option label="{lang}wcf.acp.ultimate.template.selectWidgetArea.none{/lang}" value="0">{lang}wcf.acp.ultimate.template.selectWidgetArea.none{/lang}</option>
						{htmlOptions options=$widgetAreas selected=$selectedWidgetArea}
					</select>
					{if $errorField == 'selectWidgetArea'}
						<small class="wcf-innerError">
							{lang}wcf.acp.ultimate.template.selectWidgetArea.error.{@$errorType}{/lang}
						</small>
					{/if}
					<script type="text/javascript">
					/* <![CDATA[ */
					$(function() {
						var $initialValue = $('#showWidgetArea').val();
						if ($initialValue) {
							$('#selectWidgetArea').removeClass('disabled').prop('disabled', false);
						} else {
							$('#selectWidgetArea').addClass('disabled').prop('disabled', true);
						}
						$('#showWidgetArea').change(function(event) {
							var $value = $(this).val();
							if ($value) {
								$('#selectWidgetArea').removeClass('disabled').prop('disabled', false);
							} else {
								$('#selectWidgetArea').addClass('disabled').prop('disabled', true);
							}
						});
					});
					/* ]]> */
					</script>
				</dd>
			</dl>
			<dl{if $errorField == 'selectMenu'} class="wcf-formError"{/if}>
				<dt><label for="selectMenu">{lang}wcf.acp.ultimate.template.selectMenu{/lang}</label></dt>
				<dd>
					<select id="selectMenu" name="selectMenu">
						<option label="{lang}wcf.acp.ultimate.template.selectMenu.none{/lang}" value="0">{lang}wcf.acp.ultimate.template.selectMenu.none{/lang}</option>
						{htmlOptions options=$menus selected=$selectedMenu}
					</select>
					{if $errorField == 'selectMenu'}
						<small class="wcf-innerError">
							{lang}wcf.acp.ultimate.template.selectMenu.error.{@$errorType}{/lang}
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
		{if $templateID|isset}<input type="hidden" name="id" value="{@$templateID}" />{/if}
	</div>
</form>

{include file='footer'}