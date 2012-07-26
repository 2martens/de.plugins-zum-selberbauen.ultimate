{include file='header'}

<script type="text/javascript">
    //<![CDATA[
    $(function() {
        var actionObjects = { };
        actionObjects['de.plugins-zum-selberbauen.ultimate.category'] = { };
        actionObjects['de.plugins-zum-selberbauen.ultimate.category']['delete'] = new WCF.Action.Delete('ultimate\\data\\category\\CategoryAction', $('.jsCategoryRow'), $('#categoryTableContainer .wcf-menu li:first-child .wcf-badge'));
        
        WCF.Clipboard.init('ultimate\\acp\\page\\UltimateCategoryListPage', {@$hasMarkedItems}, actionObjects);
        
        var options = { };
        {if $pages > 1}
            options.refreshPage = true;
        {/if}
        
        new WCF.Table.EmptyTableHandler($('#categoryTableContainer'), 'jsCategoryRow', options);
    });
    //]]>
</script>

<header class="boxHeadline">
    <hgroup>
        <h1>{lang}wcf.acp.ultimate.category.list{/lang}</h1>
    </hgroup>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentNavigation">
    {pages print=true assign=pagesLinks controller='UltimateCategoryList' link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
    
    <nav>
        <ul>
            {if $__wcf->session->getPermission('admin.content.ultimate.canAddCategory')}
                <li><a href="{link controller='UltimateCategoryAdd'}{/link}" title="{lang}wcf.acp.ultimate.category.add{/lang}" class="button"><img src="{@RELATIVE_WCF_DIR}icon/add.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.ultimate.category.add{/lang}</span></a></li>
            {/if}
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>


<div id="categoryTableContainer" class="tabularBox tabularBoxTitle marginTop shadow">
    <nav class="wcf-menu">
        <ul>
            <li{if $action == ''} class="active"{/if}><a href="{link controller='UltimateCategoryList'}{/link}"><span>{lang}wcf.acp.ultimate.category.list.all{/lang}</span> <span class="wcf-badge badgeInverse" title="{lang}wcf.acp.ultimate.category.list.count{/lang}">{#$items}</span></a></li>
            
            {event name='ultimateCategoryListOptions'}
        </ul>
    </nav>
    {hascontent}
        <table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.category">
            <thead>
                <tr>
                    <th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
                    <th class="columnID{if $sortField == 'categoryID'} active{/if}" colspan="2"><a href="{link controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categoryID&sortOrder={if $sortField == 'categoryID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}{if $sortField == 'categoryID'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                    <th class="columnTitle{if $sortField == 'categoryTitle'} active{/if}"><a href="{link controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categoryTitle&sortOrder={if $sortField == 'categoryTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.category.title{/lang}{if $sortField == 'categoryTitle'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                    <th class="columnDescription{if $sortField == 'categoryDescription'} active{/if}"><a href="{link controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categoryDescription&sortOrder={if $sortField == 'categoryDescription' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.category.description{/lang}{if $sortField == 'categoryDescription'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                    <th class="columnSlug{if $sortField == 'categorySlug'} active{/if}"><a href="{link controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categorySlug&sortOrder={if $sortField == 'categorySlug' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate..category.slug{/lang}{if $sortField == 'categorySlug'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                    <th class="columnDigits columnContents{if $sortField == 'categoryContents'} active{/if}"><a href="{link controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categoryContents&sortOrder={if $sortField == 'categoryContents' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.category.contents{/lang}{if $sortField == 'categoryContents'} <img src="{@$__wcf->getPath()}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                        
                    {event name='headColumns'}
                </tr>
            </thead>
            
            <tbody>
                {content}
                    {foreach from=$objects item=category}
                        <tr id="categoryContainer{@$category->categoryID}" class="jsCategoryRow">
                            <td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$category->categoryID}" /></td>
                            <td class="columnIcon">
                                
                                {if $__wcf->session->getPermission('admin.content.ultimate.canEditContent')}
                                    <a href="{link controller='UltimateCategoryEdit' id=$category->categoryID}{/link}"><img src="{@$__wcf->getPath()}icon/edit.svg" alt="" title="{lang}wcf.acp.ultimate.category.edit{/lang}" class="icon16 jsTooltip" /></a>
                                {else}
                                    <img src="{@$__wcf->getPath()}icon/edit.svg" alt="" title="{lang}wcf.acp.ultimate.category.edit{/lang}" class="icon16 disabled" />
                                {/if}
                                
                                {if $__wcf->session->getPermission('admin.content.ultimate.canDeleteCategory') && $category->categoryID > 1}
                                    <a onclick="return confirm('{lang}wcf.acp.ultimate.category.delete.sure{/lang}')" href="{link controller='UltimateCategoryDelete' id=$category->categoryID}url={@$encodedURL}&t={@SECURITY_TOKEN}{/link}"><img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.acp.ultimate.category.delete{/lang}" class="icon16 jsTooltip" /></a>
                                {else}
                                    <img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.acp.ultimate.category.delete{/lang}" class="icon16 disabled" />
                                {/if}
                                
                                {event name='buttons'}
                            </td>
                            <td class="columnID"><p>{@$category->categoryID}</p></td>
                            <td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditCategory')}<a title="{lang}wcf.acp.ultimate.category.edit{/lang}" href="{link controller='UltimateCategoryEdit' id=$category->categoryID}{/link}">{lang}{@$category->categoryTitle}{/lang}</a>{else}{lang}{@$category->categoryTitle}{/lang}{/if}</p></td>
                            <td class="columnDescription"><p>{lang}{@$category->categoryDescription}{/lang}</p></td>
                            <td class="columnSlug"><p>{@$category->categorySlug}</p></td>
                            <td class="columnContents"><p><a title="{lang}wcf.acp.ultimate.category.showContents{/lang}" href="{link controller='UltimateContentList'}categoryID={@$category->categoryID}{/link}>{$category->contents|count}</a></p></td>
                    
                            {event name='columns'}
                        </tr>
                    {/foreach}
                {/content}
            </tbody>
        </table>
        
    </div>
        
    <div class="contentNavigation">
        {@$pagesLinks}
        
        <div class="wcf-clipboardEditor jsClipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.category' ]"></div>
        
        <nav>
            <ul>
                {if $__wcf->session->getPermission('admin.content.ultimate.canAddCategory')}
                    <li><a href="{link controller='UltimateCategoryAdd'}{/link}" title="{lang}wcf.acp.ultimate.category.add{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/add.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.ultimate.category.add{/lang}</span></a></li>
                {/if}
                
                {event name='largeButtons'}
            </ul>
        </nav>
    </div>
{hascontentelse}
</div>
    
<p class="wcf-info">{lang}wcf.acp.ultimate.category.noContents{/lang}</p>
{/hascontent}

{include file='footer'}
 