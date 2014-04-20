{capture assign='pageTitle'}{lang}wcf.acp.ultimate.content.list{/lang}{/capture}
{include file='header' application='ultimate'}

<script data-relocate="true" type="text/javascript">
	//<![CDATA[
	$(function() {
		var actionObjects = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.content'] = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.content']['delete'] = new ULTIMATE.Action.Delete('ultimate\\data\\content\\ContentAction', '.jsContentRow');
		
		WCF.Clipboard.init('ultimate\\acp\\page\\UltimateContentListPage', {@$hasMarkedItems}, actionObjects);
		
		var options = { };
		options.emptyMessage = '{lang}wcf.acp.ultimate.content.noContents{/lang}';
		{if $pages > 1}
			options.refreshPage = true;
		{/if}
		
		new WCF.Table.EmptyTableHandler($('#contentTableContainer'), 'jsContentRow', options);
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.content.list{/lang}</h1>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='ultimate' controller="UltimateContentList" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
	
	<nav>
		<ul>
			{if $__wcf->session->getPermission('admin.content.ultimate.canAddContent')}
				<li><a href="{link application='ultimate' controller='UltimateContentAdd'}{/link}" title="{lang}wcf.acp.ultimate.content.add{/lang}" class="button"><span class="icon icon24 icon-plus"></span> <span>{lang}wcf.acp.ultimate.content.add{/lang}</span></a></li>
			{/if}
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>
{hascontent}
<div id="contentTableContainer" class="tabularBox tabularBoxTitle marginTop">
	<header>
		<h2>{lang}wcf.acp.ultimate.content.list{/lang} <span class="counter badge badgeInverse" title="{lang}wcf.acp.ultimate.content.list.count{/lang}">{#$items}</span></h2>
	</header>
	<table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.content">
		<thead>
			<tr>
				<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
				<th class="columnID{if $sortField == 'contentID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link application='ultimate' controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentID&sortOrder={if $sortField == 'contentID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
				<th class="columnTitle{if $sortField == 'contentTitle'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentTitle&sortOrder={if $sortField == 'contentTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.content.title{/lang}</a></th>
				<th class="columnAuthor{if $sortField == 'contentAuthor'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentAuthor&sortOrder={if $sortField == 'contentAuthor' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.author{/lang}</a></th>
				<th class="columnCategories">{lang}wcf.acp.ultimate.content.categories{/lang}</th>
				<th class="columnTags">{lang}wcf.acp.ultimate.content.tags{/lang}</th>
				<th class="columnDate dateColumn{if $sortField == 'publishDate'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=publishDate&sortOrder={if $sortField == 'publishDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.publishDateList{/lang}</a></th>
				<th class="columnLastModified dateColumn{if $sortField == 'lastModified'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=lastModified&sortOrder={if $sortField == 'lastModified' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.lastModified{/lang}</a></th>
				 
				{event name='headColumns'}
			</tr>
		</thead>
		
		<tbody>
			{content}
				{foreach from=$objects item=content}
					<tr id="contentContainer{@$content->contentID}" class="jsContentRow">
						<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$content->contentID}" /></td>
						<td class="columnIcon">
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canEditContent')}
								<a href="{link application='ultimate' controller='UltimateContentEdit' id=$content->contentID}{/link}"><span title="{lang}wcf.acp.ultimate.content.edit{/lang}" class="icon icon16 icon-pencil jsTooltip"></span></a>
							{else}
								<span title="{lang}wcf.acp.ultimate.content.edit{/lang}" class="icon icon16 icon-pencil disabled"></span>
							{/if}
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canDeleteContent')}
								<span title="{lang}wcf.acp.ultimate.content.delete{/lang}" class="icon icon16 icon-remove jsTooltip jsDeleteButton pointer" data-object-id="{@$content->contentID}" data-confirm-message="{lang}wcf.acp.ultimate.content.delete.sure{/lang}"></span>
							{else}
								<span title="{lang}wcf.acp.ultimate.content.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
							{/if}
							
							{event name='buttons'}
						</td>
						<td class="columnID"><p>{@$content->contentID}</p></td>
						<td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditContent')}<a title="{lang}wcf.acp.ultimate.content.edit{/lang}" href="{link application='ultimate' controller='UltimateContentEdit' id=$content->contentID}{/link}">{@$content->contentTitle}</a>{else}{@$content->contentTitle}{/if}</p></td>
						<td class="columnAuthor"><p><a href="{link application='ultimate' controller='UltimateContentList'}author={@$content->author}{/link}">{@$content->author}</a></p></td>
						<td class="columnCategories">
							<p>
								{implode from=$content->categories key=categoryID item=category}<a href="{link application='ultimate' controller='UltimateContentList'}categoryID={@$category->categoryID}{/link}">{@$category->getLangTitle()}</a>{/implode}
							</p>
						</td>
						<td class="columnTags">
							<p>
								{implode from=$content->tags[$__wcf->getLanguage()->languageID] key=tagID item=tag}<a href="{link application='ultimate' controller='UltimateContentList'}tagID={@$tag->tagID}{/link}">{@$tag->getTitle()}</a>{/implode}
							</p>
						</td>
						<td class="columnDate dateColumn"><p>{if $content->publishDate}{@$content->publishDate|time}{/if}</p></td>
						<td class="columnLastModified dateColumn"><p>{@$content->lastModified|time}</p></td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			{/content}
		</tbody>
	</table>
</div>
{hascontentelse}
<p class="info">{lang}wcf.acp.ultimate.content.noContents{/lang}</p>
{/hascontent}
<div class="contentNavigation">
	{@$pagesLinks}
		
	<div class="clipboardEditor jsClipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.content' ]"></div>
	 	
	<nav>
		<ul>
			{if $__wcf->session->getPermission('admin.content.ultimate.canAddContent')}
				<li><a href="{link application='ultimate' controller='UltimateContentAdd'}{/link}" title="{lang}wcf.acp.ultimate.content.add{/lang}" class="button"><span class="icon icon24 icon-plus"></span> <span>{lang}wcf.acp.ultimate.content.add{/lang}</span></a></li>
			{/if}
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>
</div>

{include file='footer'}
