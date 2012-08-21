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
			
			
			<iframe src="" class="content" id="content" style="padding-bottom: 170px; padding-left: 295px;">
				<!DOCTYPE html>
				<html lang="en">
					<head>
						<meta charset="utf-8" />
						<meta content="no-cache" http-equiv="cache-control" />
						<title>{PAGE_TITLE}</title>
						<link media="all" type="text/css" href="{@$__wcf->getPath('ultimate')}style/visualEditor.css" id="grid-iframe-css" rel="stylesheet">
						<style type="text/css"></style>
					</head><!-- /head -->

					<body class="visual-editor-iframe-grid">
						<div id="whitewrap">
							<div class="wrapper fixed-grid grid-active" id="wrapper-1">
								<div class="grid-grey" id="grid">
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
									<div class="grid-column grid-width-1"></div>
								</div>
								<div class="grid-container ui-grid">
				
								</div>
								<div id="grid-height-buttons">
									<span class="grid-height-adjustment tooltip wcf-badge" id="grid-height-decrease" title="{lang}ultimate.visualEditor.decreaseHeight{/lang}">-</span>
									<span class="grid-height-adjustment tooltip wcf-badge" id="grid-height-increase" title="{lang}ultimate.visualEditor.increaseHeight{/lang}">+</span>
								</div>
							</div>
						</div>
					</body>
				</html>
			</iframe>
			
			
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