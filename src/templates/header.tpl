<a id="top"></a>
<!-- HEADER -->
<header id="pageHeader" class="{if $__wcf->getStyleHandler()->getStyle()->getVariable('useFluidLayout')}layoutFluid{else}layoutFixed{/if}">
	<div>
		<!-- top menu -->
		<nav id="topMenu" class="userPanel">
			<div class="{if $__wcf->getStyleHandler()->getStyle()->getVariable('useFluidLayout')}layoutFluid{else}layoutFixed{/if} clearfix">
				{hascontent}
					<ul class="userPanelItems">
						{content}{event name='topMenu'}{/content}
					</ul>
				{/hascontent}
				
				<!-- search area -->
				{event name='searchArea'}
				<!-- /search area -->
			</div>
		</nav>
		<!-- /top menu -->
		
		<!-- logo -->
		<div id="logo" class="logo">
			<a href="{link}{/link}">
				{if $__wcf->getStyleHandler()->getStyle()->getPageLogo()}
					<img src="{$__wcf->getStyleHandler()->getStyle()->getPageLogo()}" alt="" />
				{/if}
				{event name='headerLogo'}
			</a>
		</div>
		<!-- /logo -->
		
		{event name='headerContents'}
		
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
				<li id="toBottomLink"><a href="{$__wcf->getAnchor('bottom')}" title="{lang}wcf.global.scrollDown{/lang}" class="jsTooltip"><span class="icon icon16 icon-arrow-down"></span> <span class="invisible">{lang}wcf.global.scrollDown{/lang}</span></a></li>
				<li id="sitemap" class="javascriptOnly"><a title="{lang}wcf.sitemap.title{/lang}" class="jsTooltip"><span class="icon icon16 icon-sitemap"></span> <span class="invisible">{lang}wcf.sitemap.title{/lang}</span></a></li>
				{if $headerNavigation|isset}{@$headerNavigation}{/if}
				{event name='navigationIcons'}
			</ul>
		</nav>
		<!-- /navigation -->
	</div>
</header>
<!-- /HEADER -->

<!-- MAIN -->
<div id="main" class="{if $__wcf->getStyleHandler()->getStyle()->getVariable('useFluidLayout')}layoutFluid{else}layoutFixed{/if}{if $sidebarOrientation|isset && $sidebar|isset} sidebarOrientation{@$sidebarOrientation|ucfirst} clearfix{if $sidebarOrientation == 'right' && $sidebarCollapsed} sidebarCollapsed{/if}{/if}">
	<div>
		{if $sidebar|isset}
			<aside class="sidebar"{if $sidebarOrientation|isset && $sidebarOrientation == 'right'} data-is-open="{if $sidebarCollapsed}false{else}true{/if}" data-sidebar-name="{$sidebarName}"{/if}>
				<div>
					{event name='sidebarBoxesTop'}
					
					{@$sidebar}
					
					{event name='sidebarBoxesBottom'}
				</div>
			</aside>
			
			{if $sidebarOrientation|isset && $sidebarOrientation == 'right'}
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
		<section id="content" class="content clearfix">
		
			{event name='contents'}
			
			{if $skipBreadcrumbs|empty}{include file='breadcrumbs'}{/if}