{include file="documentHeader"}
<head>
	<title>{lang}{@$title}{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude'}
	<script type="text/javascript">
	/* <![CDATA[ */
	$(function() {
		new ULTIMATE.VisualEditor();
	});
	/* ]]> */
	</script>
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
			{* implement iFrame, panels, etc. *}
			
			
			<iframe src="{link controller='VisualEditor'}visualEditorIFrame=true{/link}" class="content" id="content" style="padding-bottom: 170px; padding-left: 295px;"></iframe>
			
			
			<div id="blockTypePopup" class="ultimateHidden">
				<h4 id="blockTypePopupHeading">{lang}ultimate.visualEditor.selectBlockType{/lang}</h4>
				<ul>
					{foreach from=$blockTypes key=blockTypeID item=blockType}
						<li class="tooltip" id="block-type-{$blockType->cssIdentifier}">{$blockType->blockTypeName}</li>
					{/foreach}
				</ul>
			</div>
		</section>
		<!-- /CONTENT -->
	</div>
</div>
<!-- /MAIN -->

</body>
</html>