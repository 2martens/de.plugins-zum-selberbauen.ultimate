{include file='header'}

<script type="text/javascript">
    /* <![CDATA[ */
    $(function() {
        WCF.TabMenu.init();
    });
    /* ]]> */
</script>

<header class="boxHeadline">
    <hgroup>
        <h1>{lang}wcf.acp.ultimate.menu.{@$action}{/lang}</h1>
    </hgroup>
</header>

{if $errorField}
    <p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
    <p class="success">{lang}wcf.global.form.{@$action}.success{/lang}</p>
{/if}

<div class="contentNavigation">
    <nav>
        <ul>
            <li><a href="{link controller='UltimateMenuList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.menu.list{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/list.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.menu.link.ultimate.menu.list{/lang}</span></a></li>
            
            {event name='largeButtons'}
        </ul>
    </nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='UltimateMenuAdd'}{/link}{else}{link controller='UltimateMenuEdit'}{/link}{/if}">
    <div class="container containerPadding marginTop shadow">
        <fieldset>
            <legend>{lang}wcf.acp.ultimate.menu.general{/lang}</legend>
            <dl{if $errorField == 'menuName'} class="formError"{/if}>
                <dt><label for="menuName">{lang}wcf.acp.ultimate.menu.name{/lang}</label></dt>
                <dd>
                    <input type="text" id="menuName" name="menuName" value="{@$menuName}" class="long" required="required" placeholder="{lang}wcf.acp.ultimate.menu.name.placeholder{/lang}" />
                    {if $errorField == 'menuName'}
                        <small class="innerError">
                            {if $errorType == 'empty'}
                                {lang}wcf.global.form.error.empty{/lang}
                            {else}
                                {lang}wcf.acp.ultimate.menu.name.error.{@$errorType}{/lang}
                            {/if}
                        </small>
                    {/if}
                </dd>
            </dl>
        </fieldset>
        <fieldset>
            <legend>{lang}wcf.acp.ultimate.menu.items{/lang}</legend>
            <div id="menuItemList" class="container containerPadding marginTop shadow{if $objectType->getProcessor()->canEditMenuItem() && $menuItemNodeList|count > 1} sortableListContainer{/if}">
                <ol class="sortableList" data-object-id="0">
                    {assign var=oldDepth value=0}
                    {foreach from=$menuItemNodeList item=menuItem}
                        {section name=i loop=$oldDepth-$menuItemNodeList->getDepth()}</ol></li>{/section}
                
                        <li class="{if $__wcf->session->getPermission('admin.content.ultimate.canEditMenuItem') && $menuItemNodeList|count > 1}sortableNode {if $menuItemNodeList->getDepth() == $objectType->getProcessor()->getMaximumNestingLevel()}sortableNoNesting {/if}{/if}jsMenuItem" data-object-id="{@$menuItem->menuItemID}"{* {if $collapsedMenuItemIDs|is_array} data-is-open="{if $collapsedMenuItemIDs[$menuItem->menuItemID]|isset}0{else}1{/if}"{/if} *}>
                            <span class="sortableNodeLabel">
                                <span class="buttons">
                                    
                                    {if $__wcf->session->getPermission('admin.content.ultimate.canDeleteMenuItem')}
                                        <img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.global.button.delete{/lang}" class="icon16 jsDeleteButton jsTooltip" data-object-id="{@$menuItem->menuItemID}" data-confirm-message="{lang}'wcf.acp.ultimate.menu.item.delete.sure'{/lang}" />
                                    {else}
                                        <img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.global.button.delete{/lang}" class="icon16 disabled" />
                                    {/if}

                                    {if $__wcf->session->getPermission('admin.content.ultimate.canEditMenuItem')}
                                        {* todo: toggle icons aren't clickable *}
                                        <img src="{@$__wcf->getPath()}icon/{if !$menuItem->isDisabled}enabled{else}disabled{/if}.svg" alt="" title="{lang}wcf.global.button.{if !$menuItem->isDisabled}disable{else}enable{/if}{/lang}" class="icon16 jsToggleButton jsTooltip" data-object-id="{@$menuItem->menuItemID}" />
                                    {else}
                                        <img src="{@$__wcf->getPath()}icon/{if !$menuItem->isDisabled}enabled{else}disabled{/if}.svg" alt="" title="{lang}wcf.global.button.{if !$menuItem->isDisabled}enable{else}disable{/if}{/lang}" class="icon16 disabled" />
                                    {/if}

                                    {event name='buttons'}
                                </span>

                                <span class="title">
                                    {lang}{@$menuItem->menuItemName}{/lang}
                                </span>
                            </span>
                    
                            <ol class="menuItemList sortableList" data-object-id="{@$menuItem->menuItemID}">
                        {if !$menuItemNodeList->current()->hasChildren()}
                            </ol></li>
                        {/if}
                        {assign var=oldDepth value=$menuItemNodeList->getDepth()}
                    {/foreach}
                    {section name=i loop=$oldDepth}</ol></li>{/section}  
                </ol>
                {if $__wcf->session->getPermission('admin.content.ultimate.canEditMenuItem') && menuItemNodeList|count > 1}
                    <div class="formSubmit">
                        <button class="button default" data-type="submit">{lang}wcf.global.button.save{/lang}</button>
                    </div>
                {/if}
            </div>
            
            <script type="text/javascript">
            /* <![CDATA[ */
                $(function() {
                    {if $__wcf->session->getPermission('admin.content.ultimate.canDeleteMenuItem')}
                        new WCF.Action.Delete('ultimate\\data\\menu\\item\\MenuItemAction', $('.jsMenuItem'));
                    {/if}
                    {if $__wcf->session->getPermission('admin.content.ultimate.canEditMenuItem')}
                        new WCF.Action.Toggle('ultimate\\data\\menu\\item\\MenuItemAction', $('.jsMenuItem'), '> .buttons > .jsToggleButton');
                        {if $menuItemNodeList|count > 1}
                            var sortableNodes = $('.sortableNode');
                            sortableNodes.each(function(index, node) {
                                $(node).wcfIdentify();
                            });
                            new WCF.Sortable.List('menuItemList', 'ultimate\\data\\menu\\item\\MenuItemAction', 0, { }, false);
                        {/if}
                    {/if}
                });
            /* ]]> */
            </script>
                
        </fieldset>    
        {event name='fieldsets'}
    </div>
        
    <div class="formSubmit">
        <input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
        {@SID_INPUT_TAG}
        <input type="hidden" name="action" value="{@$action}" />
        {if $menuID|isset}<input type="hidden" name="id" value="{@$menuID}" />{/if}
    </div>
</form>

{include file='footer'}