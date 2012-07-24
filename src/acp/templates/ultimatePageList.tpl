{include file='header'}

<script type="text/javascript">
    //<![CDATA[
    $(function() {
        WCF.Clipboard.init('ultimate\\acp\\page\\UltimatePageListPage', {@$hasMarkedItems});
        new ULTIMATE.ACP.Page.List();
    });
    //]]>
</script>

<header class="mainHeading">
    {*<img src="{@RELATIVE_WCF_DIR}icon/{if $searchID}search{else}user{/if}1.svg" alt="" />*}
    <hgroup>
        <h1>{lang}wcf.acp.ultimate.page.list{/lang}</h1>
    </hgroup>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentHeader">
    {pages print=true assign=pagesLinks controller="UltimatePageList" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
    
    <nav>
        <ul class="largeButtons">
            {if $__wcf->session->getPermission('admin.content.ultimate.canAddPage')}
                <li><a href="{link controller='UltimatePageAdd'}{/link}" title="{lang}wcf.acp.ultimate.page.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}wcf.acp.ultimate.page.add{/lang}</span></a></li>
            {/if}
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

<div class="border boxTitle">
    <nav class="menu">
        <ul>
            <li{if $action == ''} class="active"{/if}><a href="{link controller='UltimatePageList'}{/link}"><span>{lang}wcf.acp.ultimate.page.list.all{/lang}</span> <span class="badge" title="{lang}wcf.acp.ultimate.page.list.count{/lang}">{#$items}</span></a></li>
            
            {event name='ultimateContentListOptions'}
        </ul>
    </nav>
    {hascontent}
    <table class="clipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.page">
        <thead>
            <tr class="tableHead">
                <th class="columnMark"><label><input type="checkbox" class="clipboardMarkAll" /></label></th>
                <th class="columnID{if $sortField == 'pageID'} active{/if}" colspan="2"><a href="{link controller='UltimatePageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=pageID&sortOrder={if $sortField == 'pageID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}{if $sortField == 'pageID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="{if $sortOrder == 'ASC'}{lang}wcf.global.sortOrder.ascending{/lang}{else}{lang}wcf.global.sortOrder.descending{/lang}{/if}" />{/if}</a></th>
                <th class="columnTitle{if $sortField == 'pageTitle'} active{/if}"><a href="{link controller='UltimatePageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=pageTitle&sortOrder={if $sortField == 'pageTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.page.title{/lang}{if $sortField == 'pageTitle'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="{if $sortOrder == 'ASC'}{lang}wcf.global.sortOrder.ascending{/lang}{else}{lang}wcf.global.sortOrder.descending{/lang}{/if}" />{/if}</a></th>
                <th class="columnAuthor{if $sortField == 'pageAuthor'} active{/if}"><a href="{link controller='UltimatePageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=pageAuthor&sortOrder={if $sortField == 'pageAuthor' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.general.author{/lang}{if $sortField == 'pageAuthor'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="{if $sortOrder == 'ASC'}{lang}wcf.global.sortOrder.ascending{/lang}{else}{lang}wcf.global.sortOrder.descending{/lang}{/if}" />{/if}</a></th>
                <th class="columnLastModified{if $sortField == 'lastModified'} active{/if}"><a href="{link controller='UltimatePageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=lastModified&sortOrder={if $sortField == 'lastModified' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.general.lastModified{/lang}{if $sortField == 'lastModified'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="{if $sortOrder == 'ASC'}{lang}wcf.global.sortOrder.ascending{/lang}{else}{lang}wcf.global.sortOrder.descending{/lang}{/if}" />{/if}</a></th>
                    
                {event name='headColumns'}
            </tr>
        </thead>
        
        <tbody>
            {content}
                {foreach from=$objects item=page}
                    <tr id="contentRow{@$page['pageID']}">
                        <td class="columnMark"><input type="checkbox" class="clipboardItem" data-object-id="{@$page['pageID']}" /></td>
                        <td class="columnIcon">
                            {if $__wcf->session->getPermission('admin.content.ultimate.canEditPage')}
                                <a href="{link controller='UltimatePageEdit' id=$page['pageID']}{/link}"><img src="{@RELATIVE_WCF_DIR}icon/edit1.svg" alt="" title="{lang}wcf.acp.ultimate.page.edit{/lang}" class="balloonTooltip" /></a>
                            {else}
                                <img src="{@RELATIVE_WCF_DIR}icon/edit1D.svg" alt="" title="{lang}wcf.acp.ultimate.page.edit{/lang}" />
                            {/if}
                            {if $__wcf->session->getPermission('admin.content.ultimate.canDeletePage')}
                                <a onclick="return confirm('{lang}wcf.acp.ultimate.page.delete.sure{/lang}')" href="{link controller='UltimatePageDelete' id=$page['pageID']}url={@$encodedURL}&t={@SECURITY_TOKEN}{/link}"><img src="{@RELATIVE_WCF_DIR}icon/delete1.svg" alt="" title="{lang}wcf.acp.ultimate.page.delete{/lang}" class="balloonTooltip" /></a>
                            {else}
                                <img src="{@RELATIVE_WCF_DIR}icon/delete1D.svg" alt="" title="{lang}wcf.acp.ultimate.page.delete{/lang}" />
                            {/if}
                    
                            {event name='buttons'}
                        </td>
                        <td class="columnID"><p>{@$page->pageID}</p></td>
                        <td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditPage')}<a title="{lang}wcf.acp.ultimate.page.edit{/lang}" href="{link controller='UltimatePageEdit' id=$page->pageID}{/link}">{lang}{@$page->pageTitle}{/lang}</a>{else}{lang}{@$page->pageTitle}{/lang}{/if}</p></td>
                		<td class="columnAuthor"><p>{if $__wcf->session->getPermission('admin.user.canEditUser')}<a title="{lang}wcf.acp.user.edit{/lang}" href="{link controller='UserEdit' id=$page->authorID}{/link}">{@$page->author->username}</a>{else}{@$page->author->username}{/if}</p></td>
                		<td class="columnLastModified"><p>{@$page->lastModified|time}</p></td>
                		
                        {event name='columns'}
                    </tr>
                {/foreach}
            {/content}
        </tbody>
    </table>
    
</div>
    
<div class="contentFooter">
    {@$pagesLinks}
    
    <div class="clipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.page' ]"></div>
     
    <nav>
        <ul class="largeButtons">
            {if $__wcf->session->getPermission('admin.content.ultimate.canAddPage')}
                <li><a href="{link controller='UltimatePageAdd'}{/link}" title="{lang}wcf.acp.ultimate.page.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}wcf.acp.ultimate.page.add{/lang}</span></a></li>
            {/if}
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>
{hascontentelse}
</div>

<p class="info">{lang}wcf.acp.ultimate.page.noContents{/lang}</p>
{/hascontent}

{include file='footer'}
 