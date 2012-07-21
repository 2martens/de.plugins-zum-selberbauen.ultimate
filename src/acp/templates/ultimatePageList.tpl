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
                <th class="columnID{if $sortField == 'pageID'} active{/if}" colspan="2"><a href="{link controller='UltimatePageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=pageID&sortOrder={if $sortField == 'pageID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}{if $sortField == 'pageID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                <th class="columnTitle{if $sortField == 'pageSlug'} active{/if}"><a href="{link controller='UltimatePageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=pageSlug&sortOrder={if $sortField == 'pageSlug' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}ultimate.template.page.slug{/lang}{if $sortField == 'pageSlug'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                <th class="columnText{if $sortField == 'configTitle'} active{/if}"><a href="{link controller='UltimatePageList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=configTitle&sortOrder={if $sortField == 'configTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}ultimate.template.config.title{/lang}{if $sortField == 'configTitle'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                
                    
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
                        <td class="columnID"><p>{@$page['pageID']}</p></td>
                        <td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditPage')}<a title="{lang}wcf.acp.ultimate.page.edit{/lang}" href="{link controller='UltimatePageEdit' id=$page['pageID']}{/link}">{$page['pageSlug']}</a>{else}{$page['pageSlug']}{/if}</p></td>
                		<td class="columnText"><p>{$page['configTitle']}</p></td>
                		
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
 