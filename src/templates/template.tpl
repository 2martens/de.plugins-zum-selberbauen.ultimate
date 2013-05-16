{include file="documentHeader"}
<head>
	<title>{lang}{@$title}{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude'}
</head>

<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{if $sidebarOrientation|isset}
{include file='header' sidebarOrientation=''|concat:$sidebarOrientation sidebar=''|concat:$sidebar}
{else}
{include file='header'}
{/if}
			<!-- custom area -->
			<script type="text/javascript">
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
			
{include file='footer'}

</body>
</html>
