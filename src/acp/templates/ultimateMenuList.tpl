{capture assign='pageTitle'}{lang}wcf.acp.ultimate.menu.list{/lang}{/capture}
{include file='header' application='ultimate'}

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		var actionObjects = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.menu'] = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.menu']['delete'] = new WCF.Action.Delete('ultimate\\data\\menu\\MenuAction', $('.jsMenuRow'), $('#menuTableContainer .menu li:first-child .badge'));
		
		WCF.Clipboard.init('ultimate\\acp\\page\\UltimateMenuListPage', {@$hasMarkedItems}, actionObjects);
		
		var options = { };
		options.emptyMessage = '{lang}wcf.acp.ultimate.menu.noContents{/lang}';
		{if $pages > 1}
			options.refreshPage = true;
		{/if}
		
		new WCF.Table.EmptyTableHandler($('#menuTableContainer'), 'jsMenuRow', options);
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.menu.list{/lang}</h1>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='ultimate' controller='UltimateMenuList' link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
	
	<nav>
		<ul>
			{if $__wcf->session->getPermission('admin.content.ultimate.canManageMenus')}
				<li><a href="{link application='ultimate' controller='UltimateMenuAdd'}{/link}" title="{lang}wcf.acp.ultimate.menu.add{/lang}" class="button"><span class="icon icon24 icon-plus"></span> <span>{lang}wcf.acp.ultimate.menu.add{/lang}</span></a></li>
			{/if}
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>
{hascontent}
<div id="menuTableContainer" class="tabularBox tabularBoxTitle marginTop">
	<header>
		<h2>{lang}wcf.acp.ultimate.menu.list{/lang} <span class="badge badgeInverse" title="{lang}wcf.acp.ultimate.menu.list.count{/lang}">{#$items}</span></h2>
	</header>
	<table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.menu">
		<thead>
			<tr>
				<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
				<th class="columnID{if $sortField == 'menuID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link application='ultimate' controller='UltimateMenuList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=menuID&sortOrder={if $sortField == 'menuID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
				<th class="columnTitle{if $sortField == 'menuName'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimateMenuList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=menuName&sortOrder={if $sortField == 'menuName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.menu.name{/lang}</a></th>
				
				{event name='headColumns'}
			</tr>
		</thead>
		
		<tbody>
			{content}
				{foreach from=$objects item=menu}
					<tr id="menuContainer{@$menu->menuID}" class="jsMenuRow">
						<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$menu->menuID}" /></td>
						<td class="columnIcon">
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canManageMenus')}
								<a href="{link application='ultimate' controller='UltimateMenuEdit' id=$menu->menuID}{/link}"><span title="{lang}wcf.acp.ultimate.menu.edit{/lang}" class="icon icon16 icon-pencil jsTooltip"></span></a>
							{else}
								<span title="{lang}wcf.acp.ultimate.menu.edit{/lang}" class="icon icon16 icon-pencil disabled"></span>
							{/if}
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canManageMenus')}
								<span title="{lang}wcf.acp.ultimate.menu.delete{/lang}" class="icon icon16 icon-remove jsTooltip jsDeleteButton" data-object-id="{@$menu->menuID}" data-confirm-message="{lang}wcf.acp.ultimate.menu.delete.sure{/lang}"></span>
							{else}
								<span title="{lang}wcf.acp.ultimate.menu.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
							{/if}
							
							{event name='buttons'}
						</td>
						<td class="columnID"><p>{@$menu->menuID}</p></td>
						<td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canManageMenus')}<a title="{lang}wcf.acp.ultimate.menu.edit{/lang}" href="{link application='ultimate' controller='UltimateMenuEdit' id=$menu->menuID}{/link}">{lang}{@$menu->menuName}{/lang}</a>{else}{lang}{@$menu->menuName}{/lang}{/if}</p></td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			{/content}
		</tbody>
	</table>
</div>
{hascontentelse}
<p class="info">{lang}wcf.acp.ultimate.menu.noContents{/lang}</p>
{/hascontent}
<div class="contentNavigation">
	{@$pagesLinks}
	
	<div class="clipboardEditor jsClipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.menu' ]"></div>
	
	<nav>
		<ul>
			{if $__wcf->session->getPermission('admin.content.ultimate.canManageMenus')}
				<li><a href="{link application='ultimate' controller='UltimateMenuAdd'}{/link}" title="{lang}wcf.acp.ultimate.menu.add{/lang}" class="button"><span class="icon icon24 icon-plus"></span> <span>{lang}wcf.acp.ultimate.menu.add{/lang}</span></a></li>
			{/if}
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>
</div>

{include file='footer'}
