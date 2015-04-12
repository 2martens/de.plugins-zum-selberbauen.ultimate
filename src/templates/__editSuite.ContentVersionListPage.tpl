<div id="pageContent" data-controller="ContentListPage" data-request-type="page" data-ajax-only="true">
    <script data-relocate="true" type="text/javascript">
        //<![CDATA[
        $(function() {
            var options = { };
            options.emptyMessage = '{lang}wcf.acp.ultimate.content.noVersions{/lang}';
            {if $pages > 1}
            options.refreshPage = true;
            {if $pages == $pageNo}
            options.updatePageNumber = -1;
            {/if}
            {else}
            options.emptyMessage = '{lang}wcf.acp.ultimate.content.noVersions{/lang}';
            {/if}

            new WCF.Table.EmptyTableHandler($('#contentVersionTableContainer'), 'jsContentVersionRow', options);
        });
        //]]>
    </script>

    <header class="boxHeadline">
        <h1>{lang}wcf.acp.ultimate.content.version.list{/lang}</h1>
    </header>

    {assign var=encodedURL value=$url|rawurlencode}
    {assign var=encodedAction value=$action|rawurlencode}
    <div class="contentNavigation">
        {pagesExtended print=true assign=pagesLinks application='ultimate' controller="ContentVersionList" parent="EditSuite" link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}

        {hascontent}
            <nav>
                <ul>
                    {content}
                        <li><a data-controller="ContentListPage" data-request-type="page" href="{link application='ultimate' parent='edit-suite' controller='ContentList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.content.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.content.list{/lang}</span></a></li>
                    {if $__wcf->session->getPermission('user.ultimate.editing.canAddContentVersion')}
                        <li><a data-controller="ContentVersionAddForm" data-request-type="form" href="{link controller='ContentVersionAdd' application='ultimate' parent='edit-suite' id=$contentID}{/link}" title="{lang}wcf.acp.ultimate.content.version.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.ultimate.content.version.add{/lang}</span></a></li>
                    {/if}

                    {event name='contentNavigationButtonsTop'}
                    {/content}
                </ul>
            </nav>
        {/hascontent}
    </div>
    {if $items}
        <div id="contentVersionTableContainer" class="tabularBox tabularBoxTitle marginTop jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.content.version">
            <header>
                <h2>{lang}wcf.acp.ultimate.content.version.list{/lang} <span class="counter badge badgeInverse" title="{lang}wcf.acp.ultimate.content.version.list.count{/lang}">{#$items}</span></h2>
            </header>
            <table class="table">
                <thead>
                <tr>
                    <th class="columnID{if $sortField == 'versionNumber'} active {@$sortOrder}{/if}" colspan="2"><a data-controller="ContentVersionListPage" data-request-type="page" href="{link controller='ContentVersionList' application='ultimate' parent='edit-suite' id=$contentID}pageNo={@$pageNo}&sortField=versionNumber&sortOrder={if $sortField == 'versionNumber' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
                    <th class="columnTitle{if $sortField == 'contentTitle'} active {@$sortOrder}{/if}"><a data-controller="ContentVersionListPage" data-request-type="page" href="{link controller='ContentVersionList' application='ultimate' parent='edit-suite' id=$contentID}pageNo={@$pageNo}&sortField=contentTitle&sortOrder={if $sortField == 'contentTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.content.title{/lang}</a></th>
                    <th class="columnAuthor{if $sortField == 'contentAuthor'} active {@$sortOrder}{/if}"><a data-controller="ContentVersionListPage" data-request-type="page" href="{link controller='ContentVersionList' application='ultimate' parent='edit-suite' id=$contentID}pageNo={@$pageNo}&sortField=contentAuthor&sortOrder={if $sortField == 'contentAuthor' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.author{/lang}</a></th>
                    <th class="columnDate{if $sortField == 'publishDate'} active {@$sortOrder}{/if}"><a data-controller="ContentVersionListPage" data-request-type="page" href="{link controller='ContentVersionList' application='ultimate' parent='edit-suite' id=$contentID}pageNo={@$pageNo}&sortField=publishDate&sortOrder={if $sortField == 'publishDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.publishDateList{/lang}</a></th>

                    {event name='headColumns'}
                </tr>
                </thead>

                <tbody>
                {foreach from=$objects item=version}
                    <tr id="contentVersionContainer{@$version->versionID}" class="jsContentVersionRow jsClipboardObject">
                        <td class="columnIcon">

                            {if $__wcf->session->getPermission('user.ultimate.editing.canEditContentVersion')}
                                <a data-controller="ContentVersionEditForm" data-request-type="form" href="{link controller='ContentVersionEdit' application='ultimate' parent='edit-suite' id=$version->versionID}{/link}"><span title="{lang}wcf.acp.ultimate.content.version.edit{/lang}" class="icon icon16 icon-pencil jsTooltip"></span></a>
                            {else}
                                <span title="{lang}wcf.acp.ultimate.content.version.edit{/lang}" class="icon icon16 icon-pencil disabled"></span>
                            {/if}
                            
                            {if $__wcf->session->getPermission('user.ultimate.editing.canDeleteContentVersion') && $items > 1}
                                <span title="{lang}wcf.acp.ultimate.content.version.delete{/lang}" class="icon icon16 icon-remove jsTooltip jsDeleteButton pointer" data-object-id="{@$version->versionNumber}" data-content-id="{@$contentID}" data-confirm-message="{lang}wcf.acp.ultimate.content.version.delete.sure{/lang}"></span>
                            {else}
                                <span title="{lang}wcf.acp.ultimate.content.version.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
                            {/if}

                            {event name='buttons'}
                        </td>
                        <td class="columnID"><p>{@$version->versionNumber}</p></td>
                        <td class="columnTitle"><p>{if $__wcf->session->getPermission('user.ultimate.editing.canEditContentVersion')}<a title="{lang}wcf.acp.ultimate.content.version.edit{/lang}" data-controller="ContentVersionEditForm" data-request-type="form"  href="{link controller='ContentVersionEdit' application='ultimate' parent='edit-suite' id=$version->versionID}{/link}">{@$version->contentTitle}</a>{else}{@$version->contentTitle}{/if}</p></td>
                        <td class="columnAuthor"><p><a data-controller="ContentVersionListPage" data-request-type="page" href="{link controller='ContentVersionList' application='ultimate' parent='edit-suite'}authorID={@$version->authorID}{/link}">{@$version->author}</a></p></td>
                        <td class="columnDate dateColumn"><p>{if $version->publishDate > 0 && $version->status >= 2}{@$version->publishDate|time}{else}{/if}</p></td>

                        {event name='columns'}
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    {else}
        <p class="info">{lang}wcf.acp.ultimate.content.noVersions{/lang}</p>
    {/if}
    <div class="contentNavigation">
        {@$pagesLinks}

        {hascontent}
            <nav>
                <ul>
                    {content}
                        <li><a data-controller="ContentListPage" data-request-type="page" href="{link application='ultimate' parent='edit-suite' controller='ContentList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.content.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.ultimate.content.list{/lang}</span></a></li>
                    {if $__wcf->session->getPermission('user.ultimate.editing.canAddContentVersion')}
                        <li><a data-controller="ContentVersionAddForm" data-request-type="form" href="{link controller='ContentVersionAdd' application='ultimate' parent='edit-suite' id=$contentID}{/link}" title="{lang}wcf.acp.ultimate.content.version.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.ultimate.content.version.add{/lang}</span></a></li>
                    {/if}

                    {event name='contentNavigationButtonsBottom'}
                    {/content}
                </ul>
            </nav>
        {/hascontent}
    </div>
</div>
