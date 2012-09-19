		<!-- top menu -->
		<nav id="topMenu" class="userPanel">
			<div class="layoutFluid clearfix">
				{hascontent}
					<ul class="userPanelItems">
						{content}
						{event name='topMenu'}
						{if $visualEditor|isset && $visualEditor}{include file='visualEditorTopMenu'}{/if}
						{/content}
					</ul>
				{/hascontent}
				
				<!-- search area -->
				{event name='searchArea'}
				<!-- /search area -->
			</div>
		</nav>
		<!-- /top menu -->
		
		{if $visualEditor|isset && $visualEditor}
		{else} {* only display logo and navigation if we're not in the VisualEditor *}
		<!-- logo -->
		<div id="logo" class="logo">
			<!-- clickable area -->
			<a href="{link controller='Index'}{/link}">
				<img src="{@$__wcf->getPath()}acp/images/wcfLogo2.svg" width="300" height="80" alt="Product-logo" title="WoltLab Community Framework 2.0 Alpha 1" />
				{event name='headerLogo'}
			</a>
			<!-- /clickable area -->
		</div>
		<!-- /logo -->
		
		<!-- main menu -->
		{if $__wcf->getCustomMenu()->getMenuItems('')|count > 0}
			<nav id="mainMenu" class="mainMenu">
				<ul>
					{foreach from=$__wcf->getCustomMenu()->getMenuItems('') item=menuItem}
						<li{if $__wcf->getCustomMenu()->getActiveMenuItem() == $menuItem->menuItemName} class="active"{/if}><a href="{$menuItem->getProcessor()->getLink()}">{lang}{$menuItem->menuItemName}{/lang}{if $menuItem->getProcessor()->getNotifications()} <span class="badge {if $__wcf->getCustomMenu()->getActiveMenuItem() == $menuItem->menuItemName}badgeUpdate{else}badgeInverse{/if}">{#$menuItem->getProcessor()->getNotifications()}</span>{/if}</a></li>
					{/foreach}
				</ul>
			</nav>
		{else}
			{* if no menu is attached or no items are found then the normal page menu shall be displayed *}
			{if $__wcf->getPageMenu()->getMenuItems('header')|count > 0}
				<nav id="mainMenu" class="mainMenu">
					<ul>
						{foreach from=$__wcf->getPageMenu()->getMenuItems('header') item=menuItem}
							<li{if $__wcf->getPageMenu()->getActiveMenuItem() == $menuItem->menuItem} class="active"{/if}><a href="{$menuItem->getProcessor()->getLink()}">{lang}{$menuItem->menuItem}{/lang}{if $menuItem->getProcessor()->getNotifications()} <span class="badge {if $__wcf->getPageMenu()->getActiveMenuItem() == $menuItem->menuItem}badgeUpdate{else}badgeInverse{/if}">{#$menuItem->getProcessor()->getNotifications()}</span>{/if}</a></li>
						{/foreach}
					</ul>
				</nav>
			{/if}
		{/if}
		<!-- /main menu -->
	
		<!-- navigation -->
		<nav class="navigation navigationHeader clearfix">
			<!-- sub menu -->
			{foreach from=$__wcf->getCustomMenu()->getMenuItems('') item=menuItem}
				{if $__wcf->getCustomMenu()->getMenuItems($menuItem->menuItemName)|count > 0 && $__wcf->getCustomMenu()->getActiveMenuItem() == $menuItem->menuItem}
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
				{event name='headerNavigation'}
			</ul>
		</nav>
		<!-- /navigation -->
		{/if}