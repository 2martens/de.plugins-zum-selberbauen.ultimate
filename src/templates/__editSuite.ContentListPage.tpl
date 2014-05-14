<div id="pageContent" data-controller="ContentListPage" data-request-type="page">
	<header class="boxHeadline">
		<h1>{lang}wcf.acp.ultimate.content.list{/lang}</h1>
	</header>
	
	{assign var=encodedURL value=$url|rawurlencode}
	{assign var=encodedAction value=$action|rawurlencode}
	<div class="contentNavigation">
		{pages print=true assign=pagesLinks application='ultimate' controller="ContentList" parent="EditSuite" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
		
		<nav>
			<ul>
				{if $__wcf->session->getPermission('admin.content.ultimate.canAddContent')}
					<li><a href="{linkExtended controller='ContentAdd' application='ultimate' parent='EditSuite'}{/linkExtended}" title="{lang}wcf.acp.ultimate.content.add{/lang}" class="button"><span class="icon icon24 icon-plus"></span> <span>{lang}wcf.acp.ultimate.content.add{/lang}</span></a></li>
				{/if}
				
				{event name='contentNavigationButtonsTop'}
			</ul>
		</nav>
	</div>
	<div id="contentTableContainer" class="tabularBox tabularBoxTitle marginTop jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.content">
		<header>
			<h2>{lang}wcf.acp.ultimate.content.list{/lang} <span class="counter badge badgeInverse" title="{lang}wcf.acp.ultimate.content.list.count{/lang}">{#$items}</span></h2>
		</header>
		<table class="table">
			<thead>
				<tr>
					<th class="columnMark jsOnly"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
					<th class="columnID{if $sortField == 'contentID'} active {@$sortOrder}{/if}" colspan="2"><a href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentID&sortOrder={if $sortField == 'contentID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle{if $sortField == 'contentTitle'} active {@$sortOrder}{/if}"><a href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentTitle&sortOrder={if $sortField == 'contentTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.acp.ultimate.content.title{/lang}</a></th>
					<th class="columnAuthor{if $sortField == 'contentAuthor'} active {@$sortOrder}{/if}"><a href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentAuthor&sortOrder={if $sortField == 'contentAuthor' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.acp.ultimate.author{/lang}</a></th>
					<th class="columnCategories">{lang}wcf.acp.ultimate.content.categories{/lang}</th>
					<th class="columnTags">{lang}wcf.acp.ultimate.content.tags{/lang}</th>
					<th class="columnDate{if $sortField == 'publishDate'} active {@$sortOrder}{/if}"><a href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=publishDate&sortOrder={if $sortField == 'publishDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.acp.ultimate.publishDateList{/lang}</a></th>
					<th class="columnLastModified{if $sortField == 'lastModified'} active {@$sortOrder}{/if}"><a href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=lastModified&sortOrder={if $sortField == 'lastModified' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.acp.ultimate.lastModified{/lang}</a></th>
					 
					{event name='headColumns'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=content}
					<tr id="contentContainer{@$content->contentID}" class="jsContentRow jsClipboardObject">
						<td class="columnMark jsOnly"><label><input type="checkbox" class="jsClipboardItem" data-object-id="{@$content->contentID}" /></label></td>
						<td class="columnIcon">
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canEditContent')}
								<a href="{linkExtended controller='ContentEdit' application='ultimate' parent='EditSuite' id=$content->contentID}{/linkExtended}"><span title="{lang}wcf.acp.ultimate.content.edit{/lang}" class="icon icon16 icon-pencil jsTooltip"></span></a>
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
						<td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditContent')}<a title="{lang}wcf.acp.ultimate.content.edit{/lang}" href="{linkExtended controller='ContentEdit' application='ultimate' parent='EditSuite' id=$content->contentID}{/linkExtended}">{lang}{@$content->contentTitle}{/lang}</a>{else}{lang}{@$content->contentTitle}{/lang}{/if}</p></td>
						<td class="columnAuthor"><p><a href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}author={@$content->author}{/linkExtended}">{@$content->author}</a></p></td>
						<td class="columnCategories">
							<p>
								{implode from=$content->categories key=categoryID item=category}<a href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}categoryID={@$category->categoryID}{/linkExtended}">{@$category->getLangTitle()}</a>{/implode}
							</p>
						</td>
						<td class="columnTags">
							<p>
								{implode from=$content->tags[$__wcf->getLanguage()->languageID] key=tagID item=tag}<a href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}tagID={@$tag->tagID}{/linkExtended}">{@$tag->getTitle()}</a>{/implode}
							</p>
						</td>
						
						{capture assign='englishAccent'}{@ULTIMATE_GENERAL_ENGLISHDATEFORMAT}{/capture}
						{capture assign='publishDateFormat'}{lang englishAccent=$englishAccent}ultimate.date.dateFormat{/lang}{/capture}
						{assign var='publishDateFormat' value=$publishDateFormat}
						<td class="columnDate"><p>{if $content->publishDate > 0 && $content->publishDate <= $timeNow && $content->status == 3}{@$content->publishDate|dateExtended:$publishDateFormat}{else}{/if}</p></td>
						<td class="columnLastModified"><p>{@$content->lastModified|time}</p></td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	<div class="contentNavigation">
		{@$pagesLinks}
			
		<nav>
			<ul>
				{if $__wcf->session->getPermission('admin.content.ultimate.canAddContent')}
					<li><a href="{linkExtended controller='ContentAdd' application='ultimate' parent='EditSuite'}{/linkExtended}" title="{lang}wcf.acp.ultimate.content.add{/lang}" class="button"><span class="icon icon24 icon-plus"></span> <span>{lang}wcf.acp.ultimate.content.add{/lang}</span></a></li>
				{/if}
				
				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>
		
		<nav class="jsClipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.content' ]"></nav>
	</div>
</div>