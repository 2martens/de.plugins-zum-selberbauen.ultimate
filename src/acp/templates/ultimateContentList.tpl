{include file='header'}

<script type="text/javascript">
    //<![CDATA[
    $(function() {
        WCF.Clipboard.init('ultimate\\acp\\page\\UltimateContentListPage', {@$hasMarkedItems});
        new ULTIMATE.ACP.Content.List();
    });
    //]]>
</script>

<header class="mainHeading">
    {*<img src="{@RELATIVE_WCF_DIR}icon/{if $searchID}search{else}user{/if}1.svg" alt="" />*}
    <hgroup>
        <h1>{lang}wcf.acp.ultimate.content.list{/lang}</h1>
    </hgroup>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentHeader">
    {pages print=true assign=pagesLinks controller="UltimateContentList" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
    
    <nav>
        <ul class="largeButtons">
            {if $__wcf->session->getPermission('admin.content.ultimate.canAddContent')}
                <li><a href="{link controller='UltimateContentAdd'}{/link}" title="{lang}wcf.acp.ultimate.content.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}wcf.acp.ultimate.content.add{/lang}</span></a></li>
            {/if}
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

<div class="border boxTitle">
    <nav class="menu">
        <ul>
            <li{if $action == ''} class="active"{/if}><a href="{link controller='UltimateContentList'}{/link}"><span>{lang}wcf.acp.ultimate.content.list.all{/lang}</span> <span class="badge" title="{lang}wcf.acp.ultimate.content.list.count{/lang}">{#$items}</span></a></li>
            
            {event name='ultimateContentListOptions'}
        </ul>
    </nav>
    {hascontent}
    <table class="clipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.content">
        <thead>
            <tr class="tableHead">
                <th class="columnMark"><label><input type="checkbox" class="clipboardMarkAll" /></label></th>
                <th class="columnID{if $sortField == 'contentID'} active{/if}" colspan="2"><a href="{link controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentID&sortOrder={if $sortField == 'contentID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}{if $sortField == 'contentID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                <th class="columnTitle{if $sortField == 'contentTitle'} active{/if}"><a href="{link controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=contentTitle&sortOrder={if $sortField == 'contentTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}ultimate.template.content.title{/lang}{if $sortField == 'contentTitle'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                
                {foreach from=$columnHeads key=column item=columnLanguageVariable}
                    <th class="column{$column|ucfirst}{if $sortField == $column} active{/if}"><a href="{link controller='UltimateContentList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField={$column}&sortOrder={if $sortField == $column && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}{$columnLanguageVariable}{/lang}{if $sortField == $column} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                {/foreach}
                    
                {event name='headColumns'}
            </tr>
        </thead>
        
        <tbody>
            {content}
                {foreach from=$objects item=content}
                    <tr id="contentRow{@$content->contentID}">
                        <td class="columnMark"><input type="checkbox" class="clipboardItem" data-object-id="{@$content->contentID}" /></td>
                        <td class="columnIcon">
                            {if $__wcf->session->getPermission('admin.content.ultimate.canEditContent')}
                                <a href="{link controller='UltimateContentEdit' id=$content->contentID}{/link}"><img src="{@RELATIVE_WCF_DIR}icon/edit1.svg" alt="" title="{lang}wcf.acp.ultimate.content.edit{/lang}" class="balloonTooltip" /></a>
                            {else}
                                <img src="{@RELATIVE_WCF_DIR}icon/edit1D.svg" alt="" title="{lang}wcf.acp.ultimate.content.edit{/lang}" />
                            {/if}
                            {if $__wcf->session->getPermission('admin.content.ultimate.canDeleteContent')}
                                <a onclick="return confirm('{lang}wcf.acp.ultimate.content.delete.sure{/lang}')" href="{link controller='UltimateContentDelete' id=$content->contentID}url={@$encodedURL}{/link}"><img src="{@RELATIVE_WCF_DIR}icon/delete1.svg" alt="" title="{lang}wcf.acp.ultimate.content.delete{/lang}" class="balloonTooltip" /></a>
                            {else}
                                <img src="{@RELATIVE_WCF_DIR}icon/delete1D.svg" alt="" title="{lang}wcf.acp.ultimate.content.delete{/lang}" />
                            {/if}
                    
                            {event name='buttons'}
                        </td>
                        <td class="columnID"><p>{@$content->contentID}</p></td>
                        <td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditContent')}<a title="{lang}wcf.acp.ultimate.content.edit{/lang}" href="{link controller='UltimateContentEdit' id=$content->contentID}{/link}">{$content->contentTitle}</a>{else}{$content->contentTitle}{/if}</p></td>
                
                        {foreach from=$columnHeads key=column item=columnLanguageVariable}
                            <td class="column{$column|ucfirst}"><p>{if $columnValues[$content->contentID][$column]|isset}{@$columnValues[$content->contentID][$column]}{/if}</p></td>
                        {/foreach}
                
                        {event name='columns'}
                    </tr>
                {/foreach}
            {/content}
        </tbody>
    </table>
    {hascontentelse}
    <p class="info">{lang}wcf.acp.ultimate.content.noContents{/lang}</p>
    {/hascontent}
</div>
    
<div class="contentFooter">
    {@$pagesLinks}
    
    <div class="clipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.content' ]"></div>
     
    <nav>
        <ul class="largeButtons">
            {if $__wcf->session->getPermission('admin.content.ultimate.canAddContent')}
                <li><a href="{link controller='UltimateContentAdd'}{/link}" title="{lang}wcf.acp.ultimate.content.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}wcf.acp.ultimate.content.add{/lang}</span></a></li>
            {/if}
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

{include file='footer'}
 