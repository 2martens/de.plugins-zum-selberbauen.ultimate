<a id="top"></a>
<!-- HEADER -->
<header id="pageHeader" class="layoutFluid">
	<div>
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
			<a href="{link controller='Index'}{/link}">
				<img src="{@$__wcf->getPath('wbb')}images/wbbLogo2.svg" alt="" style="height: 90px; width: 246px;" />
				{*event name='headerLogo'*}
			</a>
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
			{include file='mainMenu'}
		{/if}
		<!-- /main menu -->
	
		<!-- navigation -->
		<nav class="navigation navigationHeader clearfix">
			<!-- sub menu -->
			{if $__wcf->getCustomMenu()->getMenuItems('')|count > 0}
				{foreach from=$__wcf->getCustomMenu()->getMenuItems('') item=menuItem}
					{if $__wcf->getCustomMenu()->getMenuItems($menuItem->menuItemName)|count > 0 && $__wcf->getCustomMenu()->getActiveMenuItem() == $menuItem->menuItem}
						<ul>
							{foreach from=$__wcf->getCustomMenu()->getMenuItems($menuItem->menuItemName) item=subMenuItem}
								<li><a href="{$subMenuItem->getProcessor()->getLink()}"><span>{lang}{$subMenuItem->menuItemName}{/lang}</span></a>{if $subMenuItem->getProcessor()->getNotifications()} <span class="badge">{#$subMenuItem->getProcessor()->getNotifications()}</span>{/if}</li>
							{/foreach}
						</ul>
					{/if}
				{/foreach}
			{else}
				{* same goes for the sub menu *}
				{include file='mainMenuSubMenu'}
			{/if}
			<!-- /sub menu -->
		
			<ul class="navigationIcons">
				<li id="toBottomLink"><a href="{$__wcf->getAnchor('bottom')}" title="{lang}wcf.global.scrollDown{/lang}" class="jsTooltip"><img src="{icon size='S'}circleArrowDownColored{/icon}" alt="" class="icon16" /> <span class="invisible">{lang}wcf.global.scrollDown{/lang}</span></a></li>
				<li id="sitemap"><a title="{lang}wcf.sitemap.title{/lang}" class="jsTooltip"><img src="{icon}switchColored{/icon}" alt="" class="icon16" /> <span class="invisible">{lang}wcf.sitemap.title{/lang}</span></a></li>
				{event name='headerNavigation'}
			</ul>
		</nav>
		<!-- /navigation -->
		{/if}
	</div>
</header>
<!-- /HEADER -->

<!-- MAIN -->
<div id="main" class="{if $visualEditor|isset && $visualEditor}clearfix{else}layoutFluid{if $sidebarOrientation|isset && $sidebar|isset} sidebarOrientation{@$sidebarOrientation|ucfirst} clearfix{/if}{/if}">
	<div>
		{if $sidebar|isset}
			<aside class="sidebar"{if $sidebarOrientation|isset && $sidebarOrientation == 'right' && $sidebarAllowCollapsible} data-is-open="{if $sidebarCollapsed}false{else}true{/if}" data-sidebar-name="{$sidebarName}"{/if}>
				{@$sidebar}
			</aside>
			
			{if $sidebarOrientation|isset && $sidebarOrientation == 'right' && $sidebarAllowCollapsible}
				<script type="text/javascript">
					//<![CDATA[
					$(function() {
						new WCF.Collapsible.Sidebar();
					});
					//]]>
				</script>
			{/if}
		{/if}
			
		<!-- CONTENT -->
		<section id="content" class="{if $visualEditor|isset && $visualEditor}{else}content {/if}clearfix"{if $visualEditor|isset && $visualEditor}{else} style="position: relative;"{/if}>
			
			{if $skipBreadcrumbs|empty}{include file='breadcrumbs'}{/if}