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

<form method="post" action="{if $action == "add"}{link controller='UltimateTemplateAdd'}{/link}{else}{link controller='UltimateTemplateEdit'}{/link}{/if}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.template.general{/lang}</legend>
			<dl{if $errorField == 'templateName'} class="formError"{/if}>
				<dt><label for="templateName">{lang}wcf.acp.ultimate.template.name{/lang}</label></dt>
				<dd>
					<input type="text" id="templateName" name="templateName" value="{@$ultimateTemplateName}" class="long" required="required" placeholder="{lang}wcf.acp.ultimate.template.name.placeholder{/lang}" />
					{if $errorField == 'templateName'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.template.name.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			<dl{if $errorField == 'showWidgetArea'} class="formError"{/if}>
				<dt><label for="showWidgetArea">{lang}wcf.acp.ultimate.template.showWidgetArea{/lang}</label></dt>
				<dd>
					<input type="checkbox" id="showWidgetArea" name="showWidgetArea" value="{$showWidgetArea}"{if $showWidgetArea} checked="checked"{/if} />
					{if $errorField == 'showWidgetArea'}
						<small class="innerError">
							{lang}wcf.acp.ultimate.template.showWidgetArea.error.{@$errorType}{/lang}
						</small>
					{/if}
					<small>
						{lang}wcf.acp.ultimate.template.showWidgetArea.description{/lang}
					</small>
				</dd>
			</dl>
			<dl{if $errorField == 'widgetAreaSide'} class="formError"{/if}>
				<dt><label for="widgetAreaSide">{lang}wcf.acp.ultimate.template.widgetAreaSide{/lang}</label></dt>
				<dd>
					<select id="widgetAreaSide" name="widgetAreaSide">
						<option label="{lang}wcf.acp.ultimate.template.widgetAreaSide.left{/lang}" value="left"{if $widgetAreaSide == 'left'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.widgetAreaSide.left{/lang}</option>
						<option label="{lang}wcf.acp.ultimate.template.widgetAreaSide.right{/lang}" value="right"{if $widgetAreaSide == 'right'} selected="selected"{/if}>{lang}wcf.acp.ultimate.template.widgetAreaSide.right{/lang}</option>
					</select>
					{if $errorField == 'widgetAreaSide'}
						<small class="innerError">
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
			<dl{if $errorField == 'selectWidgetArea'} class="formError"{/if}>
				<dt><label for="selectWidgetArea">{lang}wcf.acp.ultimate.template.selectWidgetArea{/lang}</label></dt>
				<dd>
					<select id="selectWidgetArea" name="selectWidgetArea">
						<option label="{lang}wcf.acp.ultimate.template.selectWidgetArea.none{/lang}" value="0">{lang}wcf.acp.ultimate.template.selectWidgetArea.none{/lang}</option>
						{htmlOptions options=$widgetAreas selected=$selectedWidgetArea}
					</select>
					{if $errorField == 'selectWidgetArea'}
						<small class="innerError">
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
			<dl{if $errorField == 'selectMenu'} class="formError"{/if}>
				<dt><label for="selectMenu">{lang}wcf.acp.ultimate.template.selectMenu{/lang}</label></dt>
				<dd>
					<select id="selectMenu" name="selectMenu">
						<option label="{lang}wcf.acp.ultimate.template.selectMenu.none{/lang}" value="0">{lang}wcf.acp.ultimate.template.selectMenu.none{/lang}</option>
						{htmlOptions options=$menus selected=$selectedMenu}
					</select>
					{if $errorField == 'selectMenu'}
						<small class="innerError">
							{lang}wcf.acp.ultimate.template.selectMenu.error.{@$errorType}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
		</fieldset>
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.template.blocks{/lang}</legend>
			<div id="templateBlockList" class="container containerPadding marginTop shadow">
				{if $action == 'edit'}
				<ol data-object-id="0">
					{assign var=oldDepth value=0}
					{foreach from=$blocks item=templateBlock}
						<li class="jsBlock" data-object-name="{@$templateBlock->blockTypeName}" data-object-id="{@$templateBlock->blockID}">
							<span>
								<span class="buttons">
									
									{if $__wcf->session->getPermission('admin.content.ultimate.canDeleteBlock')}
										<img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.global.button.delete{/lang}" class="icon16 jsDeleteButton jsTooltip" data-object-id="{@$templateBlock->blockID}" data-confirm-message="{lang}'wcf.acp.ultimate.block.delete.sure'{/lang}" />
									{else}
										<img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.global.button.delete{/lang}" class="icon16 disabled" />
									{/if}
									
									{event name='buttons'}
								</span>
								
								<span class="title">
									{@$templateBlock->blockTypeName} #{@$templateBlock->blockID}
								</span>
							</span>
						</li>
					{/foreach}
				</ol>
				{else}
					<p>{lang}wcf.acp.ultimate.template.addTemplateFirst{/lang}</p>
				{/if}
				{if $__wcf->session->getPermission('admin.content.ultimate.canEditBlock')}
					<div class="formSubmit">
						<button class="button default{if $action == 'add' || $blocks|count == 0} disabled" disabled="disabled{/if}" data-type="submit">{lang}wcf.global.button.save{/lang}</button>
					</div>
				{/if}
			</div>
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
<form method="post">
	<div id="blocktypeContainer" class="container containerPadding marginTop shadow{if $action == 'add'} disabled{/if}">
		<dl{if $errorField == 'selectBlocktype'} class="formError"{/if}>
			<dt><label for="selectBlocktype">{lang}wcf.acp.ultimate.template.selectBlocktype{/lang}</label></dt>
			<dd>
				<select id="selectBlocktype" name="selectBlocktype">
					<option label="{lang}wcf.acp.ultimate.template.selectBlocktype.none{/lang}" value="0">{lang}wcf.acp.ultimate.template.selectBlocktype.none{/lang}</option>
					{htmlOptions options=$blocktypes}
				</select>
				{if $errorField == 'selectBlocktype'}
					<small class="innerError">
						{lang}wcf.acp.ultimate.template.selectBlocktype.error.{@$errorType}{/lang}
					</small>
				{/if}
			</dd>
		</dl>
		<dl{if $errorField == 'height'} class="formError"{/if}>
			<dt><label for="height">{lang}wcf.acp.ultimate.template.height{/lang}</label></dt>
			<dd>
				<input type="number" id="height" name="height" value="{$height}" min="0" /><span class="unit">px</span>
				{if $errorField == 'height'}
					<small class="innerError">
						{lang}wcf.acp.ultimate.template.height.error.{@$errorType}{/lang}
					</small>
				{/if}
			</dd>
		</dl>
		<dl{if $errorField == 'width'} class="formError"{/if}>
			<dt><label for="width">{lang}wcf.acp.ultimate.template.width{/lang}</label></dt>
			<dd>
				<input type="number" id="width" name="width" value="{$width}" min="1" max="24" />
				{if $errorField == 'width'}
					<small class="innerError">
						{lang}wcf.acp.ultimate.template.width.error.{@$errorType}{/lang}
					</small>
				{/if}
			</dd>
		</dl>
		<dl{if $errorField == 'top'} class="formError"{/if}>
			<dt><label for="topDistance">{lang}wcf.acp.ultimate.template.top{/lang}</label></dt>
			<dd>
				<input type="number" id="topDistance" name="top" value="{$top}" min="0" /><span class="unit">px</span>
				{if $errorField == 'top'}
					<small class="innerError">
						{lang}wcf.acp.ultimate.template.top.error.{@$errorType}{/lang}
					</small>
				{/if}
			</dd>
		</dl>
		<dl{if $errorField == 'left'} class="formError"{/if}>
			<dt><label for="left">{lang}wcf.acp.ultimate.template.left{/lang}</label></dt>
			<dd>
				<input type="number" id="left" name="left" value="{$left}" min="1" max="24" />
				{if $errorField == 'left'}
					<small class="innerError">
						{lang}wcf.acp.ultimate.template.left.error.{@$errorType}{/lang}
					</small>
				{/if}
			</dd>
		</dl>
		<div class="formSubmit">
			<button class="button default{if $action == 'add'} disabled" disabled="disabled{/if}" data-type="submit">{lang}wcf.acp.ultimate.template.addToTemplate{/lang}</button>
		</div>
	</div>
</form>

<script type="text/javascript">
	/* <![CDATA[ */
		$(function() {
			{if $action == 'edit'}
				{if $__wcf->session->getPermission('admin.content.ultimate.canDeleteBlock')}
					new WCF.Action.Delete('ultimate\\data\\block\\BlockAction', $('.jsBlock'));
				{/if}
				{if $__wcf->session->getPermission('admin.content.ultimate.canEditBlock')}
					$('#blockList').find('button[data-type="submit"]').click(function(event) {
						event.preventDefault();
						if ($('#blockList').find('.jsBlock').length == 0) {
							event.stopImmediatePropagation();
						} else {
							event.stopPropagation();
						}
					});
					ULTIMATE.Permission.addObject({
						'admin.content.ultimate.canEditBlock': {if $__wcf->session->getPermission('admin.content.ultimate.canEditBlock')}true{else}false{/if},
						'admin.content.ultimate.canDeleteBlock': {if $__wcf->session->getPermission('admin.content.ultimate.canDeleteBlock')}true{else}false{/if}
					});
					{* icon is buggy *}
					WCF.Icon.addObject({
						'wcf.icon.delete': '../{icon size='S'}delete{/icon}',
						'wcf.icon.enabled': '../{icon size='S'}enabled{/icon}',
						'wcf.icon.disabled': '../{icon size='S'}disabled{/icon}'
					});
					new ULTIMATE.Block.Transfer('blocktypeContainer', 'templateBlockList', 'ultimate\\data\\block\\BlockAction');
				{/if}
			{/if}
		});
	/* ]]> */
</script>

{include file='footer'}