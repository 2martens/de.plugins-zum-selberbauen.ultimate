{include file='header'}

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		var actionObjects = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.page'] = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.page']['delete'] = new WCF.Action.Delete('ultimate\\data\\page\\PageAction', $('.jsPageRow'), $('#pageTableContainer .menu li:first-child .badge'));
		
		WCF.Clipboard.init('ultimate\\acp\\page\\UltimatePageListPage', {@$hasMarkedItems}, actionObjects);
		
		var options = { };
		options.emptyMessage = '{lang}wcf.acp.ultimate.page.noContents{/lang}';
		{if $pages > 1}
			options.refreshPage = true;
		{/if}
		
		new WCF.Table.EmptyTableHandler($('#pageTableContainer'), 'jsPageRow', options);
	});
	//]]>
</script>

<header class="boxHeadline">
	<hgroup>
		<h1>{lang}wcf.acp.ultimate.page.list{/lang}</h1>
	</hgroup>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='ultimate' controller="UltimatePageList" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
	
	<nav>
		<ul>
			{if $__wcf->session->getPermission('admin.content.ultimate.canAddPage')}
				<li><a href="{link application='ultimate' controller='UltimatePageAdd'}{/link}" title="{lang}wcf.acp.ultimate.page.add{/lang}" class="button"><span class="icon icon24 icon-plus"></span> <span>{lang}wcf.acp.ultimate.page.add{/lang}</span></a></li>
			{/if}
			
			{event name='largeButtons'}
		</ul>
	</nav>
</div>
{hascontent}
<div id="pageTableContainer" class="tabularBox tabularBoxTitle marginTop shadow">
	<nav class="menu tableMenu">
		<ul>
			<li{if $action == ''} class="active"{/if}>
				<a href="{link application='ultimate' controller='UltimatePageList'}{/link}"><span>{lang}wcf.acp.ultimate.page.list.all{/lang}</span> <span class="badge badgeInverse" title="{lang}wcf.acp.ultimate.page.list.count{/lang}">{#$items}</span></a>
			</li>
			
			{event name='ultimatePageListOptions'}
		</ul>
	</nav>
	<table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.page">
		<thead>
			<tr>
				<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
				<th class="columnID{if $sortField == 'pageID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link application='ultimate' controller='UltimatePageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=pageID&sortOrder={if $sortField == 'pageID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
				<th class="columnTitle{if $sortField == 'pageTitle'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimatePageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=pageTitle&sortOrder={if $sortField == 'pageTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.page.title{/lang}</a></th>
				<th class="columnAuthor{if $sortField == 'pageAuthor'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimatePageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=pageAuthor&sortOrder={if $sortField == 'pageAuthor' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.author{/lang}</a></th>
				<th class="columnDate{if $sortField == 'publishDate'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimatePageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=publishDate&sortOrder={if $sortField == 'publishDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.publishDateList{/lang}</a></th>
				<th class="columnLastModified{if $sortField == 'lastModified'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimatePageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=lastModified&sortOrder={if $sortField == 'lastModified' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.lastModified{/lang}</a></th>
				{event name='headColumns'}
			</tr>
		</thead>
		
		<tbody>
			{content}
				{foreach from=$objects item=page}
					<tr id="pageContainer{@$page->pageID}" class="jsPageRow">
						<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$page->pageID}" /></td>
						<td class="columnIcon">
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canEditPage')}
								<a href="{link application='ultimate' controller='UltimatePageEdit' id=$page->pageID}{/link}"><span title="{lang}wcf.acp.ultimate.page.edit{/lang}" class="icon icon16 icon-pencil jsTooltip"></span></a>
							{else}
								<span title="{lang}wcf.acp.ultimate.page.edit{/lang}" class="icon icon16 icon-pencil disabled"></span>
							{/if}
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canDeletePage')}
								<span title="{lang}wcf.acp.ultimate.page.delete{/lang}" class="icon icon16 icon-remove jsTooltip jsDeleteButton" data-object-id="{@$page->pageID}" data-confirm-message="{lang}wcf.acp.ultimate.page.delete.sure{/lang}"></span>
							{else}
								<span title="{lang}wcf.acp.ultimate.page.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
							{/if}
							
							{event name='buttons'}
						</td>
						<td class="columnID"><p>{@$page->pageID}</p></td>
						<td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditPage')}<a title="{lang}wcf.acp.ultimate.page.edit{/lang}" href="{link application='ultimate' controller='UltimatePageEdit' id=$page->pageID}{/link}">{lang}{@$page->pageTitle}{/lang}</a>{else}{lang}{@$page->pageTitle}{/lang}{/if}</p></td>
						<td class="columnAuthor"><p>{if $__wcf->session->getPermission('admin.user.canEditUser')}<a title="{lang}wcf.acp.user.edit{/lang}" href="{link controller='UserEdit' id=$page->authorID}{/link}">{@$page->author->username}</a>{else}{@$page->author->username}{/if}</p></td>
						{assign var='englishAccent' value={@ULTIMATE_GENERAL_ENGLISHLANGUAGE}}
						{capture assign='publishDateFormat'}{lang britishEnglish=$englishAccent}ultimate.date.dateFormat{/lang}{/capture}
						{assign var='publishDateFormat' value=$publishDateFormat}
						<td class="columnDate"><p>{if $page->publishDate}{@$page->publishDate|dateExtended:$publishDateFormat}{else}{/if}</p></td>
						<td class="columnLastModified"><p>{@$page->lastModified|time}</p></td>
						
						{event name='columns'}
					</tr>				
				{/foreach}
			{/content}
		</tbody>
	</table>
</div>
{hascontentelse}
<p class="info">{lang}wcf.acp.ultimate.page.noContents{/lang}</p>
{/hascontent}

<div class="contentNavigation">
	{@$pagesLinks}
	
	<div class="clipboardEditor jsClipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.page' ]"></div>
 	
	<nav>
		<ul>
			{if $__wcf->session->getPermission('admin.content.ultimate.canAddPage')}
				<li><a href="{link application='ultimate' controller='UltimatePageAdd'}{/link}" title="{lang}wcf.acp.ultimate.page.add{/lang}" class="button"><span class="icon icon24 icon-plus"></span> <span>{lang}wcf.acp.ultimate.page.add{/lang}</span></a></li>
			{/if}
			
			{event name='largeButtons'}
		</ul>
	</nav>
</div>
</div>

{include file='footer'}
