{include file="documentHeader"}
<head>
	<title>{lang}{@$title}{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude'}
</head>

<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
<a id="top"></a>
<!-- HEADER -->
<header id="pageHeader" class="layoutFluid">
	<div>
		<!-- top menu -->
		<nav id="topMenu" class="userPanel">
			<div class="layoutFluid clearfix">
				{hascontent}
					<ul class="userPanelItems">
						{content}{event name='topMenu'}{/content}
					</ul>
				{/hascontent}
				<!-- TODO: finish template -->
				<!-- search area -->
				{event name='searchArea'}
				<!-- /search area -->
			</div>
		</nav>
		<!-- /top menu -->
		
		{if $headerBlock}{include file='header'}{/if}
		{if $navigationBlock}{include file='navigation'}{/if}
		
	</div>
</header>
<!-- /HEADER -->

<!-- MAIN -->
<div id="main" class="layoutFluid{if $sidebarOrientation|isset && $sidebar|isset} sidebarOrientation{@$sidebarOrientation|ucfirst} clearfix{/if}">
	<div>
		{if $sidebar|isset}
			<aside class="sidebar">
				{@$sidebar}
			</aside>
		{/if}
			
		<!-- CONTENT -->
		<section id="content" class="content clearfix">
			
			<!-- custom area -->
			{@$customArea}
			<!-- /custom area -->
			
{include file='footer'}

</body>
</html>
