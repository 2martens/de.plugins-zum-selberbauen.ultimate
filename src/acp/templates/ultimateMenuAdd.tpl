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
		<h1>{lang}wcf.acp.ultimate.menu.{@$action}{/lang}</h1>
	</hgroup>
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
			<li><a href="{link controller='UltimateMenuList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.appearance.menu.list{/lang}" class="button"><span class="icon icon24 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.appearance.menu.list{/lang}</span></a></li>
			
			{event name='largeButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='UltimateMenuAdd'}{/link}{else}{link controller='UltimateMenuEdit'}{/link}{/if}">
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
			<div id="menuItemList" class="container containerPadding marginTop shadow{if $__wcf->session->getPermission('admin.content.ultimate.canEditMenuItem') && $menuItemNodeList|count > 1} sortableListContainer{/if}">
				{if $action == 'edit'}
				<ol class="sortableList" data-object-id="0">
					{assign var=oldDepth value=0}
					{foreach from=$menuItemNodeList item=menuItem}
						{section name=i loop=$oldDepth-$menuItemNodeList->getDepth()}</ol></li>{/section}
						
						<li class="{if $__wcf->session->getPermission('admin.content.ultimate.canEditMenuItem') && $menuItemNodeList|count > 1}sortableNode {/if}jsMenuItem" data-object-name="{@$menuItem->menuItemName}" data-object-id="{@$menuItem->menuItemID}"{* {if $collapsedMenuItemIDs|is_array} data-is-open="{if $collapsedMenuItemIDs[$menuItem->menuItemID]|isset}0{else}1{/if}"{/if} *}>
							<span class="sortableNodeLabel">
								<span class="buttons">
									
									{if $__wcf->session->getPermission('admin.content.ultimate.canDeleteMenuItem')}
										<span title="{lang}wcf.global.button.delete{/lang}" class="icon icon16 icon-remove jsDeleteButton jsTooltip" data-object-id="{@$menuItem->menuItemID}" data-confirm-message="{lang}wcf.acp.ultimate.menu.item.delete.sure{/lang}"></span>
									{else}
										<span title="{lang}wcf.global.button.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
									{/if}
									
									{if $__wcf->session->getPermission('admin.content.ultimate.canEditMenuItem')}
										<span title="{lang}wcf.global.button.{if !$menuItem->isDisabled}disable{else}enable{/if}{/lang}" class="icon icon16 icon-{if !$menuItem->isDisabled}circle-blank{else}off{/if} jsToggleButton jsTooltip" data-object-id="{@$menuItem->menuItemID}"></span>
									{else}
										<span title="{lang}wcf.global.button.{if !$menuItem->isDisabled}enable{else}disable{/if}{/lang}" class="icon icon16 icon-{if !$menuItem->isDisabled}circle-blank{else}off{/if} disabled"></span>
									{/if}
									
									{event name='buttons'}
								</span>
								
								<span class="title">
									{$menuItem}
								</span>
							</span>
							
							<ol class="sortableList" data-object-id="{@$menuItem->menuItemID}">
						{if !$menuItemNodeList->current()->hasChildren()}
							</ol></li>
						{/if}
						{assign var=oldDepth value=$menuItemNodeList->getDepth()}
					{/foreach}
					{section name=i loop=$oldDepth}</ol></li>{/section}
				</ol>
				{else}
					<p>{lang}wcf.acp.ultimate.menu.addMenuFirst{/lang}</p>
				{/if}
				{if $__wcf->session->getPermission('admin.content.ultimate.canEditMenuItem')}
					<div class="formSubmit">
						<button class="button default{if $action == 'add' || $menuItemNodeList|count == 0} disabled" disabled="disabled{/if}" data-type="submit">{lang}wcf.global.button.save{/lang}</button>
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
		<dl>
			<dt><label>{lang}wcf.acp.ultimate.menu.categories{/lang}</label></dt>
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
		<div class="formSubmit">
			<button class="button default disabled" disabled="disabled" data-type="submit">{lang}wcf.acp.ultimate.menu.addToMenu{/lang}</button>
		</div>
	</div>
</form>
<form method="post">
	<div id="pageSelectContainer" class="container containerPadding marginTop shadow{if $action == 'add'} disabled{/if}">
		<dl>
			<dt><label>{lang}wcf.acp.ultimate.menu.pages{/lang}</label></dt>
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
		<div class="formSubmit">
			<button class="button default disabled" disabled="disabled" data-type="submit">{lang}wcf.acp.ultimate.menu.addToMenu{/lang}</button>
		</div>
	</div>
</form>
<form method="post">	
	<div id="customContainer" class="container containerPadding marginTop shadow{if $action == 'add'} disabled{/if}">
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
		<div class="formSubmit">
			<button class="button default{if $action == 'add'} disabled" disabled="disabled{/if}" data-type="submit">{lang}wcf.acp.ultimate.menu.addToMenu{/lang}</button>
		</div>
	</div>
</form>
<script type="text/javascript">
	/* <![CDATA[ */
		$(function() {
			{if $action == 'edit'}
				{if $__wcf->session->getPermission('admin.content.ultimate.canDeleteMenuItem')}
					new ULTIMATE.NestedSortable.Delete('ultimate\\data\\menu\\item\\MenuItemAction', $('.jsMenuItem'));
				{/if}
				{if $__wcf->session->getPermission('admin.content.ultimate.canEditMenuItem')}
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
					new WCF.Sortable.List('menuItemList', 'ultimate\\data\\menu\\item\\MenuItemAction', 0, { }, false);
					ULTIMATE.Permission.addObject({
						'admin.content.ultimate.canEditMenuItem': {if $__wcf->session->getPermission('admin.content.ultimate.canEditMenuItem')}true{else}false{/if},
						'admin.content.ultimate.canDeleteMenuItem': {if $__wcf->session->getPermission('admin.content.ultimate.canDeleteMenuItem')}true{else}false{/if}
					});
					
					new ULTIMATE.Menu.Item.Transfer('categorySelectContainer', 'menuItemList', 'ultimate\\data\\menu\\item\\MenuItemAction', 0, 'category');
					new ULTIMATE.Menu.Item.Transfer('pageSelectContainer', 'menuItemList', 'ultimate\\data\\menu\\item\\MenuItemAction', 0, 'page');
					new ULTIMATE.Menu.Item.Transfer('customContainer', 'menuItemList', 'ultimate\\data\\menu\\item\\MenuItemAction', 0, 'custom');
				{/if}
			{/if}
		});
	/* ]]> */
</script>

{include file='footer'}