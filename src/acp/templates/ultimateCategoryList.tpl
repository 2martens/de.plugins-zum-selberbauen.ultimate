{include file='header'}

<script type="text/javascript">
    //<![CDATA[
    $(function() {
        WCF.Clipboard.init('ultimate\\acp\\page\\UltimateCategoryListPage', {@$hasMarkedItems});
        new ULTIMATE.ACP.Category.List();
    });
    //]]>
</script>

<header class="mainHeading">
    {*<img src="{@RELATIVE_WCF_DIR}icon/{if $searchID}search{else}user{/if}1.svg" alt="" />*}
    <hgroup>
        <h1>{lang}wcf.acp.ultimate.category.list{/lang}</h1>
    </hgroup>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentHeader">
    {pages print=true assign=pagesLinks controller='UltimateCategoryList' link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
    
    <nav>
        <ul class="largeButtons">
            {if $__wcf->session->getPermission('admin.content.ultimate.canAddCategory')}
                <li><a href="{link controller='UltimateCategoryAdd'}{/link}" title="{lang}wcf.acp.ultimate.category.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}wcf.acp.ultimate.category.add{/lang}</span></a></li>
            {/if}
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

<div class="border boxTitle">
    <nav class="menu">
        <ul>
            <li{if $action == ''} class="active"{/if}><a href="{link controller='UltimateCategoryList'}{/link}"><span>{lang}wcf.acp.ultimate.category.list.all{/lang}</span> <span class="badge" title="{lang}wcf.acp.ultimate.category.list.count{/lang}">{#$items}</span></a></li>
            
            {event name='ultimateCategoryListOptions'}
        </ul>
    </nav>
    {hascontent}
    <table class="clipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.category">
        <thead>
            <tr class="tableHead">
                <th class="columnMark"><label><input type="checkbox" class="clipboardMarkAll" /></label></th>
                <th class="columnID{if $sortField == 'categoryID'} active{/if}" colspan="2"><a href="{link controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categoryID&sortOrder={if $sortField == 'categoryID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}{if $sortField == 'categoryID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                <th class="columnTitle{if $sortField == 'categoryTitle'} active{/if}"><a href="{link controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categoryTitle&sortOrder={if $sortField == 'categoryTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.category.title{/lang}{if $sortField == 'categoryTitle'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                <th class="columnDescription{if $sortField == 'categoryDescription'} active{/if}"><a href="{link controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categoryDescription&sortOrder={if $sortField == 'categoryDescription' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.category.description{/lang}{if $sortField == 'categoryDescription'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                <th class="columnSlug{if $sortField == 'categorySlug'} active{/if}"><a href="{link controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categorySlug&sortOrder={if $sortField == 'categorySlug' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate..category.slug{/lang}{if $sortField == 'categorySlug'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                <th class="columnContents{if $sortField == 'categoryContents'} active{/if}"><a href="{link controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categoryContents&sortOrder={if $sortField == 'categoryContents' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.category.contents{/lang}{if $sortField == 'categoryContents'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}.svg" alt="" />{/if}</a></th>
                     
                {event name='headColumns'}
            </tr>
        </thead>
        
        <tbody>
            {content}
                {foreach from=$objects item=category}
                    <tr id="categoryRow{@$category->categoryID}">
                        <td class="columnMark"><input type="checkbox" class="clipboardItem" data-object-id="{@$category->categoryID}" /></td>
                        <td class="columnIcon">
                            
                            {if $__wcf->session->getPermission('admin.content.ultimate.canEditContent')}
                                <a href="{link controller='UltimateCategoryEdit' id=$category->categoryID}{/link}"><img src="{@RELATIVE_WCF_DIR}icon/edit1.svg" alt="" title="{lang}wcf.acp.ultimate.category.edit{/lang}" class="balloonTooltip" /></a>
                            {else}
                                <img src="{@RELATIVE_WCF_DIR}icon/edit1D.svg" alt="" title="{lang}wcf.acp.ultimate.category.edit{/lang}" />
                            {/if}
                            
                            {if $__wcf->session->getPermission('admin.content.ultimate.canDeleteCategory') && $category->categoryID > 1}
                                <a onclick="return confirm('{lang}wcf.acp.ultimate.category.delete.sure{/lang}')" href="{link controller='UltimateCategoryDelete' id=$category->categoryID}url={@$encodedURL}&t={@SECURITY_TOKEN}{/link}"><img src="{@RELATIVE_WCF_DIR}icon/delete1.svg" alt="" title="{lang}wcf.acp.ultimate.category.delete{/lang}" class="balloonTooltip" /></a>
                            {else}
                                <img src="{@RELATIVE_WCF_DIR}icon/delete1D.svg" alt="" title="{lang}wcf.acp.ultimate.category.delete{/lang}" />
                            {/if}
                            
                            {event name='buttons'}
                        </td>
                        <td class="columnID"><p>{@$category->categoryID}</p></td>
                        <td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canEditCategory')}<a title="{lang}wcf.acp.ultimate.category.edit{/lang}" href="{link controller='UltimateCategoryEdit' id=$category->categoryID}{/link}">{lang}{@$category->categoryTitle}{/lang}</a>{else}{lang}{@$category->categoryTitle}{/lang}{/if}</p></td>
                        <td class="columnDescription"><p>{lang}{@$category->categoryDescription}{/lang}</p></td>
                        <td class="columnSlug"><p>{@$category->categorySlug}</p></td>
                        {assign var=contents value=$category->getContents()}
                        <td class="columnContents"><p><a title="{lang}wcf.acp.ultimate.category.showContents{/lang}" href="{link controller='UltimateContentList'}categoryID={@$category->categoryID}{/link}>{$contents|count}</a></p></td>
                
                        {event name='columns'}
                    </tr>
                {/foreach}
            {/content}
        </tbody>
    </table>
    
</div>
    
<div class="contentFooter">
    {@$pagesLinks}
    
    <div class="clipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.category' ]"></div>
    
    <nav>
        <ul class="largeButtons">
            {if $__wcf->session->getPermission('admin.content.ultimate.canAddCategory')}
                <li><a href="{link controller='UltimateCategoryAdd'}{/link}" title="{lang}wcf.acp.ultimate.category.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/add1.svg" alt="" /> <span>{lang}wcf.acp.ultimate.category.add{/lang}</span></a></li>
            {/if}
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>
{hascontentelse}
</div>

<p class="info">{lang}wcf.acp.ultimate.category.noContents{/lang}</p>
{/hascontent}
{include file='footer'}
 