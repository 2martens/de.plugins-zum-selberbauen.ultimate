{include file='header'}

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		var actionObjects = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.content'] = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.content']['delete'] = new WCF.Action.Delete('ultimate\\data\\content\\ContentAction', $('.jsContentRow'), $('#contentTableContainer .menu li:first-child .badge'));
		
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
	<hgroup>
		<h1>{lang}wcf.acp.ultimate.content.list{/lang}</h1>
	</hgroup>
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
			
			{event name='largeButtons'}
		</ul>
	</nav>
</div>
{hascontent}
<div id="contentTableContainer" class="tabularBox tabularBoxTitle marginTop shadow">
	<nav class="menu tableMenu">
		<ul>
			<li{if $action == ''} class="active"{/if}>
				<a href="{link application='ultimate' controller='UltimateContentList'}{/link}"><span>{lang}wcf.acp.ultimate.content.list.all{/lang}</span> <span class="badge badgeInverse" title="{lang}wcf.acp.ultimate.content.list.count{/lang}">{#$items}</span></a>
			</li>
			
			{event name='ultimateContentListOptions'}
		</ul>
	</nav>
	<table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.content">
		<thead>
			<tr>
				<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
				<th class="columnID{if $sortField == 'contentID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentID&sortOrder={if $sortField == 'contentID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
				<th class="columnTitle{if $sortField == 'contentTitle'} active {@$sortOrder}{/if}"><a href="{link controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentTitle&sortOrder={if $sortField == 'contentTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.content.title{/lang}</a></th>
				<th class="columnAuthor{if $sortField == 'contentAuthor'} active {@$sortOrder}{/if}"><a href="{link controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentAuthor&sortOrder={if $sortField == 'contentAuthor' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.author{/lang}</a></th>
				<th class="columnCategories">{lang}wcf.acp.ultimate.content.categories{/lang}</th>
				<th class="columnTags">{lang}wcf.acp.ultimate.content.tags{/lang}</th>
				<th class="columnDate{if $sortField == 'publishDate'} active {@$sortOrder}{/if}"><a href="{link controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=publishDate&sortOrder={if $sortField == 'publishDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.publishDateList{/lang}</a></th>
				<th class="columnLastModified{if $sortField == 'lastModified'} active {@$sortOrder}{/if}"><a href="{link controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=lastModified&sortOrder={if $sortField == 'lastModified' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.lastModified{/lang}</a></th>
				 
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
								<a href="{link controller='UltimateContentEdit' id=$content->contentID}{/link}"><span title="{lang}wcf.acp.ultimate.content.edit{/lang}" class="icon icon16 icon-pencil jsTooltip"></span></a>
							{else}
								<span title="{lang}wcf.acp.ultimate.content.edit{/lang}" class="icon icon16 icon-pencil disabled"></span>
							{/if}
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canDeleteContent')}
								<span title="{lang}wcf.acp.ultimate.content.delete{/lang}" class="icon icon16 icon-remove jsTooltip jsDeleteButton" data-object-id="{@$content->contentID}" data-confirm-message="{lang}wcf.acp.ultimate.content.delete.sure{/lang}"></span>
							{else}
								<span title="{lang}wcf.acp.ultimate.content.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
							{/if}
							
							{event name='buttons'}
						</td>
						<td class="columnID"><p>{@$content->contentID}</p></td>
						<td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditContent')}<a title="{lang}wcf.acp.ultimate.content.edit{/lang}" href="{link controller='UltimateContentEdit' id=$content->contentID}{/link}">{lang}{@$content->contentTitle}{/lang}</a>{else}{lang}{@$content->contentTitle}{/lang}{/if}</p></td>
						<td class="columnAuthor"><p><a href="{link controller='UltimateContentList'}author={@$content->author}{/link}">{@$content->author}</a></p></td>
						<td class="columnCategories">
							<p>
								{implode from=$content->categories key=categoryID item=category}<a href="{link controller='UltimateContentList'}categoryID={@$category->categoryID}{/link}">{@$category}</a>{/implode}
							</p>
						</td>
						<td class="columnTags">
							<p>
								{implode from=$content->tags[$__wcf->getLanguage()->languageID] key=tagID item=tag}<a href="{link controller='UltimateContentList'}tagID={@$tag->tagID}{/link}">{@$tag->getTitle()}</a>{/implode}
							</p>
						</td>
						{assign var='englishAccent' value={@ULTIMATE_GENERAL_ENGLISHLANGUAGE}}
						{capture assign='publishDateFormat'}{lang britishEnglish=$englishAccent}ultimate.date.dateFormat{/lang}{/capture}
						{assign var='publishDateFormat' value=$publishDateFormat}
						<td class="columnDate"><p>{if $content->publishDate > 0 && $content->publishDate <= $timeNow && $content->status == 3}{@$content->publishDate|dateExtended:$publishDateFormat}{else}{/if}</p></td>
						<td class="columnLastModified"><p>{@$content->lastModified|time}</p></td>
						
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
			
			{event name='largeButtons'}
		</ul>
	</nav>
</div>
</div>

{include file='footer'}
