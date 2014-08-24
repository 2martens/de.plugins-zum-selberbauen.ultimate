<div id="pageContent" data-controller="ContentListPage" data-request-type="page" data-ajax-only="true">
	<script data-relocate="true" type="text/javascript">
		//<![CDATA[
		$(function() {
			var actionObjects = { };
			actionObjects['de.plugins-zum-selberbauen.ultimate.content'] = { };
			actionObjects['de.plugins-zum-selberbauen.ultimate.content']['delete'] = new ULTIMATE.Action.Delete('ultimate\\data\\content\\ContentAction', '.jsContentRow');
			
			ULTIMATE.EditSuite.Clipboard.init('ultimate\\page\\ContentListPage', {@$hasMarkedItems}, actionObjects);
			
			var options = { };
			options.emptyMessage = '{lang}wcf.acp.ultimate.content.noContents{/lang}';
			{if $pages > 1}
				options.refreshPage = true;
				{if $pages == $pageNo}
					options.updatePageNumber = -1;
				{/if}
			{else}
				options.emptyMessage = '{lang}wcf.acp.ultimate.content.noContents{/lang}';
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
		{pagesExtended print=true assign=pagesLinks application='ultimate' controller="ContentList" parent="EditSuite" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
		
		{hascontent}
        <nav>
			<ul>
                {content}
                    {if $__wcf->session->getPermission('user.ultimate.content.canEditContent')}
                        <li><a data-controller="ContentAddForm" data-request-type="form" href="{linkExtended controller='ContentAdd' application='ultimate' parent='EditSuite'}{/linkExtended}" title="{lang}wcf.acp.ultimate.content.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.ultimate.content.add{/lang}</span></a></li>
                    {/if}
    
                    {event name='contentNavigationButtonsTop'}
                {/content}
			</ul>
		</nav>
        {/hascontent}
	</div>
    {if $items}
        <div id="contentTableContainer" class="tabularBox tabularBoxTitle marginTop jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.content">
            <header>
                <h2>{lang}wcf.acp.ultimate.content.list{/lang} <span class="counter badge badgeInverse" title="{lang}wcf.acp.ultimate.content.list.count{/lang}">{#$items}</span></h2>
            </header>
            <table class="table">
                <thead>
                    <tr>
                        <th class="columnMark jsOnly"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
                        <th class="columnID{if $sortField == 'contentID'} active {@$sortOrder}{/if}" colspan="2"><a data-controller="ContentListPage" data-request-type="page" href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}pageNo={@$pageNo}&sortField=contentID&sortOrder={if $sortField == 'contentID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.global.objectID{/lang}</a></th>
                        <th class="columnTitle{if $sortField == 'contentTitle'} active {@$sortOrder}{/if}"><a data-controller="ContentListPage" data-request-type="page" href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}pageNo={@$pageNo}&sortField=contentTitle&sortOrder={if $sortField == 'contentTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.acp.ultimate.content.title{/lang}</a></th>
                        <th class="columnAuthor{if $sortField == 'contentAuthor'} active {@$sortOrder}{/if}"><a data-controller="ContentListPage" data-request-type="page" href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}pageNo={@$pageNo}&sortField=contentAuthor&sortOrder={if $sortField == 'contentAuthor' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.acp.ultimate.author{/lang}</a></th>
                        <th class="columnCategories">{lang}wcf.acp.ultimate.content.categories{/lang}</th>
                        <th class="columnTags">{lang}wcf.acp.ultimate.content.tags{/lang}</th>
                        <th class="columnDate{if $sortField == 'publishDate'} active {@$sortOrder}{/if}"><a data-controller="ContentListPage" data-request-type="page" href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}pageNo={@$pageNo}&sortField=publishDate&sortOrder={if $sortField == 'publishDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.acp.ultimate.publishDateList{/lang}</a></th>
                        <th class="columnLastModified{if $sortField == 'lastModified'} active {@$sortOrder}{/if}"><a data-controller="ContentListPage" data-request-type="page" href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}pageNo={@$pageNo}&sortField=lastModified&sortOrder={if $sortField == 'lastModified' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.acp.ultimate.lastModified{/lang}</a></th>
                         
                        {event name='headColumns'}
                    </tr>
                </thead>
                
                <tbody>
                    {foreach from=$objects item=content}
                        <tr id="contentContainer{@$content->contentID}" class="jsContentRow jsClipboardObject">
                            <td class="columnMark jsOnly"><label><input type="checkbox" class="jsClipboardItem" data-object-id="{@$content->contentID}" /></label></td>
                            <td class="columnIcon">
                                
                                {if $__wcf->session->getPermission('user.ultimate.content.canEditContent')}
                                    <a data-controller="ContentEditForm" data-request-type="form" href="{linkExtended controller='ContentEdit' application='ultimate' parent='EditSuite' id=$content->contentID}{/linkExtended}"><span title="{lang}wcf.acp.ultimate.content.edit{/lang}" class="icon icon16 icon-pencil jsTooltip"></span></a>
                                {else}
                                    <span title="{lang}wcf.acp.ultimate.content.edit{/lang}" class="icon icon16 icon-pencil disabled"></span>
                                {/if}
                                
                                {if $__wcf->session->getPermission('user.ultimate.content.canDeleteContent')}
                                    <span title="{lang}wcf.acp.ultimate.content.delete{/lang}" class="icon icon16 icon-remove jsTooltip jsDeleteButton pointer" data-object-id="{@$content->contentID}" data-confirm-message="{lang}wcf.acp.ultimate.content.delete.sure{/lang}"></span>
                                {else}
                                    <span title="{lang}wcf.acp.ultimate.content.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
                                {/if}
                                
                                {event name='buttons'}
                            </td>
                            <td class="columnID"><p>{@$content->contentID}</p></td>
                            <td class="columnTitle"><p>{if $__wcf->session->getPermission('user.ultimate.content.canEditContent')}<a title="{lang}wcf.acp.ultimate.content.edit{/lang}" data-controller="ContentEditForm" data-request-type="form"  href="{linkExtended controller='ContentEdit' application='ultimate' parent='EditSuite' id=$content->contentID}{/linkExtended}">{lang}{@$content->contentTitle}{/lang}</a>{else}{lang}{@$content->contentTitle}{/lang}{/if}</p></td>
                            <td class="columnAuthor"><p><a data-controller="ContentListPage" data-request-type="page" href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}authorID={@$content->authorID}{/linkExtended}">{@$content->author}</a></p></td>
                            <td class="columnCategories">
                                <p>
                                    {implode from=$content->categories key=categoryID item=category}<a data-controller="ContentListPage" data-request-type="page" href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}categoryID={@$category->categoryID}{/linkExtended}">{@$category->getLangTitle()}</a>{/implode}
                                </p>
                            </td>
                            <td class="columnTags">
                                <p>
                                    {implode from=$content->tags[$__wcf->getLanguage()->languageID] key=tagID item=tag}<a data-controller="ContentListPage" data-request-type="page" href="{linkExtended controller='ContentList' application='ultimate' parent='EditSuite'}tagID={@$tag->tagID}{/linkExtended}">{@$tag->getTitle()}</a>{/implode}
                                </p>
                            </td>
                            
                            <td class="columnDate dateColumn"><p>{if $content->publishDate > 0 && $content->status >= 2}{@$content->publishDate|time}{else}{/if}</p></td>
                            <td class="columnLastModified dateColumn"><p>{@$content->lastModified|time}</p></td>
                            
                            {event name='columns'}
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    {else}
        <p class="info">{lang}wcf.acp.ultimate.content.noContents{/lang}</p>
    {/if}
	<div class="contentNavigation">
		{@$pagesLinks}
		
        {hascontent}
		<nav>
			<ul>
                {content}
                    {if $__wcf->session->getPermission('user.ultimate.content.canEditContent')}
                        <li><a data-controller="ContentAddForm" data-request-type="form" href="{linkExtended controller='ContentAdd' application='ultimate' parent='EditSuite'}{/linkExtended}" title="{lang}wcf.acp.ultimate.content.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.ultimate.content.add{/lang}</span></a></li>
                    {/if}
                    
                    {event name='contentNavigationButtonsBottom'}
                {/content}
			</ul>
		</nav>
        {/hascontent}
		
		<nav class="jsClipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.content' ]"></nav>
	</div>
</div>
