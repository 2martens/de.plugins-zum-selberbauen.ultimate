<div class="block block-type-navigation">
    <!-- main menu -->
    {if $__wcf->getCustomMenu()->getMenuItems('')|count > 0}
    <nav id="mainMenu" class="mainMenu">
        <ul>
            {foreach from=$__wcf->getCustomMenu()->getMenuItems('') item=menuItem}
                <li{if $__wcf->getCustomMenu()->getActiveMenuItem() == $menuItem->menuItemName} class="active"{/if}><a href="{$menuItem->getProcessor()->getLink()}">{lang}{$menuItem->menuItemName}{/lang}{if $menuItem->getProcessor()->getNotifications()} <span class="badge {if $__wcf->getCustomMenu()->getActiveMenuItem() == $menuItem->menuItemName}badgeUpdate{else}badgeInverse{/if}">{#$menuItem->getProcessor()->getNotifications()}</span>{/if}</a></li>
            {/foreach}
        </ul>
    </nav>
    {/if}
    <!-- /main menu -->

    <!-- navigation -->
    <nav class="navigation navigationHeader clearfix">
        <!-- sub menu -->
        {foreach from=$__wcf->getCustomMenu()->getMenuItems('') item=menuItem}
            {if $__wcf->getCustomMenu()->getMenuItems($menuItem->menuItemName)|count > 0 && $__wcf->getPageMenu()->getActiveMenuItem() == $menuItem->menuItem}
                <ul>
                    {foreach from=$__wcf->getCustomMenu()->getMenuItems($menuItem->menuItemName) item=subMenuItem}
                        <li><a href="{$subMenuItem->getProcessor()->getLink()}"><span>{lang}{$subMenuItem->menuItemName}{/lang}</span></a>{if $subMenuItem->getProcessor()->getNotifications()} <span class="wcf-badge">{#$subMenuItem->getProcessor()->getNotifications()}</span>{/if}</li>
                    {/foreach}
                </ul>
            {/if}
        {/foreach}
        <!-- /sub menu -->
    
        <ul class="navigationIcons">
            <li id="toBottomLink"><a href="{$__wcf->getAnchor('bottom')}" title="{lang}wcf.global.scrollDown{/lang}" class="jsTooltip"><img src="{icon size='S'}circleArrowDownColored{/icon}" alt="" class="icon16" /> <span class="invisible">{lang}wcf.global.scrollDown{/lang}</span></a></li>
            <li id="sitemap"><a title="{lang}wcf.sitemap.title{/lang}" class="jsTooltip"><img src="{icon size='S'}switchColored{/icon}" alt="" class="icon16" /> <span class="invisible">{lang}wcf.sitemap.title{/lang}</span></a></li>
        </ul>
    </nav>
    <!-- /navigation -->
</div>