{include file='header'}

<script type="text/javascript">
    //<![CDATA[
    $(function() {
        var actionObjects = { };
        actionObjects['de.plugins-zum-selberbauen.ultimate.menu'] = { };
        actionObjects['de.plugins-zum-selberbauen.ultimate.menu']['delete'] = new WCF.Action.Delete('ultimate\\data\\menu\\MenuAction', $('.jsCategoryRow'), $('#menuTableContainer .wcf-menu li:first-child .wcf-badge'));
        
        WCF.Clipboard.init('ultimate\\acp\\page\\UltimateMenuListPage', {@$hasMarkedItems}, actionObjects);
        
        var options = { };
        {if $pages > 1}
            options.refreshPage = true;
        {/if}
        
        new WCF.Table.EmptyTableHandler($('#menuTableContainer'), 'jsMenuRow', options);
    });
    //]]>
</script>

<header class="boxHeadline">
    <hgroup>
        <h1>{lang}wcf.acp.ultimate.menu.list{/lang}</h1>
    </hgroup>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentNavigation">
    {pages print=true assign=pagesLinks controller='UltimateMenuList' link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
    
    <nav>
        <ul>
            {if $__wcf->session->getPermission('admin.content.ultimate.canAddMenu')}
                <li><a href="{link controller='UltimateMenuAdd'}{/link}" title="{lang}wcf.acp.ultimate.menu.add{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/add.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.ultimate.menu.add{/lang}</span></a></li>
            {/if}
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

<div id="menuTableContainer" class="tabularBox marginTop shadow">
    <nav class="wcf-menu">
        <ul>
            <li{if $action == ''} class="active"{/if}><a href="{link controller='UltimateMenuList'}{/link}"><span>{lang}wcf.acp.ultimate.menu.list.all{/lang}</span> <span class="wcf-badge" title="{lang}wcf.acp.ultimate.menu.list.count{/lang}">{#$items}</span></a></li>
            
            {event name='ultimateMenuListOptions'}
        </ul>
    </nav>
    {hascontent}
        <table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.menu">
            <thead>
                <tr>
                    <th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
                    <th class="columnID{if $sortField == 'menuID'} active{/if}" colspan="2"><a href="{link controller='UltimateMenuList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=menuID&sortOrder={if $sortField == 'menuID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}{if $sortField == 'menuID'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                    <th class="columnTitle{if $sortField == 'menuName'} active{/if}"><a href="{link controller='UltimateMenuList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=menuName&sortOrder={if $sortField == 'menuName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.menu.name{/lang}{if $sortField == 'menuName'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                         
                    {event name='headColumns'}
                </tr>
            </thead>
            
            <tbody>
                {content}
                    {foreach from=$objects item=menu}
                        <tr id="menuContainer{@$menu->menuID}" class="jsMenuRow">
                            <td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$menu->menuID}" /></td>
                            <td class="columnIcon">
                                
                                {if $__wcf->session->getPermission('admin.content.ultimate.canEditMenu')}
                                    <a href="{link controller='UltimateMenuEdit' id=$menu->menuID}{/link}"><img src="{@$__wcf->getPath()}icon/edit.svg" alt="" title="{lang}wcf.acp.ultimate.menu.edit{/lang}" class="icon16 jsTooltip" /></a>
                                {else}
                                    <img src="{@$__wcf->getPath()}icon/edit.svg" alt="" title="{lang}wcf.acp.ultimate.menu.edit{/lang}" class="icon16 disabled" />
                                {/if}
                                
                                {if $__wcf->session->getPermission('admin.content.ultimate.canDeleteMenu') && $menu->menuID > 1}
                                    <a onclick="return confirm('{lang}wcf.acp.ultimate.menu.delete.sure{/lang}')" href="{link controller='UltimateMenuDelete' id=$menu->menuID}url={@$encodedURL}&t={@SECURITY_TOKEN}{/link}"><img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.acp.ultimate.menu.delete{/lang}" class="icon16 jsTooltip" /></a>
                                {else}
                                    <img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.acp.ultimate.menu.delete{/lang}" class="icon16 disabled" />
                                {/if}
                                
                                {event name='buttons'}
                            </td>
                            <td class="columnID"><p>{@$menu->menuID}</p></td>
                            <td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditMenu')}<a title="{lang}wcf.acp.ultimate.menu.edit{/lang}" href="{link controller='UltimateMenuEdit' id=$menu->menuID}{/link}">{lang}{@$menu->menuName}{/lang}</a>{else}{lang}{@$menu->menuName}{/lang}{/if}</p></td>
                            
                            {event name='columns'}
                        </tr>
                    {/foreach}
                {/content}
            </tbody>
        </table>        
    </div>
        
    <div class="contentNavigation">
        {@$pagesLinks}
        
        <div class="wcf-clipboardEditor jsClipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.menu' ]"></div>
        
        <nav>
            <ul>
                {if $__wcf->session->getPermission('admin.content.ultimate.canAddMenu')}
                    <li><a href="{link controller='UltimateMenuAdd'}{/link}" title="{lang}wcf.acp.ultimate.menu.add{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/add.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.ultimate.menu.add{/lang}</span></a></li>
                {/if}
                
                {event name='largeButtons'}
            </ul>
        </nav>
    </div>
{hascontentelse}
</div>
    
<p class="wcf-info">{lang}wcf.acp.ultimate.menu.noContents{/lang}</p>
{/hascontent}

{include file='footer'}
 