{foreach from=$__wcf->getCustomMenu()->getMenuItems('') item=menuItem}
	{if $__wcf->getCustomMenu()->getMenuItems($menuItem->menuItemName)|count > 0 && $__wcf->getCustomMenu()->getActiveMenuItem() == $menuItem->menuItemName}
		<ul class="navigationMenuItems">
			{foreach from=$__wcf->getCustomMenu()->getMenuItems($menuItem->menuItemName) item=subMenuItem}
				<li{if $__wcf->getCustomMenu()->getActiveMenuItem(1) == $subMenuItem->menuItemName} class="active"{/if}><a href="{$subMenuItem->getProcessor()->getLink()}"><span>{lang}{$subMenuItem->menuItemName}{/lang}</span></a>{if $subMenuItem->getProcessor()->getNotifications()} <span class="badge badgeUpdate">{#$subMenuItem->getProcessor()->getNotifications()}</span>{/if}</li>
			{/foreach}
			{event name='items'}
		</ul>
	{/if}
{/foreach}