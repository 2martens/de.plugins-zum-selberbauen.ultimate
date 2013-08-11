{capture assign='pageTitle'}{lang}wcf.acp.ultimate.template.{@$action}{/lang}{/capture}
{include file='header' application='ultimate'}

<script data-relocate="true" type="text/javascript">
	/* <![CDATA[ */
	$(function() {
		WCF.TabMenu.init();
	});
	/* ]]> */
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.template.{@$action}{/lang}</h1>
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
			<li><a href="{link application='ultimate' controller='UltimateTemplateList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.appearance.template.list{/lang}" class="button"><span class="icon icon24 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.appearance.template.list{/lang}</span></a></li>
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == "add"}{link application='ultimate' controller='UltimateTemplateAdd'}{/link}{else}{link application='ultimate' controller='UltimateTemplateEdit'}{/link}{/if}">
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
					<script data-relocate="true" type="text/javascript">
					/* <![CDATA[ */
					$(function() {
						var $initialValue = $('#showWidgetArea').val();
						if ($initialValue) {
							$('#widgetAreaSide').removeClass('disabled').prop('disabled', false);
						} else {
							$('#widgetAreaSide').addClass('disabled').prop('disabled', true);
						}
						$('#showWidgetArea').change(function(event) {
							var $value = $(this).prop('checked');
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
						<option label="{lang}wcf.acp.ultimate.template.selectWidgetArea.default{/lang}" value="0">{lang}wcf.acp.ultimate.template.selectWidgetArea.default{/lang}</option>
						{htmlOptions options=$widgetAreas selected=$selectedWidgetArea}
					</select>
					{if $errorField == 'selectWidgetArea'}
						<small class="innerError">
							{lang}wcf.acp.ultimate.template.selectWidgetArea.error.{@$errorType}{/lang}
						</small>
					{/if}
					<script data-relocate="true" type="text/javascript">
					/* <![CDATA[ */
					$(function() {
						var $initialValue = $('#showWidgetArea').val();
						if ($initialValue) {
							$('#selectWidgetArea').removeClass('disabled').prop('disabled', false);
						} else {
							$('#selectWidgetArea').addClass('disabled').prop('disabled', true);
						}
						$('#showWidgetArea').change(function(event) {
							var $value = $(this).prop('checked');
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
						<option label="{lang}wcf.acp.ultimate.template.selectMenu.default{/lang}" value="0">{lang}wcf.acp.ultimate.template.selectMenu.default{/lang}</option>
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
									
									{if $__wcf->session->getPermission('admin.content.ultimate.canManageBlocks')}
										<span title="{lang}wcf.acp.ultimate.block.edit{/lang}" class="icon icon16 icon-pencil jsTooltip" data-object-id="{@$templateBlock->blockID}"></span>
									{else}
										<span title="{lang}wcf.acp.ultimate.block.edit{/lang}" class="icon icon16 icon-pencil disabled"></span>
									{/if}
									
									{if $__wcf->session->getPermission('admin.content.ultimate.canManageBlocks')}
										<span title="{lang}wcf.global.button.delete{/lang}" class="icon icon16 icon-remove jsDeleteButton jsTooltip" data-object-id="{@$templateBlock->blockID}" data-confirm-message="{lang}'wcf.acp.ultimate.block.delete.sure'{/lang}"></span>
									{else}
										<span title="{lang}wcf.global.button.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
									{/if}
									
									{event name='buttons'}
								</span>
								
								<span class="title">
									{lang}{@$templateBlock->blockType->blockTypeName}{/lang} #{@$templateBlock->blockID}
								</span>
							</span>
						</li>
					{/foreach}
				</ol>
				{else}
					<p>{lang}wcf.acp.ultimate.template.addTemplateFirst{/lang}</p>
				{/if}
				{* doesn't have any use
				if $__wcf->session->getPermission('admin.content.ultimate.canManageBlocks')}
					<div class="formSubmit">
						<button class="button default{if $action == 'add' || $blocks|count == 0} disabled" disabled="disabled{/if}" data-type="submit">{lang}wcf.global.button.save{/lang}</button>
					</div>
				{/if
				*}
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
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.template.addBlock{/lang}</legend>
			<dl{if $errorField == 'selectBlocktype'} class="formError"{/if}>
				<dt><label for="selectBlocktype">{lang}wcf.acp.ultimate.template.selectBlocktype{/lang}</label></dt>
				<dd>
					<select id="selectBlocktype" name="selectBlocktype">
						<option label="{lang}wcf.acp.ultimate.template.selectBlocktype.none{/lang}" value="0">{lang}wcf.acp.ultimate.template.selectBlocktype.none{/lang}</option>
						{htmlOptions options=$blocktypes}
					</select>
					{if $errorField == 'selectBlocktype'}
						<small id="selectBlocktypeError" class="innerError">
							{lang}wcf.acp.ultimate.template.selectBlocktype.error.{@$errorType}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
		</fieldset>
		<div class="formSubmit">
			<button class="button default disabled" disabled="disabled" data-type="submit">{lang}wcf.acp.ultimate.template.addToTemplate{/lang}</button>
		</div>	
	</div>
</form>
<div id="blockForm" class="ultimateHidden"></div>

<script data-relocate="true" type="text/javascript">
	/* <![CDATA[ */
		$(function() {
			{if $action == 'edit'}
				{if $__wcf->session->getPermission('admin.content.ultimate.canManageBlocks')}
					new WCF.Action.Delete('ultimate\\data\\block\\BlockAction', '.jsBlock');
				{/if}
				{if $__wcf->session->getPermission('admin.content.ultimate.canManageBlocks')}
					$('#selectBlocktype').change(function(event) {
						var $item = $('#selectBlocktype');
						var $submitButton = $('#blocktypeContainer').find('button[data-type="submit"]');
						if ($item.val() == 0 && !$submitButton.prop('disabled')) {
							$submitButton.prop('disabled', true).addClass('disabled');
						}
						else if ($item.val() != 0 && $submitButton.prop('disabled')) {
							$submitButton.prop('disabled', false).removeClass('disabled');
						}
					});
					ULTIMATE.Permission.addObject({
						'admin.content.ultimate.canManageBlocks': {if $__wcf->session->getPermission('admin.content.ultimate.canManageBlocks')}true{else}false{/if}
					});
					WCF.Language.addObject({
						'wcf.acp.ultimate.template.dialog.additionalOptions': '{lang}wcf.acp.ultimate.template.dialog.additionalOptions{/lang}',
						'wcf.acp.ultimate.block.edit': '{lang}wcf.acp.ultimate.block.edit{/lang}'
					});
					new ULTIMATE.ACP.Block.Transfer('blocktypeContainer', 'templateBlockList', 'ultimate\\data\\block\\BlockAction');
				{/if}
			{/if}
		});
	/* ]]> */
</script>

{include file='footer'}