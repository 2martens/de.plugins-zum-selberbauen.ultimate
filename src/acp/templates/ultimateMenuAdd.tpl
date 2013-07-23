{include file='header' pageTitle='wcf.acp.ultimate.menu.'|concat:$action}

<script type="text/javascript">
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
			
			new ULTIMATE.Menu.Item.Transfer('categorySelectContainer', 'menuItemList', 'ultimate\\data\\menu\\item\\MenuItemAction', 0, 'category');
			new ULTIMATE.Menu.Item.Transfer('pageSelectContainer', 'menuItemList', 'ultimate\\data\\menu\\item\\MenuItemAction', 0, 'page');
			new ULTIMATE.Menu.Item.Transfer('customContainer', 'menuItemList', 'ultimate\\data\\menu\\item\\MenuItemAction', 0, 'custom');
		{/if}
	});
	/* ]]> */
</script>
{*<script type="text/javascript">
	/* <![CDATA[ */
		$(function() {
			{if $action == 'edit'}
				{if $__wcf->session->getPermission('admin.content.ultimate.canManageMenuItems')}
					new ULTIMATE.NestedSortable.Delete('ultimate\\data\\menu\\item\\MenuItemAction', $('.jsMenuItem'));
				{/if}
				{if $__wcf->session->getPermission('admin.content.ultimate.canManageMenuItems')}
					new WCF.Action.Toggle('ultimate\\data\\menu\\item\\MenuItemAction', $('.jsMenuItem'), '> .buttons > .jsToggleButton');
					{if $menuItemNodeList|count > 1}
						var sortableNodes = $('.sortableNode');
						sortableNodes.each(function(index, node) {
							$(node).wcfIdentify();
						});
					{/if}
					$('#menuItemList').find('button[data-type="submit"]').click(function(event) {
						event.preventDefault();
						if ($('#menuItemList').find('.jsMenuItem').length == 0) {
							event.stopImmediatePropagation();
						} else {
							event.stopPropagation();
						}
					});
					new WCF.Sortable.List('menuItemList', 'ultimate\\data\\menu\\item\\MenuItemAction', 0, {literal}{{/literal}maxLevels: 2{literal}}{/literal}, false);
					
					
					
				{/if}
			{/if}
		});
	/* ]]> */
</script>*}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.menu.{@$action}{/lang}</h1>
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
			<li><a href="{link application='ultimate' controller='UltimateMenuList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.appearance.menu.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.appearance.menu.list{/lang}</span></a></li>
			
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

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
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.menu.items{/lang}</legend>
			<div id="menuItemList" class="sortableListContainer">
				{if $action == 'edit'}
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
				{else}
					<p>{lang}wcf.acp.ultimate.menu.addMenuFirst{/lang}</p>
				{/if}
				{if $__wcf->session->getPermission('admin.content.ultimate.canManageMenuItems')}
					<div class="formSubmit">
						<button class="{if $action == 'add' || $menuItems|count == 0} disabled" disabled="disabled{/if}" data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
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
		{if $menuID|isset}<input type="hidden" name="id" value="{@$menuID}" />{/if}
	</div>
</form>
<form method="post">
	<div id="categorySelectContainer" class="container containerPadding marginTop shadow{if $action == 'add'} disabled{/if}">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.menu.categories{/lang}</legend>
			<dl>
				{*<dt><label>{lang}wcf.acp.ultimate.menu.categories{/lang}</label></dt>*}
				<dd>
					{if $action == 'add'}
						{nestedHtmlCheckboxes options=$categories name='categoryIDs' disabled='disabled'}
					{else}
						{nestedHtmlCheckboxes options=$categories name='categoryIDs' disabled=$disabledCategoryIDs}
					{/if}
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
					{if $action == 'add'}
						{nestedHtmlCheckboxes options=$pages name='pageIDs' disabled='disabled'}
					{else}
						{nestedHtmlCheckboxes options=$pages name='pageIDs' disabled=$disabledPageIDs}
					{/if}
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
				<dt><label for="link">{lang}wcf.acp.ultimate.menu.custom.link{/lang}</label></dt>
				<dd>
					<input type="url" name="link" id="link" value="http://" class="medium{if $action == 'add'} disabled" disabled="disabled{/if}" />
				</dd>
			</dl>
			<dl>
				<dt><label for="title">{lang}wcf.acp.ultimate.menu.custom.linkTitle{/lang}</label></dt>
				<dd>
					<script type="text/javascript">
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

{include file='footer'}