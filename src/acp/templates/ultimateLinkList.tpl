{include file='header'}

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		var actionObjects = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.link'] = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.link']['delete'] = new WCF.Action.Delete('ultimate\\data\\link\\LinkAction', $('.jsLinkRow'), $('#linkTableContainer .wcf-menu li:first-child .wcf-badge'));
		
		WCF.Clipboard.init('ultimate\\acp\\page\\UltimateLinkListPage', {@$hasMarkedItems}, actionObjects);
		
		var options = { };
		{if $pages > 1}
			options.refreshPage = true;
		{/if}
		
		new WCF.Table.EmptyTableHandler($('#linkTableContainer'), 'jsLinkRow', options);
	});
	//]]>
</script>

<header class="boxHeadline">
	<hgroup>
		<h1>{lang}wcf.acp.ultimate.link.list{/lang}</h1>
	</hgroup>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller="UltimateLinkList" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
	
	<nav>
		<ul>
			{if $__wcf->session->getPermission('admin.content.ultimate.canAddContent')}
				<li><a href="{link controller='UltimateLinkAdd'}{/link}" title="{lang}wcf.acp.ultimate.link.add{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/add.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.ultimate.link.add{/lang}</span></a></li>
			{/if}
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<div id="linkTableContainer" class="tabularBox marginTop shadow">
	<nav class="wcf-menu">
		<ul>
			<li{if $action == ''} class="active"{/if}><a href="{link controller='UltimateLinkList'}{/link}"><span>{lang}wcf.acp.ultimate.link.list.all{/lang}</span> <span class="wcf-badge" title="{lang}wcf.acp.ultimate.link.list.count{/lang}">{#$items}</span></a></li>
			
			{event name='ultimateLinkListOptions'}
		</ul>
	</nav>
	{hascontent}
		<table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.link">
			<thead>
				<tr>
					<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
					<th class="columnID{if $sortField == 'linkID'} active{/if}" colspan="2"><a href="{link controller='UltimateLinkList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=linkID&sortOrder={if $sortField == 'linkID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}{if $sortField == 'linkID'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="{if $sortOrder == 'ASC'}{lang}wcf.global.sortOrder.ascending{/lang}{else}{lang}wcf.global.sortOrder.descending{/lang}{/if}" />{/if}</a></th>
					<th class="columnTitle{if $sortField == 'linkName'} active{/if}"><a href="{link controller='UltimateLinkList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=linkName&sortOrder={if $sortField == 'linkName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.link.name{/lang}{if $sortField == 'linkName'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="{if $sortOrder == 'ASC'}{lang}wcf.global.sortOrder.ascending{/lang}{else}{lang}wcf.global.sortOrder.descending{/lang}{/if}" />{/if}</a></th>
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
								
								{if $__wcf->session->getPermission('admin.content.ultimate.canEditLink')}
									<a href="{link controller='UltimateLinkEdit' id=$link->linkID}{/link}"><img src="{@$__wcf->getPath()}icon/edit.svg" alt="" title="{lang}wcf.acp.ultimate.link.edit{/lang}" class="icon16 jsTooltip" /></a>
								{else}
									<img src="{@$__wcf->getPath()}icon/edit.svg" alt="" title="{lang}wcf.acp.ultimate.link.edit{/lang}" class="icon16 disabled" />
								{/if}
								
								{if $__wcf->session->getPermission('admin.content.ultimate.canDeleteLink')}
									<img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.acp.ultimate.link.delete{/lang}" class="icon16 jsTooltip jsDeleteButton" data-object-id="{@$link->linkID}" data-confirm-message="{lang}wcf.acp.ultimate.link.delete.sure{/lang}" />
								{else}
									<img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.acp.ultimate.link.delete{/lang}" class="icon16 disabled" />
								{/if}
								
								{event name='buttons'}
							</td>
							<td class="columnID"><p>{@$link->linkID}</p></td>
							<td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditLink')}<a title="{lang}wcf.acp.ultimate.link.edit{/lang}" href="{link controller='UltimateLinkEdit' id=$link->linkID}{/link}">{lang}{@$link->linkName}{/lang}</a>{else}{lang}{@$link->linkName}{/lang}{/if}</p></td>
							<td class="columnCategories">
								<p>
									{implode from=$link->categories key=categoryID item=category}<a href="{link controller='UltimateLinkList'}categoryID={@$category->categoryID}{/link}">{@$category}</a>{/implode}
								</p>
							</td>
							
							{event name='columns'}
						</tr>
					{/foreach}
				{/content}
			</tbody>
		</table>
	</div>
	
	<div class="contentNavigation">
		{@$pagesLinks}
		
		<div class="wcf-clipboardEditor jsClipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.link' ]"></div>
	 	
		<nav>
			<ul>
				{if $__wcf->session->getPermission('admin.content.ultimate.canAddLink')}
					<li><a href="{link controller='UltimateLinkAdd'}{/link}" title="{lang}wcf.acp.ultimate.link.add{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/add.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.ultimate.link.add{/lang}</span></a></li>
				{/if}
				
				{event name='contentNavigationButtons'}
			</ul>
		</nav>
	</div>
{hascontentelse}
</div>

<p class="wcf-info">{lang}wcf.acp.ultimate.link.noContents{/lang}</p>
{/hascontent}
{include file='footer'}
