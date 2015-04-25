<nav id="mainMenu" class="mainMenu jsMobileNavigation customMenu" data-button-label="{lang}wcf.page.mainMenu{/lang}">
	<ul>
		{foreach from=$__wcf->getCustomMenu()->getMenuItems('') item=menuItem}
			<li{if $__wcf->getCustomMenu()->getActiveMenuItem() == $menuItem->menuItemName} class="active"{/if}>
				<a href="{$menuItem->getProcessor()->getLink()}">{$menuItem}{if $menuItem->getProcessor()->getNotifications()} <span class="badge badgeUpdate">{#$menuItem->getProcessor()->getNotifications()}</span>{/if}</a>
				{if $__wcf->getCustomMenu()->getMenuItems($menuItem->menuItemName)|count > 0 && $__wcf->getCustomMenu()->getActiveMenuItem() == $menuItem->menuItemName}<ul class="invisible">{*
					*}{foreach from=$__wcf->getCustomMenu()->getMenuItems($menuItem->menuItemName) item=subMenuItem}{*
						*}<li{if $__wcf->getCustomMenu()->getActiveMenuItem(1) == $subMenuItem->menuItemName} class="active"{/if}><a href="{$subMenuItem->getProcessor()->getLink()}"><span>{$subMenuItem}</span></a>{if $subMenuItem->getProcessor()->getNotifications()} <span class="badge badgeUpdate">{#$subMenuItem->getProcessor()->getNotifications()}</span>{/if}</li>{*
					*}{/foreach}{*
					*}{event name='items'}
					</ul>
				{/if}
			</li>
		{/foreach}
	</ul>
</nav>
