{include file='header'}

<script type="text/javascript">
    //<![CDATA[
    $(function() {
        WCF.Clipboard.init('ultimate\\acp\\page\\UltimateLinkListPage', {@$hasMarkedItems});
        new ULTIMATE.ACP.Link.List();
    });
    //]]>
</script>

<header class="mainHeading">
    {*<img src="{@RELATIVE_WCF_DIR}icon/{if $searchID}search{else}user{/if}1.svg" alt="" />*}
    <hgroup>
        <h1>{lang}wcf.acp.ultimate.link.list{/lang}</h1>
    </hgroup>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentHeader">
    {pages print=true assign=pagesLinks controller="UltimateLinkList" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
    
    <nav>
        <ul class="largeButtons">
            {if $__wcf->session->getPermission('admin.content.ultimate.canAddLink')}
                <li><a href="{link controller='UltimateLinkAdd'}{/link}" title="{lang}wcf.acp.ultimate.link.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}wcf.acp.ultimate.link.add{/lang}</span></a></li>
            {/if}
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

<div class="border boxTitle">
    <nav class="menu">
        <ul>
            <li{if $action == ''} class="active"{/if}><a href="{link controller='UltimateLinkList'}{/link}"><span>{lang}wcf.acp.ultimate.link.list.all{/lang}</span> <span class="badge" title="{lang}wcf.acp.ultimate.link.list.count{/lang}">{#$items}</span></a></li>
            
            {event name='ultimateContentListOptions'}
        </ul>
    </nav>
    {hascontent}
    <table class="clipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.link">
        <thead>
            <tr class="tableHead">
                <th class="columnMark"><label><input type="checkbox" class="clipboardMarkAll" /></label></th>
                <th class="columnID{if $sortField == 'linkID'} active{/if}" colspan="2"><a href="{link controller='UltimateLinkList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=linkID&sortOrder={if $sortField == 'linkID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}{if $sortField == 'linkID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                <th class="columnTitle{if $sortField == 'linkSlug'} active{/if}"><a href="{link controller='UltimateLinkList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=linkSlug&sortOrder={if $sortField == 'linkSlug' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}ultimate.template.link.slug{/lang}{if $sortField == 'linkSlug'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                <th class="columnText{if $sortField == 'configTitle'} active{/if}"><a href="{link controller='UltimateLinkList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=configTitle&sortOrder={if $sortField == 'configTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}ultimate.template.config.title{/lang}{if $sortField == 'configTitle'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                
                {foreach from=$columnHeads key=column item=columnLanguageVariable}
                    <th class="column{$column|ucfirst}{if $sortField == $column} active{/if}"><a href="{link controller='UltimateLinkList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField={$column}&sortOrder={if $sortField == $column && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}{$columnLanguageVariable}{/lang}{if $sortField == $column} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                {/foreach}
                    
                {event name='headColumns'}
            </tr>
        </thead>
        
        <tbody>
            {content}
                {foreach from=$objects item=link}
                    <tr id="contentRow{@$link['linkID']}">
                        <td class="columnMark"><input type="checkbox" class="clipboardItem" data-object-id="{@$link['linkID']}" /></td>
                        <td class="columnIcon">
                            {if $__wcf->session->getPermission('admin.content.ultimate.canEditLink')}
                                <a href="{link controller='UltimateLinkEdit' id=$link['linkID']}{/link}"><img src="{@RELATIVE_WCF_DIR}icon/edit1.svg" alt="" title="{lang}wcf.acp.ultimate.link.edit{/lang}" class="balloonTooltip" /></a>
                            {else}
                                <img src="{@RELATIVE_WCF_DIR}icon/edit1D.svg" alt="" title="{lang}wcf.acp.ultimate.link.edit{/lang}" />
                            {/if}
                            {if $__wcf->session->getPermission('admin.content.ultimate.canDeleteLink')}
                                <a onclick="return confirm('{lang}wcf.acp.ultimate.link.delete.sure{/lang}')" href="{link controller='UltimateLinkDelete' id=$link['linkID']}url={@$encodedURL}{/link}"><img src="{@RELATIVE_WCF_DIR}icon/delete1.svg" alt="" title="{lang}wcf.acp.ultimate.link.delete{/lang}" class="balloonTooltip" /></a>
                            {else}
                                <img src="{@RELATIVE_WCF_DIR}icon/delete1D.svg" alt="" title="{lang}wcf.acp.ultimate.link.delete{/lang}" />
                            {/if}
                    
                            {event name='buttons'}
                        </td>
                        <td class="columnID"><p>{@$link['linkID']}</p></td>
                        <td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditLink')}<a title="{lang}wcf.acp.ultimate.link.edit{/lang}" href="{link controller='UltimateLinkEdit' id=$link['linkID']}{/link}">{$link['linkSlug']}</a>{else}{$link['linkSlug']}{/if}</p></td>
                		<td class="columnText"><p>{$link['configTitle']}</p></td>
                		
                        {foreach from=$columnHeads key=column item=columnLanguageVariable}
                            <td class="column{$column|ucfirst}"><p>{if $columnValues[$link['linkID']][$column]|isset}{@$columnValues[$link['linkID']][$column]}{/if}</p></td>
                        {/foreach}
                
                        {event name='columns'}
                    </tr>
                {/foreach}
            {/content}
        </tbody>
    </table>
    {hascontentelse}
    <p class="info">{lang}wcf.acp.ultimate.link.noContents{/lang}</p>
    {/hascontent}
</div>
    
<div class="contentFooter">
    {@$pagesLinks}
    
    <div class="clipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.link' ]"></div>
     
    <nav>
        <ul class="largeButtons">
            {if $__wcf->session->getPermission('admin.content.ultimate.canAddLink')}
                <li><a href="{link controller='UltimateLinkAdd'}{/link}" title="{lang}wcf.acp.ultimate.link.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}wcf.acp.ultimate.link.add{/lang}</span></a></li>
            {/if}
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

{include file='footer'}
 