{include file='header'}

<script type="text/javascript">
    //<![CDATA[
    $(function() {
        var actionObjects = { };
        actionObjects['de.plugins-zum-selberbauen.ultimate.content'] = { };
        actionObjects['de.plugins-zum-selberbauen.ultimate.content']['delete'] = new WCF.Action.Delete('ultimate\\data\\content\\ContentAction', $('.jsContentRow'), $('#contentTableContainer .wcf-menu li:first-child .wcf-badge'));
        
        WCF.Clipboard.init('ultimate\\acp\\page\\UltimateContentListPage', {@$hasMarkedItems}, actionObjects);
        
        var options = { };
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
    {pages print=true assign=pagesLinks controller="UltimateContentList" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
    
    <nav>
        <ul>
            {if $__wcf->session->getPermission('admin.content.ultimate.canAddContent')}
                <li><a href="{link controller='UltimateContentAdd'}{/link}" title="{lang}wcf.acp.ultimate.content.add{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/add.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.ultimate.content.add{/lang}</span></a></li>
            {/if}
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

<div id="contentTableContainer" class="tabularBox tabularBoxTitle marginTop shadow">
    <nav class="wcf-menu">
        <ul>
            <li{if $action == ''} class="active"{/if}><a href="{link controller='UltimateContentList'}{/link}"><span>{lang}wcf.acp.ultimate.content.list.all{/lang}</span> <span class="wcf-badge badgeInverse" title="{lang}wcf.acp.ultimate.content.list.count{/lang}">{#$items}</span></a></li>
            
            {event name='ultimateContentListOptions'}
        </ul>
    </nav>
    {hascontent}
        <table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.content">
            <thead>
                <tr>
                    <th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
                    <th class="columnID{if $sortField == 'contentID'} active{/if}" colspan="2"><a href="{link controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentID&sortOrder={if $sortField == 'contentID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}{if $sortField == 'contentID'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="{if $sortOrder == 'ASC'}{lang}wcf.global.sortOrder.ascending{/lang}{else}{lang}wcf.global.sortOrder.descending{/lang}{/if}" />{/if}</a></th>
                    <th class="columnTitle{if $sortField == 'contentTitle'} active{/if}"><a href="{link controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentTitle&sortOrder={if $sortField == 'contentTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.content.title{/lang}{if $sortField == 'contentTitle'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="{if $sortOrder == 'ASC'}{lang}wcf.global.sortOrder.ascending{/lang}{else}{lang}wcf.global.sortOrder.descending{/lang}{/if}" />{/if}</a></th>
                    <th class="columnAuthor{if $sortField == 'contentAuthor'} active{/if}"><a href="{link controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentAuthor&sortOrder={if $sortField == 'contentAuthor' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.content.author{/lang}{if $sortField == 'contentAuthor'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="{if $sortOrder == 'ASC'}{lang}wcf.global.sortOrder.ascending{/lang}{else}{lang}wcf.global.sortOrder.descending{/lang}{/if}" />{/if}</a></th>
                    <th class="columnCategories">{lang}wcf.acp.ultimate.content.categories{/lang}</th>
                    {* need to implement tags
                    <th class="columnTags">{lang}wcf.acp.ultimate.content.tags{/lang}</th>
                    *}
                    <th class="columnLastModified">{if $sortField == 'lastModified'} active{/if}"><a href="{link controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=lastModified&sortOrder={if $sortField == 'lastModified' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.content.lastModified{/lang}{if $sortField == 'lastModified'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="{if $sortOrder == 'ASC'}{lang}wcf.global.sortOrder.ascending{/lang}{else}{lang}wcf.global.sortOrder.descending{/lang}{/if}" />{/if}</a></th>
                     
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
                                    <a href="{link controller='UltimateContentEdit' id=$content->contentID}{/link}"><img src="{@$__wcf->getPath()}icon/edit.svg" alt="" title="{lang}wcf.acp.ultimate.content.edit{/lang}" class="icon16 jsTooltip" /></a>
                                {else}
                                    <img src="{@$__wcf->getPath()}icon/edit.svg" alt="" title="{lang}wcf.acp.ultimate.content.edit{/lang}" class="icon16 disabled" />
                                {/if}
                                {if $__wcf->session->getPermission('admin.content.ultimate.canDeleteContent')}
                                    <a onclick="return confirm('{lang}wcf.acp.ultimate.content.delete.sure{/lang}')" href="{link controller='UltimateContentDelete' id=$content->contentID}url={@$encodedURL}&t={@SECURITY_TOKEN}{/link}"><img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.acp.ultimate.content.delete{/lang}" class="icon16 jsTooltip" /></a>
                                {else}
                                    <img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.acp.ultimate.content.delete{/lang}" class="icon16 disabled" />
                                {/if}
                    
                                {event name='buttons'}
                            </td>
                            <td class="columnID"><p>{@$content->contentID}</p></td>
                            <td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditContent')}<a title="{lang}wcf.acp.ultimate.content.edit{/lang}" href="{link controller='UltimateContentEdit' id=$content->contentID}{/link}">{$content->contentTitle}</a>{else}{$content->contentTitle}{/if}</p></td>
                            <td class="columnAuthor"><p><a href="{link controller='UltimateContentList'}author={@$content->author}{/link}">{@$content->author}</a></p></td>
                            <td class="columnCategories">
                                <p>
                                    {foreach from=$content->getCategories() item=category}
                                        {counter name=categoryCounter assign=categoryCounter print=false start=0}
                                        {if $categoryCounter > 0}, {/if}<a href="{link controller='UltimateContentList'}categoryID={@$category->categoryID}{/link}">{@$category}</a>
                                    {/foreach}
                                </p>
                            </td>
                            {* need to implement tags
                            <td class="columnTags">
                                <p>
                                    {foreach from=$content->getTags() item=tag}
                                        {counter name=tagCounter assign=tagCounter print=false start=0}
                                        {if $tagCounter > 0}, {/if}<a href="{link controller='UltimateContentList'}tagID={@$tag->tagID}{/link}">{@$tag}</a>
                                    {/foreach}
                                </p>
                            </td> *}
                            <td class="columnLastModified"><p>{@$content->lastModified|time}</p></td>
                        
                            {event name='columns'}
                        </tr>
                    {/foreach}
                {/content}
            </tbody>
        </table>
    
    </div>
    
    <div class="contentNavigation">
        {@$pagesLinks}
    
        <div class="clipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.content' ]"></div>
     
        <nav>
            <ul>
                {if $__wcf->session->getPermission('admin.content.ultimate.canAddContent')}
                    <li><a href="{link controller='UltimateContentAdd'}{/link}" title="{lang}wcf.acp.ultimate.content.add{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/add.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.ultimate.content.add{/lang}</span></a></li>
                {/if}
            
                {event name='largeButtons'}
            </ul>
        </nav>
    </div>
{hascontentelse}
</div>

<p class="info">{lang}wcf.acp.ultimate.content.noContents{/lang}</p>
{/hascontent}
{include file='footer'}
 