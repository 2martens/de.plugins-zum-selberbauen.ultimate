{capture assign='pageTitle'}{lang}wcf.acp.ultimate.menu.{@$action}{/lang}{/capture}
{include file='header' application='ultimate'}

<script data-relocate="true" type="text/javascript">
	/* <![CDATA[ */
	$(function() {
		WCF.TabMenu.init();
		{if $action == 'edit'}
			new ULTIMATE.NestedSortable.Delete('ultimate\\data\\menu\\item\\MenuItemAction', '.sortableNode', '> .sortableNodeLabel .jsDeleteButton');
			new WCF.Action.Toggle('ultimate\\data\\menu\\item\\MenuItemAction', '.sortableNode', '> .sortableNodeLabel .jsToggleButton');
			{if $menuItems|count > 1}
				new WCF.Sortable.List('menuItemList', 'ultimate\\data\\menu\\item\\MenuItemAction', 0, { maxLevels: 2 }, false);
			{/if}
			ULTIMATE.Permission.addObject({
				'admin.content.ultimate.canManageMenuItems': {if $__wcf->session->getPermission('admin.content.ultimate.canManageMenuItems')}true{else}false{/if}
			});
			
			new ULTIMATE.ACP.Menu.Item.Transfer('categorySelectContainer', 'menuItemList', 'ultimate\\data\\menu\\item\\MenuItemAction', 0, 'category');
			new ULTIMATE.ACP.Menu.Item.Transfer('pageSelectContainer', 'menuItemList', 'ultimate\\data\\menu\\item\\MenuItemAction', 0, 'page');
			new ULTIMATE.ACP.Menu.Item.Transfer('customContainer', 'menuItemList', 'ultimate\\data\\menu\\item\\MenuItemAction', 0, 'custom');
		{/if}
	});
	/* ]]> */
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.menu.{@$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link application='ultimate' controller='UltimateMenuList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.appearance.menu.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.appearance.menu.list{/lang}</span></a></li>
			
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $action == 'add'}
	<p class="info">{lang}wcf.acp.ultimate.menu.addMenuFirst{/lang}</p>
{/if}
<form method="post" action="{if $action == 'add'}{link application='ultimate' controller='UltimateMenuAdd'}{/link}{else}{link application='ultimate' controller='UltimateMenuEdit'}{/link}{/if}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.menu.general{/lang}</legend>
			<dl{if $errorField == 'menuName'} class="formError"{/if}>
				<dt><label for="menuName">{lang}wcf.acp.ultimate.menu.name{/lang}</label></dt>
				<dd>
					<input type="text" id="menuName" name="menuName" value="{@$menuName}" class="long" required="required" placeholder="{lang}wcf.acp.ultimate.menu.name.placeholder{/lang}" />
					{if $errorField == 'menuName'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.menu.name.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
		</fieldset>
		{if $action == 'edit'}
			<fieldset>
				<legend>{lang}wcf.acp.ultimate.menu.items{/lang}</legend>
				<div id="menuItemList" class="sortableListContainer">
					<ol class="sortableList" data-object-id="0">
						{foreach from=$menuItems item=menuItem}
							<li class="sortableNode" data-object-name="{@$menuItem->menuItemName}" data-object-id="{@$menuItem->menuItemID}">
								<span class="sortableNodeLabel">
									<span>{lang}{$menuItem->menuItemName}{/lang}</span>
									<span class="statusDisplay sortableButtonContainer">
										{if $menuItem->canDisable()}
											<span class="icon icon16 icon-check{if $menuItem->isDisabled}-empty{/if} jsToggleButton jsTooltip pointer" title="{lang}wcf.global.button.{if $menuItem->isDisabled}enable{else}disable{/if}{/lang}" data-object-id="{@$menuItem->menuItemID}" data-disable-message="{lang}wcf.global.button.disable{/lang}" data-enable-message="{lang}wcf.global.button.enable{/lang}"></span>
										{else}
											<span class="icon icon16 icon-check{if $menuItem->isDisabled}-empty{/if} disabled" title="{lang}wcf.global.button.{if $menuItem->isDisabled}enable{else}disable{/if}{/lang}"></span>
										{/if}
										{if $menuItem->canDelete()}
											<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$menuItem->menuItemID}" data-confirm-message="{lang __menuItem=$menuItem}wcf.acp.pageMenu.delete.sure{/lang}"></span>
										{else}
											<span class="icon icon16 icon-remove disabled" title="{lang}wcf.global.button.delete{/lang}"></span>
										{/if}
										
										{event name='menuItemButtons'}
									</span>
								</span>
								<ol class="sortableList" data-object-id="{@$menuItem->menuItemID}">
									{foreach from=$menuItem item=childMenuItem}
										<li class="sortableNode sortableNoNesting" data-object-id="{@$childMenuItem->menuItemID}">
											<span class="sortableNodeLabel">
												<span>{lang}{$childMenuItem->menuItemName}{/lang}</span>
												<span class="statusDisplay sortableButtonContainer">
													{if $childMenuItem->canDisable()}
														<span class="icon icon16 icon-check{if $childMenuItem->isDisabled}-empty{/if} jsToggleButton jsTooltip pointer" title="{lang}wcf.global.button.{if $childMenuItem->isDisabled}enable{else}disable{/if}{/lang}" data-object-id="{@$childMenuItem->menuItemID}" data-disable-message="{lang}wcf.global.button.disable{/lang}" data-enable-message="{lang}wcf.global.button.enable{/lang}"></span>
													{else}
														<span class="icon icon16 icon-check{if $childMenuItem->isDisabled}-empty{/if} disabled" title="{lang}wcf.global.button.{if $menuItem->isDisabled}enable{else}disable{/if}{/lang}"></span>
													{/if}
													{if $childMenuItem->canDelete()}
															<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$childMenuItem->menuItemID}" data-confirm-message="{lang __menuItem=$childMenuItem}wcf.acp.pageMenu.delete.sure{/lang}"></span>
													{else}
														<span class="icon icon16 icon-remove disabled" title="{lang}wcf.global.button.delete{/lang}"></span>
													{/if}
													
													{event name='subMenuItemButtons'}
												</span>
											</span>
										</li>
									{/foreach}
								</ol>
							</li>
						{/foreach}
					</ol>
					{if $__wcf->session->getPermission('admin.content.ultimate.canManageMenuItems')}
						<div class="formSubmit">
							<button class="{if $action == 'add' || $menuItems|count == 0} disabled" disabled="disabled{/if}" data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
						</div>
					{/if}
				</div>
			</fieldset>
		{/if}
		{event name='fieldsets'}
	</div>
	<div class="formSubmit">
		<input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SID_INPUT_TAG}
		<input type="hidden" name="action" value="{@$action}" />
		{if $menuID|isset}<input type="hidden" name="id" value="{@$menuID}" />{/if}
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>
{if $action == 'edit'}
	<form method="post">
		<div id="categorySelectContainer" class="container containerPadding marginTop shadow{if $action == 'add'} disabled{/if}">
			<fieldset>
				<legend>{lang}wcf.acp.ultimate.menu.categories{/lang}</legend>
				<dl>
					{*<dt><label>{lang}wcf.acp.ultimate.menu.categories{/lang}</label></dt>*}
					<dd>
						{nestedHtmlCheckboxes options=$categories name='categoryIDs' disabled=$disabledCategoryIDs}
						<small>
							{lang}wcf.acp.ultimate.menu.categories.description{/lang}
						</small>
					</dd>
				</dl>
			</fieldset>
			<div class="formSubmit">
				<button class="button default disabled" disabled="disabled" data-type="submit">{lang}wcf.acp.ultimate.menu.addToMenu{/lang}</button>
			</div>
		</div>
	</form>
	<form method="post">
		<div id="pageSelectContainer" class="container containerPadding marginTop shadow{if $action == 'add'} disabled{/if}">
			<fieldset>
				<legend>{lang}wcf.acp.ultimate.menu.pages{/lang}</legend>
				<dl>
					{*<dt><label>{lang}wcf.acp.ultimate.menu.pages{/lang}</label></dt>*}
					<dd>
						{nestedHtmlCheckboxes options=$pages name='pageIDs' disabled=$disabledPageIDs}
						<small>
							{lang}wcf.acp.ultimate.menu.pages.description{/lang}
						</small>
					</dd>
				</dl>
			</fieldset>
			<div class="formSubmit">
				<button class="button default disabled" disabled="disabled" data-type="submit">{lang}wcf.acp.ultimate.menu.addToMenu{/lang}</button>
			</div>
		</div>
	</form>
	<form method="post">	
		<div id="customContainer" class="container containerPadding marginTop shadow{if $action == 'add'} disabled{/if}">
			<fieldset>
				<legend>{lang}wcf.acp.ultimate.menu.custom{/lang}</legend>
				<dl>
					<dt><label for="linkType">{lang}wcf.acp.ultimate.menu.custom.linkType{/lang}</label></dt>
					<dd>
						<label><input type="radio" name="linkType" id="linkType" value="controller" checked="checked" />{lang}wcf.acp.ultimate.menu.custom.linkType.controller{/lang}</label>
						<label><input type="radio" name="linkType" value="url" />{lang}wcf.acp.ultimate.menu.custom.linkType.url{/lang}</label>
						<script data-relocate="true" type="text/javascript">
						//<![CDATA[
							{if $action == 'edit'}
							$(function() {
								$('#url').closest('dl').hide();
								$('input[name="linkType"]').change(function(event) {
									var $target = $(event.currentTarget);
									var value = $target.val();
									if (value == 'controller') {
										$('#controller').closest('dl').show();
										$('#url').closest('dl').hide();
									}
									else if (value == 'url') {
										$('#controller').closest('dl').hide();
										$('#url').closest('dl').show();
									}
								});
							});
							{/if}
						//]]>
						</script>
					</dd>
				</dl>
				<dl>
					<dt><label for="link">{lang}wcf.acp.ultimate.menu.custom.linkType.url{/lang}</label></dt>
					<dd>
						<input type="url" name="url" id="url" value="http://" class="medium{if $action == 'add'} disabled" disabled="disabled{/if}" />
					</dd>
				</dl>
				<dl>
					<dt><label for="controller">{lang}wcf.acp.ultimate.menu.custom.linkType.controller{/lang}</label></dt>
					<dd>
						<input type="text" name="controller" id="controller" value="" class="medium{if $action == 'add'} disabled" disabled="disabled{/if}" />
					</dd>
				</dl>
				<dl>
					<dt><label for="title">{lang}wcf.acp.ultimate.menu.custom.linkTitle{/lang}</label></dt>
					<dd>
						<script data-relocate="true" type="text/javascript">
						//<![CDATA[
							{if $action == 'edit'}
							$(function() {
								var $availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{@$languageID}: '{$languageName}'{/implode} };
								var $optionValues = { {implode from=$i18nValues['title'] key=languageID item=value}'{@$languageID}': '{$value}'{/implode} };
								new WCF.MultipleLanguageInput('title', false, $optionValues, $availableLanguages);
							});
							{/if}
						//]]>
						</script>
						<input type="text" id="title" name="title" value="{@$i18nPlainValues['title']}" class="medium{if $action == 'add'} disabled" disabled="disabled{/if}" placeholder="{lang}wcf.acp.ultimate.menu.custom.linkTitle.placeholder{/lang}" />
					</dd>
				</dl>
			</fieldset>
			<div class="formSubmit">
				<button class="button default{if $action == 'add'} disabled" disabled="disabled{/if}" data-type="submit">{lang}wcf.acp.ultimate.menu.addToMenu{/lang}</button>
			</div>
		</div>
	</form>
{/if}

{include file='footer'}