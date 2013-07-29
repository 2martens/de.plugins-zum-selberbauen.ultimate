{capture assign='pageTitle'}{lang}wcf.acp.ultimate.link.list{/lang}{/capture}
{include file='header' application='ultimate'}

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		var actionObjects = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.link'] = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.link']['delete'] = new WCF.Action.Delete('ultimate\\data\\link\\LinkAction', '.jsLinkRow');
		
		WCF.Clipboard.init('ultimate\\acp\\page\\UltimateLinkListPage', {@$hasMarkedItems}, actionObjects);
		
		var options = { };
		options.emptyMessage = '{lang}wcf.acp.ultimate.link.noContents{/lang}';
		{if $pages > 1}
			options.refreshPage = true;
		{/if}
		
		new WCF.Table.EmptyTableHandler($('#linkTableContainer'), 'jsLinkRow', options);
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.link.list{/lang}</h1>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='ultimate' controller="UltimateLinkList" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
	
	<nav>
		<ul>
			{if $__wcf->session->getPermission('admin.content.ultimate.canManageLinks')}
				<li><a href="{link application='ultimate' controller='UltimateLinkAdd'}{/link}" title="{lang}wcf.acp.ultimate.link.add{/lang}" class="button"><span class="icon icon24 icon-plus"></span> <span>{lang}wcf.acp.ultimate.link.add{/lang}</span></a></li>
			{/if}
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>
{hascontent}
<div id="linkTableContainer" class="tabularBox tabularBoxTitle marginTop">
	<header>
		<h2>{lang}wcf.acp.ultimate.link.list{/lang} <span class="badge badgeInverse" title="{lang}wcf.acp.ultimate.link.list.count{/lang}">{#$items}</span></h2>
	</header>
	<table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.link">
		<thead>
			<tr>
				<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
				<th class="columnID{if $sortField == 'linkID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link application='ultimate' controller='UltimateLinkList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=linkID&sortOrder={if $sortField == 'linkID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
				<th class="columnTitle{if $sortField == 'linkName'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimateLinkList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=linkName&sortOrder={if $sortField == 'linkName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.link.name{/lang}</a></th>
				<th class="columnCategories">{lang}wcf.acp.ultimate.link.categories{/lang}</th>
				 
				{event name='headColumns'}
			</tr>
		</thead>
			
		<tbody>
			{content}
				{foreach from=$objects item=link}
					<tr id="linkContainer{@$link->linkID}" class="jsLinkRow">
						<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$link->linkID}" /></td>
						<td class="columnIcon">
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canManageLinks')}
								<a href="{link application='ultimate' controller='UltimateLinkEdit' id=$link->linkID}{/link}"><span title="{lang}wcf.acp.ultimate.link.edit{/lang}" class="icon icon16 icon-pencil jsTooltip"></span></a>
							{else}
								<span title="{lang}wcf.acp.ultimate.link.edit{/lang}" class="icon icon16 icon-pencil disabled"></span>
							{/if}
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canManageLinks')}
								<span title="{lang}wcf.acp.ultimate.link.delete{/lang}" class="icon icon16 icon-remove jsTooltip jsDeleteButton" data-object-id="{@$link->linkID}" data-confirm-message="{lang}wcf.acp.ultimate.link.delete.sure{/lang}"></span>
							{else}
								<span title="{lang}wcf.acp.ultimate.link.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
							{/if}
							
							{event name='buttons'}
						</td>
						<td class="columnID"><p>{@$link->linkID}</p></td>
						<td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canManageLinks')}<a title="{lang}wcf.acp.ultimate.link.edit{/lang}" href="{link application='ultimate' controller='UltimateLinkEdit' id=$link->linkID}{/link}">{lang}{@$link->linkName}{/lang}</a>{else}{lang}{@$link->linkName}{/lang}{/if}</p></td>
						<td class="columnCategories">
							<p>
								{implode from=$link->categories key=categoryID item=category}<a href="{link application='ultimate' controller='UltimateLinkList'}categoryID={@$category->categoryID}{/link}">{@$category->getTitle()}</a>{/implode}
							</p>
						</td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			{/content}
		</tbody>
	</table>
</div>
{hascontentelse}
<p class="info">{lang}wcf.acp.ultimate.link.noContents{/lang}</p>
{/hascontent}
<div class="contentNavigation">
	{@$pagesLinks}
	
	<div class="clipboardEditor jsClipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.link' ]"></div>
 	
	<nav>
		<ul>
			{if $__wcf->session->getPermission('admin.content.ultimate.canManageLinks')}
				<li><a href="{link application='ultimate' controller='UltimateLinkAdd'}{/link}" title="{lang}wcf.acp.ultimate.link.add{/lang}" class="button"><span class="icon icon24 icon-plus"></span> <span>{lang}wcf.acp.ultimate.link.add{/lang}</span></a></li>
			{/if}
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>
</div>

{include file='footer'}
