<ul class="sitemapList">
	{foreach from=$menuItems item=menuItem}
		<li>
			<a href="{$menuItem->getLink()}">{$menuItem}</a>
		
		{if $childMenuItems && $menuItem->childItems|count}
			<ul>
				{foreach from=$menuItem->childItems item=childItem}
					{if $childItem->menuItemController == null}
						<li>
							<a href="{$childItem->getLink()}">{$childItem}</a>
						</li>
					{/if}
				{/foreach}
			</ul>
		{else}
			<ul></ul>
		{/if}
		</li>
	{/foreach}
</ul>
