{include file="documentHeader"}
<head>
	<title>{if $title|isset}{lang}{@$title}{/lang} - {/if}{lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' application='ultimate'}
</head>

<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{if $__boxSidebar|isset && $__boxSidebar}
	{capture assign='sidebar'}
		{@$__boxSidebar}
	{/capture}
{/if}
{if $sidebarOrientation|isset}
	{include file='header' application='ultimate' sidebarOrientation=''|concat:$sidebarOrientation}
{else}
	{include file='header' application='ultimate' sidebarOrientation='right'}
{/if}
			<!-- custom area -->
			<script data-relocate="true" type="text/javascript">
			/* <![CDATA[ */
			{* try without
			$(function() {
				var blockIDs = [{implode from=$blockIDs item=blockID glue=','}{$blockID}{/implode}];
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
							var height = $block.data('height');
							
							// this value is absolute
							$block.height(height);
						}
					}
				}
			});
			*}
			/* ]]> */
			</script>
			{@$customArea}
			<!-- /custom area -->
			
{include file='footer' application='ultimate'}

</body>
</html>
