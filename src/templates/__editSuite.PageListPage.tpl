<div id="pageContent" data-controller="PageListPage" data-request-type="page" data-ajax-only="true">
    <script data-relocate="true" type="text/javascript">
        //<![CDATA[
        $(function() {
            var actionObjects = { };
            actionObjects['de.plugins-zum-selberbauen.ultimate.page'] = { };
            actionObjects['de.plugins-zum-selberbauen.ultimate.page']['delete'] = new ULTIMATE.Action.Delete('ultimate\\data\\page\\PageAction', '.jsPageRow');
            
            WCF.Clipboard.init('ultimate\\page\\PageListPage', {@$hasMarkedItems}, actionObjects);
            
            var options = { };
            options.emptyMessage = '{lang}wcf.acp.ultimate.page.noContents{/lang}';
            {if $pages > 1}
                options.refreshPage = true;
                {if $pages == $pageNo}
                    options.updatePageNumber = -1;
                {/if}
            {else}
                options.emptyMessage = '{lang}wcf.acp.ultimate.content.noContents{/lang}';
            {/if}
            
            new WCF.Table.EmptyTableHandler($('#pageTableContainer'), 'jsPageRow', options);
        });
        //]]>
    </script>
    
    <header class="boxHeadline">
        <h1>{lang}wcf.acp.ultimate.page.list{/lang}</h1>
    </header>
    
    {assign var=encodedURL value=$url|rawurlencode}
    {assign var=encodedAction value=$action|rawurlencode}
    <div class="contentNavigation">
        {pagesExtended print=true assign=pagesLinks application='ultimate' controller="PageList" parent="EditSuite" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
        
        {hascontent}
        <nav>
            <ul>
                {content}
                    {if $__wcf->session->getPermission('user.ultimate.editing.canEditPage')}
                        <li><a data-controller="PageAddForm" data-request-type="form" href="{linkExtended application='ultimate' parent='EditSuite' controller='PageAdd'}{/linkExtended}" title="{lang}wcf.acp.ultimate.page.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.ultimate.page.add{/lang}</span></a></li>
                    {/if}
                    
                    {event name='contentNavigationButtons'}
                {/content}
            </ul>
        </nav>
        {/hascontent}
    </div>
    {if $items}
        <div id="pageTableContainer" class="tabularBox tabularBoxTitle marginTop">
            <header>
                <h2>{lang}wcf.acp.ultimate.page.list{/lang} <span class="counter badge badgeInverse" title="{lang}wcf.acp.ultimate.page.list.count{/lang}">{#$items}</span></h2>
            </header>
            <table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.page">
                <thead>
                    <tr>
                        <th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
                        <th class="columnID{if $sortField == 'pageID'} active {@$sortOrder}{/if}" colspan="2"><a data-controller="PageListPage" data-request-type="page" href="{linkExtended application='ultimate' parent='EditSuite' controller='PageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=pageID&sortOrder={if $sortField == 'pageID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.global.objectID{/lang}</a></th>
                        <th class="columnTitle{if $sortField == 'pageTitle'} active {@$sortOrder}{/if}"><a data-controller="PageListPage" data-request-type="page" href="{linkExtended application='ultimate' parent='EditSuite' controller='PageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=pageTitle&sortOrder={if $sortField == 'pageTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.acp.ultimate.page.title{/lang}</a></th>
                        <th class="columnAuthor{if $sortField == 'pageAuthor'} active {@$sortOrder}{/if}"><a data-controller="PageListPage" data-request-type="page" href="{linkExtended application='ultimate' parent='EditSuite' controller='PageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=pageAuthor&sortOrder={if $sortField == 'pageAuthor' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.acp.ultimate.author{/lang}</a></th>
                        <th class="columnDate dateColumn{if $sortField == 'publishDate'} active {@$sortOrder}{/if}"><a data-controller="PageListPage" data-request-type="page" href="{linkExtended application='ultimate' parent='EditSuite' controller='PageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=publishDate&sortOrder={if $sortField == 'publishDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.acp.ultimate.publishDateList{/lang}</a></th>
                        <th class="columnLastModified dateColumn{if $sortField == 'lastModified'} active {@$sortOrder}{/if}"><a data-controller="PageListPage" data-request-type="page" href="{linkExtended application='ultimate' parent='EditSuite' controller='PageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=lastModified&sortOrder={if $sortField == 'lastModified' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/linkExtended}">{lang}wcf.acp.ultimate.lastModified{/lang}</a></th>
                        
                        {event name='headColumns'}
                    </tr>
                </thead>
                
                <tbody>
                    {foreach from=$objects item=page}
                        <tr id="pageContainer{@$page->pageID}" class="jsPageRow">
                            <td class="columnMark"><label><input type="checkbox" class="jsClipboardItem" data-object-id="{@$page->pageID}" /></label></td>
                            <td class="columnIcon">
                                
                                {if $__wcf->session->getPermission('user.ultimate.editing.canEditPage')}
                                    <a data-controller="PageEditForm" data-request-type="form" href="{linkExtended application='ultimate' parent='EditSuite' controller='PageEdit' id=$page->pageID}{/linkExtended}"><span title="{lang}wcf.acp.ultimate.page.edit{/lang}" class="icon icon16 icon-pencil jsTooltip"></span></a>
                                {else}
                                    <span title="{lang}wcf.acp.ultimate.page.edit{/lang}" class="icon icon16 icon-pencil disabled"></span>
                                {/if}
                                
                                {if $__wcf->session->getPermission('user.ultimate.editing.canDeletePage')}
                                    <span title="{lang}wcf.acp.ultimate.page.delete{/lang}" class="icon icon16 icon-remove jsTooltip jsDeleteButton pointer" data-object-id="{@$page->pageID}" data-confirm-message="{lang}wcf.acp.ultimate.page.delete.sure{/lang}"></span>
                                {else}
                                    <span title="{lang}wcf.acp.ultimate.page.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
                                {/if}
                                
                                {event name='buttons'}
                            </td>
                            <td class="columnID"><p>{@$page->pageID}</p></td>
                            <td class="columnTitle"><p>{if $__wcf->session->getPermission('user.ultimate.editing.canEditPage')}<a title="{lang}wcf.acp.ultimate.page.edit{/lang}" data-controller="PageEditForm" data-request-type="form" href="{linkExtended application='ultimate' parent='EditSuite' controller='PageEdit' id=$page->pageID}{/linkExtended}">{@$page->pageTitle}</a>{else}{@$page->pageTitle}{/if}</p></td>
                            <td class="columnAuthor"><p><a data-controller="PageListPage" data-request-type="page" href="{linkExtended application='ultimate' parent='EditSuite' controller='PageList'}authorID={@$page->authorID}{/linkExtended}">{@$page->author}</a></p></td>
                            <td class="columnDate dateColumn"><p>{if $page->publishDate}{@$page->publishDate|time}{/if}</p></td>
                            <td class="columnLastModified dateColumn"><p>{@$page->lastModified|time}</p></td>
                            
                            {event name='columns'}
                        </tr>				
                    {/foreach}
                </tbody>
            </table>
        </div>
    {else}
        <p class="info">{lang}wcf.acp.ultimate.page.noContents{/lang}</p>
    {/if}
    
    <div class="contentNavigation">
        {@$pagesLinks}
        
        {hascontent}
        <nav>
            <ul>
                {content}
                    {if $__wcf->session->getPermission('user.ultimate.editing.canEditPage')}
                        <li><a data-controller="PageAddForm" data-request-type="form" href="{linkExtended application='ultimate' parent='EditSuite' controller='PageAdd'}{/linkExtended}" title="{lang}wcf.acp.ultimate.page.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.ultimate.page.add{/lang}</span></a></li>
                    {/if}
                    
                    {event name='contentNavigationButtons'}
                {/content}
            </ul>
        </nav>
        {/hascontent}
        
        <nav class="clipboardEditor jsClipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.page' ]"></nav>
    </div>
</div>
