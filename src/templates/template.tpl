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
		{include file='header'}		
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
		<section id="content" class="content clearfix" style="position: relative;">
			
			{if $skipBreadcrumbs|empty}{include file='breadcrumbs'}{/if}
			<!-- custom area -->
			<script type="text/javascript">
			/* <![CDATA[ */
			$(function() {
				var blockIDs = [{implode from=$blockIDs item=blockID glue=','}{$blockID}{/implode}];
				var columns = 24;
				var $content = $('#content');
				refreshBlocks();
				
				// if the window is resized the blocks have to be updated
				$(window).bind('resize', function(event) {
					if ( event.target != window )
						return;
					refreshBlocks();
				});
				function refreshBlocks() {
					for (var $key = 0; $key < blockIDs.length; $key++) {
						var $blockID = blockIDs[$key];
						var $blockIDStr = 'block-' + $blockID;
						if ($.wcfIsset($blockIDStr)) {
							var $block = $('#' + $blockIDStr);
							var width = $block.data('width');
							var height = $block.data('height');
							var left = $block.data('left');
							var top = $block.data('top');
							// these values are absolute
							$block.height(height).top(top);
							
							// determine width and left
							var $width = width / columns;
							$width *= $content.width();
							var $left = left / columns;
							$left *= $content.width();
							// actually set the values
							$block.left($left).width($width);
						}
					}
				}
			});
			/* ]]> */
			</script>
			{@$customArea}
			<!-- /custom area -->
			
{include file='footer'}

</body>
</html>
